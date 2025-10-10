<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Expense;
use App\Models\Facility;
use App\Models\FacilityBookingLog;
use App\Models\FacilityBookingDetails;
use App\Models\FacilitySummary;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AccountingController extends Controller
{
    public function index()
    {
        return view('admin.accounting.index');
    }

    /**
     * Return aggregated income & expenses JSON for chart + table
     * Accepts optional query params:
     *  - period = daily|weekly|monthly (default monthly)
     *  - from & to = YYYY-MM-DD (optional)
     */
    public function monthlyIncomeApi(Request $request)
    {
        $period = $request->query('period', 'monthly');
        $from = $request->query('from');
        $to = $request->query('to');

        // label & key expressions depending on period
        if ($period === 'daily') {
            $labelPayments = "DATE_FORMAT(details.checkin_date, '%b %d %Y')";
            $keyPayments = "DATE(details.checkin_date)";
            $labelDayTour = "DATE_FORMAT(date_tour, '%b %d %Y')";
            $keyDayTour = "DATE(date_tour)";
            $labelOther = "DATE_FORMAT(created_at, '%b %d %Y')";
            $keyOther = "DATE(created_at)";
            $labelExpense = "DATE_FORMAT(expense_date, '%b %d %Y')";
            $keyExpense = "expense_date";
        } elseif ($period === 'weekly') {
            $labelPayments = "CONCAT('W', WEEK(details.checkin_date,1), ' ', YEAR(details.checkin_date))";
            $keyPayments = "YEARWEEK(details.checkin_date,1)";
            $labelDayTour = "CONCAT('W', WEEK(date_tour,1), ' ', YEAR(date_tour))";
            $keyDayTour = "YEARWEEK(date_tour,1)";
            $labelOther = "CONCAT('W', WEEK(created_at,1), ' ', YEAR(created_at))";
            $keyOther = "YEARWEEK(created_at,1)";
            $labelExpense = "CONCAT('W', WEEK(expense_date,1), ' ', YEAR(expense_date))";
            $keyExpense = "YEARWEEK(expense_date,1)";
        } else {
            // monthly (default)
            $labelPayments = "DATE_FORMAT(details.checkin_date, '%M %Y')";
            $keyPayments = "DATE_FORMAT(details.checkin_date, '%Y-%m')";
            $labelDayTour = "DATE_FORMAT(date_tour, '%M %Y')";
            $keyDayTour = "DATE_FORMAT(date_tour, '%Y-%m')";
            $labelOther = "DATE_FORMAT(created_at, '%M %Y')";
            $keyOther = "DATE_FORMAT(created_at, '%Y-%m')";
            $labelExpense = "DATE_FORMAT(expense_date, '%M %Y')";
            $keyExpense = "DATE_FORMAT(expense_date, '%Y-%m')";
        }

        // FIXED: ROOM income using the accurate payment logic from RoomBookReportController
        $roomQ = $this->getRoomIncomeByPeriod($period, $from, $to, $labelPayments, $keyPayments);

        // FIXED: DAY TOUR income using the accurate logic from DayTourEarningsController
        $dayTourQ = $this->getDayTourIncomeByPeriod($period, $from, $to, $labelDayTour, $keyDayTour);

        // EXPENSES (unchanged)
        $expenseQ = collect();
        if (DB::getSchemaBuilder()->hasTable('expenses')) {
            $expenseQ = DB::table('expenses')
                ->select(
                    DB::raw("{$labelExpense} as label"),
                    DB::raw("{$keyExpense} as period_key"),
                    DB::raw('SUM(amount) as total_expense')
                )
                ->when($from && $to, fn($q) => $q->whereBetween('expense_date', [$from, $to]))
                ->groupBy('period_key', 'label')
                ->orderBy('period_key')
                ->get()
                ->map(fn($r) => ['label' => $r->label, 'expense' => (float)$r->total_expense]);
        }

        // Combine labels from all sources
        $allLabels = collect(array_merge(
            $roomQ->pluck('label')->toArray(),
            $dayTourQ->pluck('label')->toArray(),
            $expenseQ->pluck('label')->toArray()
        ))->unique()->values()->toArray();

        $combined = [];
        foreach ($allLabels as $label) {
            $room = $roomQ->firstWhere('label', $label)['income'] ?? 0;
            $daytour = $dayTourQ->firstWhere('label', $label)['income'] ?? 0;
            $expense = $expenseQ->firstWhere('label', $label)['expense'] ?? 0;
            $income = $room + $daytour;
            $net = $income - $expense;
            $combined[] = [
                'label' => $label,
                'room' => (float)$room,
                'daytour' => (float)$daytour,
                'expense' => (float)$expense,
                'income' => (float)$income,
                'net' => (float)$net,
            ];
        }

        $totalIncome = collect($combined)->sum('income');
        $totalExpense = collect($combined)->sum('expense');
        $netTotal = $totalIncome - $totalExpense;
        $average = count($combined) ? $netTotal / count($combined) : 0;
        $best = collect($combined)->sortByDesc('income')->first();
        $bestPeriod = ['label' => $best['label'] ?? 'N/A', 'income' => $best['income'] ?? 0];

        $chartData = [
            'labels' => array_column($combined, 'label'),
            'datasets' => [
                ['label' => 'Room Income', 'data' => array_column($combined, 'room')],
                ['label' => 'Day Tour Income', 'data' => array_column($combined, 'daytour')],
                ['label' => 'Expenses', 'data' => array_column($combined, 'expense')],
            ]
        ];

        return response()->json([
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netTotal' => $netTotal,
            'average' => $average,
            'best' => $bestPeriod,
            'combined' => $combined,
            'chartData' => $chartData,
        ]);
    }

/**
 * FIXED: Get room income using the accurate payment logic from RoomBookReportController
 */
private function getRoomIncomeByPeriod($period, $from, $to, $labelExpression, $keyExpression)
{
    try {
        // Get all completed bookings with payment details using Eloquent (same as RoomBookReportController)
        $bookings = FacilityBookingLog::with(['payments', 'summaries.facility', 'details'])
            ->whereHas('summaries.facility', function ($query) {
                $query->where('type', 'room');
            })
            ->where('status', '!=', 'pending_confirmation')
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereHas('details', function ($q) use ($from, $to) {
                    $q->whereBetween('checkin_date', [$from, $to]);
                });
            })
            ->get();

        $periodEarnings = [];

        foreach ($bookings as $booking) {
            // Get checkin date from details for period grouping
            $checkinDate = $booking->details->first()->checkin_date ?? null;
            if (!$checkinDate) {
                continue;
            }

            // Determine period key and label based on checkin date
            $periodKey = $this->getPeriodKey($checkinDate, $period);
            $label = $this->getPeriodLabel($checkinDate, $period);

            // Calculate total booking price and payments
            $totalBookingPrice = $booking->details->sum('total_price');
            $totalAmountPaid = $booking->payments->sum('amount');
            $totalCheckinPaid = $booking->payments->sum('checkin_paid');

            // Calculate earnings for EACH ROOM in this booking (same logic as RoomBookReportController)
            foreach ($booking->summaries as $summary) {
                if ($summary->facility->type !== 'room') {
                    continue;
                }

                // Calculate per-room prices (same as RoomBookReportController)
                $perRoomFacilityPrice = $summary->facility_price;
                $perRoomBreakfastPrice = $summary->breakfast_price;
                $roomBasePrice = $perRoomFacilityPrice + $perRoomBreakfastPrice;

                // Apply the SAME payment logic as RoomBookReportController
                $roomEarnings = $this->applyPaymentLogicPerRoom(
                    $roomBasePrice,
                    $totalBookingPrice,
                    $totalAmountPaid,
                    $totalCheckinPaid
                );

                // Accumulate earnings by period
                if (!isset($periodEarnings[$periodKey])) {
                    $periodEarnings[$periodKey] = [
                        'label' => $label,
                        'income' => 0
                    ];
                }

                $periodEarnings[$periodKey]['income'] += $roomEarnings;
            }
        }

        \Log::info('Room Income Calculation Complete', [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'total_periods' => count($periodEarnings),
            'total_income' => collect($periodEarnings)->sum('income')
        ]);

        return collect($periodEarnings)->values();

    } catch (\Exception $e) {
        \Log::error('Error in getRoomIncomeByPeriod: ' . $e->getMessage());
        return collect();
    }
}

