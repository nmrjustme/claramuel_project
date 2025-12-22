<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Payments;
use App\Models\FacilityBookingLog;
use App\Models\FacilityBookingDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RoomBookReportController extends Controller
{
    public function index()
    {
        $categories = Facility::where('type', 'room')->distinct()->pluck('category');
        return view('admin.room_earnings_report.index', compact('categories'));
    }

    // --- 1. MAIN EARNINGS DATA ---
    public function getEarningsData(Request $request)
    {
        $dates = $this->getDateRange($request);
        $category = $request->category;

        $roomsQuery = Facility::where('type', 'room');
        if ($category)
            $roomsQuery->where('category', $category);
        $rooms = $roomsQuery->get();

        $earningsData = [];
        $labels = [];
        $roomDetails = [];
        $totalEarnings = 0;
        $roomsBooked = 0;
        $totalBookings = 0;

        // Arrays to calculate best category on the fly
        $categoryTotals = [];

        foreach ($rooms as $room) {
            $e = $this->calculateRoomEarnings($room->id, $dates['start'], $dates['end']);
            $b = $this->calculateRoomBookings($room->id, $dates['start'], $dates['end']);

            $earningsData[] = $e;
            $labels[] = $room->name;
            $totalEarnings += $e;
            $totalBookings += $b;
            if ($e > 0)
                $roomsBooked++;

            // Accumulate category earnings
            if (!isset($categoryTotals[$room->category])) {
                $categoryTotals[$room->category] = 0;
            }
            $categoryTotals[$room->category] += $e;

            $roomDetails[] = [
                'name' => $room->name,
                'category' => $room->category,
                'earnings' => $e,
                'bookings' => $b,
                'occupancy' => $this->calculateRoomOccupancy($room->id, $dates['start'], $dates['end']),
                'adr' => $b > 0 ? round($e / $b, 2) : 0
            ];
        }

        // Determine Top Category
        $topCategory = '-';
        if (!empty($categoryTotals)) {
            $topCategory = array_keys($categoryTotals, max($categoryTotals))[0];
        }

        // Sort room details by earnings
        usort($roomDetails, fn($a, $b) => $b['earnings'] <=> $a['earnings']);

        // Calculate Global Stats
        $globalOccupancy = $this->calculateGlobalOccupancy($dates['start'], $dates['end'], $category);
        $totalRefunds = $this->calculateTotalRefundsForPeriod($dates['start'], $dates['end'], $category);

        return response()->json([
            'success' => true,
            'earnings' => $earningsData,
            'labels' => $labels,
            'rooms' => $roomDetails,
            'stats' => [
                'totalEarnings' => $totalEarnings,
                'roomsBooked' => $roomsBooked,
                'totalBookings' => $totalBookings,
                'occupancyRate' => $globalOccupancy,
                'topCategory' => $topCategory, // Include Best Category
                'totalRefunds' => $totalRefunds
            ]
        ]);
    }

    // --- 2. CANCELLATION & REFUND DATA ---
    public function getCancellationRefundData(Request $request)
    {
        $dates = $this->getDateRange($request);
        $cat = $request->category;

        $refundQuery = Payments::query()
            ->join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
            ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
            ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
            ->where('facilities.type', 'room')
            ->where('payments.refund_amount', '>', 0)
            ->whereBetween('payments.refund_date', [$dates['start'], $dates['end']]);

        if ($cat)
            $refundQuery->where('facilities.category', $cat);

        $totalRefunds = (clone $refundQuery)->sum('payments.refund_amount');
        $fullRefunds = (clone $refundQuery)->where('payments.refund_type', 'full')->count();
        $partialRefunds = (clone $refundQuery)->where(function ($q) {
            $q->where('payments.refund_type', 'half')->orWhere('payments.refund_type', 'partial');
        })->count();

        $cancelledBookings = FacilityBookingLog::where('status', 'cancelled')
            ->whereHas('summaries.facility', function ($q) use ($cat) {
                $q->where('type', 'room');
                if ($cat)
                    $q->where('category', $cat);
            })
            ->whereHas('details', fn($q) => $q->whereBetween('checkin_date', [$dates['start'], $dates['end']]))
            ->count();

        $reasons = (clone $refundQuery)
            ->select('payments.refund_reason', DB::raw('count(*) as total'))
            ->groupBy('payments.refund_reason')
            ->pluck('total', 'payments.refund_reason');

        $trends = $this->getRefundTrendsData($dates['start'], $dates['end'], $cat);

        $recent = (clone $refundQuery)
            ->select('log.code', 'facilities.name as room', 'payments.*')
            ->latest('payments.refund_date')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                return [
                    'booking_id' => $p->code,
                    'room_name' => $p->room,
                    'refund_date' => Carbon::parse($p->refund_date)->format('M d'),
                    'amount' => $p->refund_amount,
                    'type' => ucfirst($p->refund_type),
                    'reason' => $p->refund_reason
                ];
            });

        return response()->json([
            'success' => true,
            'stats' => [
                'cancelledBookings' => $cancelledBookings,
                'totalRefunds' => $totalRefunds,
                'fullRefunds' => $fullRefunds,
                'partialRefunds' => $partialRefunds
            ],
            'cancellationReasons' => $reasons,
            'refundTrends' => $trends,
            'recentRefunds' => $recent
        ]);
    }

    // --- 3. COMPARISON & CATEGORY ---
    public function getComparisonData(Request $request)
    {
        $cat = $request->category;
        $currentYear = now()->year;

        if ($request->period == 'yearly' && $request->filter_value) {
            $currentYear = $request->filter_value;
        } elseif ($request->period == 'monthly' && $request->filter_value) {
            $currentYear = Carbon::parse($request->filter_value)->year;
        }

        $data = [];
        for ($i = 0; $i < 2; $i++) {
            $y = $currentYear - $i;
            $data[$y] = $this->getYearlyMonthlyData($y, $cat);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        ]);
    }

    public function getCategoryEarnings(Request $request)
    {
        $dates = $this->getDateRange($request);
        $categories = Facility::where('type', 'room')->distinct()->pluck('category');

        $data = [];
        foreach ($categories as $cat) {
            $sum = 0;
            $rooms = Facility::where('type', 'room')->where('category', $cat)->pluck('id');
            foreach ($rooms as $rid)
                $sum += $this->calculateRoomEarnings($rid, $dates['start'], $dates['end']);
            if ($sum > 0)
                $data[$cat] = $sum;
        }

        return response()->json(['categoryEarnings' => $data]);
    }

    // --- EXPORTS ---

    public function exportEarningsCsv(Request $request)
    {
        $data = $this->prepareExportData($request);
        $filename = 'room_report_' . now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($data, $request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['ROOM EARNINGS REPORT']);
            fputcsv($handle, ['Filter', ucfirst($request->period) . ': ' . $request->filter_value]);
            fputcsv($handle, ['Generated', now()->toDateTimeString()]);
            fputcsv($handle, []);

            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Total Revenue', number_format($data['totalEarnings'], 2)]);
            fputcsv($handle, ['Total Bookings', $data['totalBookings']]);
            fputcsv($handle, ['Total Refunds', number_format($data['totalRefunds'], 2)]);
            fputcsv($handle, ['Avg Occupancy', $data['avgOccupancy'] . '%']);
            fputcsv($handle, ['Best Category', $data['topCategory']]); // Added to CSV
            fputcsv($handle, []);

            fputcsv($handle, ['ROOM BREAKDOWN']);
            fputcsv($handle, ['Room Name', 'Category', 'Revenue', 'Bookings', 'Occupancy %', 'Avg Rate']);
            foreach ($data['rows'] as $row) {
                fputcsv($handle, [
                    $row['name'],
                    $row['category'],
                    $row['earnings'],
                    $row['bookings'],
                    $row['occupancy'] . '%',
                    $row['adr']
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
        return $response;
    }

    public function exportEarningsPdf(Request $request)
    {
        $data = $this->prepareExportData($request);

        $pdf = Pdf::loadView('admin.room_earnings_report.pdf', [
            'data' => $data,
            'filter' => ucfirst($request->period) . ': ' . $request->filter_value,
            'generatedAt' => now()
        ]);

        return $pdf->download('room_report.pdf');
    }

    // --- HELPER CALCULATIONS ---

    private function prepareExportData(Request $request)
    {
        $req = new Request($request->all());
        $jsonData = $this->getEarningsData($req)->getData(true);
        $stats = $jsonData['stats'];

        return [
            'rows' => $jsonData['rooms'],
            'totalEarnings' => $stats['totalEarnings'],
            'totalBookings' => $stats['totalBookings'],
            'totalRefunds' => $stats['totalRefunds'],
            'avgOccupancy' => $stats['occupancyRate'],
            'topCategory' => $stats['topCategory'] // Passed for export
        ];
    }

    private function getDateRange(Request $request)
    {
        $period = $request->period ?? 'monthly';
        $val = $request->filter_value;

        try {
            if ($period == 'daily' && $val) {
                return ['start' => Carbon::parse($val)->startOfDay(), 'end' => Carbon::parse($val)->endOfDay()];
            }
            if ($period == 'monthly' && $val) {
                return ['start' => Carbon::parse($val)->startOfMonth(), 'end' => Carbon::parse($val)->endOfMonth()];
            }
            if ($period == 'yearly' && $val) {
                $dt = Carbon::createFromFormat('Y', $val);
                return ['start' => $dt->copy()->startOfYear(), 'end' => $dt->copy()->endOfYear()];
            }
        } catch (\Exception $e) {
        }

        return ['start' => now()->startOfMonth(), 'end' => now()->endOfMonth()];
    }

    private function calculateTotalRefundsForPeriod($start, $end, $category = null)
    {
        $query = Payments::query()
            ->join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
            ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
            ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
            ->where('facilities.type', 'room')
            ->where('payments.refund_amount', '>', 0)
            ->whereBetween('payments.refund_date', [$start, $end]);

        if ($category) {
            $query->where('facilities.category', $category);
        }

        return $query->sum('payments.refund_amount');
    }

    private function calculateRoomEarnings($roomId, $start, $end)
    {
        $logs = FacilityBookingLog::whereHas('summaries', fn($q) => $q->where('facility_id', $roomId))
            ->whereHas('details', fn($q) => $q->whereBetween('checkin_date', [$start, $end]))

            // ✅ ADD THIS BLOCK
            ->where(function ($q) {
                $q->where('status', '!=', 'cancelled')
                    ->orWhere(function ($q) {
                        $q->where('status', 'cancelled')
                            ->whereDoesntHave('payments', fn($q) => $q->whereNotNull('refund_amount'));
                    });
            })
            // ✅ END ADD

            ->with(['payments', 'summaries', 'details'])
            ->get();

        $earnings = 0;

        foreach ($logs as $log) {
            $summary = $log->summaries->firstWhere('facility_id', $roomId);
            if (!$summary)
                continue;

            $base = $summary->facility_price + $summary->breakfast_price;
            $paid = $log->payments->sum('amount') + $log->payments->sum('checkin_paid');
            $total = $log->details->sum('total_price');

            $contrib = 0;
            if ($total > 0 && $paid >= $total - 1) {
                $contrib = $base;
            } elseif ($total > 0 && abs($paid - ($total / 2)) < 5) {
                $contrib = $base / 2;
            }

            $refunds = $log->payments->where('refund_amount', '>', 0)->sum('refund_amount');
            if ($refunds > 0 && $total > 0) {
                $ratio = $base / $total;
                $contrib -= ($refunds * $ratio);
            }

            $earnings += max(0, $contrib);
        }

        return $earnings;
    }


    private function calculateRoomBookings($id, $s, $e)
    {
        return FacilityBookingLog::whereHas('summaries', fn($q) => $q->where('facility_id', $id))
            ->whereHas('details', fn($q) => $q->whereBetween('checkin_date', [$s, $e]))

            // ✅ ADD THIS BLOCK
            ->where(function ($q) {
                $q->where('status', '!=', 'cancelled')
                    ->orWhere(function ($q) {
                        $q->where('status', 'cancelled')
                            ->whereDoesntHave('payments', fn($q) => $q->whereNotNull('refund_amount'));
                    });
            })
            // ✅ END ADD

            ->count();
    }

    private function calculateRoomOccupancy($id, $s, $e)
    {
        $details = FacilityBookingDetails::whereHas('bookingLog', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->whereHas('facilitySummary', fn($q) => $q->where('facility_id', $id))
            ->whereBetween('checkin_date', [$s, $e])->get();

        $booked = 0;
        foreach ($details as $d) {
            $checkin = Carbon::parse($d->checkin_date);
            $checkout = $d->checkout_date ? Carbon::parse($d->checkout_date) : $checkin->copy()->addDay();
            $booked += $checkin->diffInDays($checkout);
        }

        $total = $s->diffInDays($e) + 1;
        return $total > 0 ? round(($booked / $total) * 100, 1) : 0;
    }

    private function calculateGlobalOccupancy($s, $e, $cat)
    {
        $rooms = Facility::where('type', 'room')->when($cat, fn($q) => $q->where('category', $cat))->get();
        if ($rooms->isEmpty())
            return 0;
        $totalOcc = 0;
        foreach ($rooms as $r)
            $totalOcc += $this->calculateRoomOccupancy($r->id, $s, $e);
        return round($totalOcc / $rooms->count(), 1);
    }

    private function getYearlyMonthlyData($y, $c)
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $s = Carbon::create($y, $m, 1)->startOfMonth();
            $e = $s->copy()->endOfMonth();
            $rooms = Facility::where('type', 'room')->when($c, fn($q) => $q->where('category', $c))->get();
            $sum = 0;
            foreach ($rooms as $r)
                $sum += $this->calculateRoomEarnings($r->id, $s, $e);
            $data[] = $sum;
        }
        return $data;
    }

    private function getRefundTrendsData($startDate, $endDate, $category)
    {
        $diffDays = $startDate->diffInDays($endDate);
        $groupBy = $diffDays > 32 ? 'month' : 'day';

        $query = Payments::query()
            ->join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
            ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
            ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
            ->where('facilities.type', 'room')
            ->where('payments.refund_amount', '>', 0)
            ->whereBetween('payments.refund_date', [$startDate, $endDate]);

        if ($category)
            $query->where('facilities.category', $category);

        if ($groupBy === 'day') {
            $data = $query->select(DB::raw('DATE(payments.refund_date) as date'), DB::raw('SUM(payments.refund_amount) as total'))
                ->groupBy('date')->orderBy('date')->get();
            $chartData = [];
            $labels = [];
            $curr = $startDate->copy();
            while ($curr <= $endDate) {
                $d = $curr->format('Y-m-d');
                $labels[] = $curr->format('M d');
                $found = $data->firstWhere('date', $d);
                $chartData[] = $found ? $found->total : 0;
                $curr->addDay();
            }
        } else {
            $data = $query->select(
                DB::raw("DATE_FORMAT(payments.refund_date, '%Y-%m') as ym"),
                DB::raw("DATE_FORMAT(payments.refund_date, '%b %Y') as label"),
                DB::raw('SUM(payments.refund_amount) as total')
            )->groupBy('ym', 'label')->orderBy('ym')->get();
            $labels = $data->pluck('label')->toArray();
            $chartData = $data->pluck('total')->toArray();
        }
        return ['labels' => $labels, 'data' => $chartData];
    }
}