/**
 * Helper method to get period key based on date and period type
 */
private function getPeriodKey($date, $period)
{
    $carbonDate = Carbon::parse($date);
    
    switch ($period) {
        case 'daily':
            return $carbonDate->format('Y-m-d');
        case 'weekly':
            return $carbonDate->format('Y-W');
        default: // monthly
            return $carbonDate->format('Y-m');
    }
}

/**
 * Helper method to get period label based on date and period type
 */
private function getPeriodLabel($date, $period)
{
    $carbonDate = Carbon::parse($date);
    
    switch ($period) {
        case 'daily':
            return $carbonDate->format('M d Y');
        case 'weekly':
            return 'W' . $carbonDate->week . ' ' . $carbonDate->year;
        default: // monthly
            return $carbonDate->format('F Y');
    }
}

    /**
     * FIXED: Get day tour income using the accurate logic from DayTourEarningsController
     */
    private function getDayTourIncomeByPeriod($period, $from, $to, $labelExpression, $keyExpression)
    {
        try {
            $query = DB::table('day_tour_log_details as dt')
                ->select(
                    DB::raw("{$labelExpression} as label"),
                    DB::raw("{$keyExpression} as period_key"),
                    DB::raw('SUM(dt.total_price) as total_income')
                )
                ->where('dt.reservation_status', 'paid');

            // Apply date filter
            if ($from && $to) {
                $query->whereBetween('dt.date_tour', [$from, $to]);
            }

            // Apply the same filtering logic as DayTourEarningsController
            // This ensures we count the same bookings in both reports
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNull('bgd.facility_id')
                    ->where('bgd.quantity', '>', 0);
            });

            $results = $query->groupBy('period_key', 'label')
                ->orderBy('period_key')
                ->get()
                ->map(fn($r) => ['label' => $r->label, 'income' => (float)$r->total_income]);

            \Log::info('Day Tour Income Calculation', [
                'period' => $period,
                'from' => $from,
                'to' => $to,
                'results_count' => $results->count(),
                'total_income' => $results->sum('income')
            ]);

            return $results;

        } catch (\Exception $e) {
            \Log::error('Error in getDayTourIncomeByPeriod: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Alternative: More detailed day tour income calculation that matches your analytics
     */
    private function getDayTourIncomeByPeriodDetailed($period, $from, $to, $labelExpression, $keyExpression)
    {
        try {
            // This matches the logic in DayTourEarningsController::getRevenueData
            $query = DB::table('day_tour_log_details as dt')
                ->select(
                    DB::raw("{$labelExpression} as label"),
                    DB::raw("{$keyExpression} as period_key"),
                    DB::raw('SUM(dt.total_price) as total_income')
                )
                ->where('dt.reservation_status', 'paid');

            // Apply date filter
            if ($from && $to) {
                $query->whereBetween('dt.date_tour', [$from, $to]);
            }

            // Ensure we only count bookings with actual guests (same as your analytics)
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNull('bgd.facility_id')
                    ->where('bgd.quantity', '>', 0);
            });

            $results = $query->groupBy('period_key', 'label')
                ->orderBy('period_key')
                ->get();

            \Log::info('Detailed Day Tour Income', [
                'period' => $period,
                'results' => $results->toArray()
            ]);

            return $results->map(fn($r) => ['label' => $r->label, 'income' => (float)$r->total_income]);

        } catch (\Exception $e) {
            \Log::error('Error in getDayTourIncomeByPeriodDetailed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * EXACT COPY from RoomBookReportController - This ensures consistent payment logic
     */
    private function applyPaymentLogicPerRoom($roomBasePrice, $bookingTotalPrice, $totalAmountPaid, $totalCheckinPaid)
    {
        if ($totalAmountPaid == 0) {
            return 0;
        }

        $totalPaymentsReceived = $totalAmountPaid + $totalCheckinPaid;

        \Log::info('💳 Payment Rules - Accounting', [
            'room_base_price' => $roomBasePrice,
            'booking_total_price' => $bookingTotalPrice,
            'amount_paid' => $totalAmountPaid,
            'checkin_paid' => $totalCheckinPaid,
            'total_payments' => $totalPaymentsReceived
        ]);

        // Full payment
        if ($totalPaymentsReceived >= $bookingTotalPrice) {
            \Log::info('✅ Full payment - room earns full price');
            return $roomBasePrice;
        }

        // Half payment
        if ($totalAmountPaid == ($bookingTotalPrice / 2) && $totalCheckinPaid == 0) {
            \Log::info('✅ Half payment - room earns half price');
            return $roomBasePrice / 2;
        }

        \Log::warning('❌ Payment amount does not match full or half price');
        return 0;
    }

    /**
     * Top performers (rooms/day tours/events) - UPDATED with accurate day tour logic
     */
    public function topPerformersApi()
    {
        // FIXED: Top Rooms using accurate payment logic
        $topRooms = $this->getTopRooms();

        // FIXED: Top Day Tours using same logic as DayTourEarningsController
        $topDayTours = $this->getTopDayTours();


        return response()->json([
            'rooms' => $topRooms,
            'daytours' => $topDayTours,
        ]);
    }

    /**
 * FIXED: Get top rooms using accurate payment logic (matching RoomBookReportController)
 */
private function getTopRooms()
{
    try {
        $rooms = Facility::where('type', 'room')->get();
        $roomEarnings = [];

        foreach ($rooms as $room) {
            // Use the same calculation logic as RoomBookReportController
            $earnings = $this->calculateRoomEarningsForAccounting($room->id);
            
            if ($earnings > 0) {
                $roomEarnings[] = [
                    'facility' => $room->name,
                    'total_income' => $earnings
                ];
            }
        }

        // Sort by earnings descending and limit to top 10
        usort($roomEarnings, function ($a, $b) {
            return $b['total_income'] <=> $a['total_income'];
        });

        return collect(array_slice($roomEarnings, 0, 10));

    } catch (\Exception $e) {
        \Log::error('Error in getTopRooms: ' . $e->getMessage());
        return collect();
    }
}

/**
 * Calculate room earnings for accounting (same logic as RoomBookReportController)
 */
private function calculateRoomEarningsForAccounting($roomId)
{
    try {
        $bookings = FacilityBookingLog::with(['payments', 'summaries', 'details'])
            ->whereHas('summaries', function ($query) use ($roomId) {
                $query->where('facility_id', $roomId);
            })
            ->where('status', '!=', 'pending_confirmation')
            ->get();

        $totalEarnings = 0;

        foreach ($bookings as $booking) {
            // Calculate total booking price
            $totalBookingPrice = $booking->details->sum('total_price');

            // Get payment totals
            $totalAmountPaid = $booking->payments->sum('amount');
            $totalCheckinPaid = $booking->payments->sum('checkin_paid');

            // Get the specific facility summary for this room
            $summary = $booking->summaries->where('facility_id', $roomId)->first();
            if (!$summary) {
                continue;
            }

            // Calculate per-room prices
            $perRoomFacilityPrice = $summary->facility_price;
            $perRoomBreakfastPrice = $summary->breakfast_price;
            $roomBasePrice = $perRoomFacilityPrice + $perRoomBreakfastPrice;

            // Apply the SAME payment logic
            $roomEarnings = $this->applyPaymentLogicPerRoom(
                $roomBasePrice,
                $totalBookingPrice,
                $totalAmountPaid,
                $totalCheckinPaid
            );

            $totalEarnings += $roomEarnings;
        }

        return $totalEarnings;

    } catch (\Exception $e) {
        \Log::error("Error calculating room earnings for accounting (room $roomId): " . $e->getMessage());
        return 0;
    }
}

    /**
     * Get top day tours using same logic as DayTourEarningsController
     */
    private function getTopDayTours()
    {
        try {
            return DB::table('day_tour_log_details as dt')
                ->join('booking_guest_details as bgd', 'dt.id', '=', 'bgd.day_tour_log_details_id')
                ->join('facilities', 'bgd.facility_id', '=', 'facilities.id')
                ->where('dt.reservation_status', 'paid')
                ->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('booking_guest_details as bgd2')
                        ->join('guest_type as gt', 'bgd2.guest_type_id', '=', 'gt.id')
                        ->whereColumn('bgd2.day_tour_log_details_id', 'dt.id')
                        ->whereNull('bgd2.facility_id')
                        ->where('bgd2.quantity', '>', 0);
                })
                ->select(
                    'facilities.name as facility', 
                    DB::raw('SUM(COALESCE(dt.total_price,0)) as total_income')
                )
                ->groupBy('facilities.name')
                ->orderByDesc('total_income')
                ->limit(10)
                ->get();

        } catch (\Exception $e) {
            \Log::error('Error in getTopDayTours: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * CSV export of combined report
     */
    public function export(Request $request)
    {
        $data = $this->monthlyIncomeApi($request)->getData(true);
        $combined = $data['combined'] ?? [];

        $response = new StreamedResponse(function() use ($combined) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Period','Room','Day Tour','Total Income','Expenses','Net']);
            foreach ($combined as $row) {
                fputcsv($handle, [
                    $row['label'] ?? '',
                    $row['room'] ?? 0,
                    $row['daytour'] ?? 0,
                    $row['income'] ?? 0,
                    $row['expense'] ?? 0,
                    $row['net'] ?? 0,
                ]);
            }
            fclose($handle);
        });

        $filename = 'accounting_report_' . now()->format('Y_m_d_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    /**
     * PDF export (requires barryvdh/laravel-dompdf)
     */
    public function exportPdf(Request $request)
    {
        $data = $this->monthlyIncomeApi($request)->getData(true);
        $combined = $data['combined'] ?? [];
        $summary = [
            'totalIncome' => $data['totalIncome'] ?? 0,
            'totalExpense' => $data['totalExpense'] ?? 0,
            'netTotal' => $data['netTotal'] ?? 0,
        ];

        if (!class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            // fallback to CSV if PDF lib not available
            return $this->export($request);
        }

        $pdf = \PDF::loadView('admin.accounting.report_pdf', [
            'combined' => $combined,
            'summary' => $summary,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('accounting_report_' . now()->format('Y_m_d_His') . '.pdf');
    }
}