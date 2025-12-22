<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Expense;
use App\Models\Facility;
use App\Models\FacilityBookingLog;
use App\Models\Payments; // Ensure this model is imported
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DayTourLogDetails;
use Illuminate\Support\Str;

class AccountingController extends Controller
{
    public function index()
    {
        return view('admin.accounting.index');
    }

    /**
     * API: Aggregated income & expenses based on filter choice
     * Params: 
     * - period: 'daily', 'monthly', 'yearly'
     * - filter_value: 'YYYY-MM-DD', 'YYYY-MM', or 'YYYY'
     */
    public function monthlyIncomeApi(Request $request)
    {
        $periodType = $request->query('period', 'monthly');
        $filterValue = $request->query('filter_value');

        // 1. Determine Date Range ($from, $to) and SQL Grouping Mode
        $range = $this->calculateDateRange($periodType, $filterValue);
        $from = $range['from'];
        $to = $range['to'];
        $groupingMode = $range['groupingMode']; // 'daily_single', 'daily_breakdown', or 'monthly_breakdown'

        // 2. Fetch Room Income (with refunds deducted)
        $roomQ = $this->getRoomIncomeByPeriod($groupingMode, $from, $to);

        // 3. Fetch Day Tour Income
        $dayTourQ = $this->getDayTourIncomeByPeriod($groupingMode, $from, $to);

        // 4. Fetch Expenses
        $expenseQ = collect();
        if (DB::getSchemaBuilder()->hasTable('expenses')) {
            $expenseQ = DB::table('expenses')
                ->select(
                    DB::raw($this->getLabelExpression('expense_date', $groupingMode) . " as label"),
                    DB::raw($this->getKeyExpression('expense_date', $groupingMode) . " as period_key"),
                    DB::raw('SUM(amount) as total_expense')
                )
                ->whereBetween('expense_date', [$from, $to])
                ->groupBy('period_key', 'label')
                ->orderBy('period_key')
                ->get()
                ->map(fn($r) => ['label' => $r->label, 'expense' => (float) $r->total_expense]);
        }

        // 5. Combine Data Sources
        $allLabels = collect(array_merge(
            $roomQ->pluck('label')->toArray(),
            $dayTourQ->pluck('label')->toArray(),
            $expenseQ->pluck('label')->toArray()
        ))->unique()->values()->toArray();

        // Optional: Sort labels chronologically if needed (based on period_key logic)

        $combined = [];
        foreach ($allLabels as $label) {
            $room = $roomQ->firstWhere('label', $label)['income'] ?? 0;
            $daytour = $dayTourQ->firstWhere('label', $label)['income'] ?? 0;
            $expense = $expenseQ->firstWhere('label', $label)['expense'] ?? 0;

            // Note: Total income is Room + Daytour
            $income = $room + $daytour;
            $net = $income - $expense;

            $combined[] = [
                'label' => $label,
                'room' => (float) $room,
                'daytour' => (float) $daytour,
                'expense' => (float) $expense,
                'income' => (float) $income,
                'net' => (float) $net,
            ];
        }

        // Handle case where specific day has no data
        if ($periodType === 'daily' && empty($combined)) {
            $dateLabel = $from ? Carbon::parse($from)->format('M d Y') : 'Selected Date';
            $combined[] = [
                'label' => $dateLabel,
                'room' => 0,
                'daytour' => 0,
                'expense' => 0,
                'income' => 0,
                'net' => 0
            ];
        }

        // 6. Calculate Summaries
        $totalIncome = collect($combined)->sum('income');
        $totalExpense = collect($combined)->sum('expense');
        $netTotal = $totalIncome - $totalExpense;
        $average = count($combined) ? $netTotal / count($combined) : 0;
        $best = collect($combined)->sortByDesc('income')->first();
        $bestPeriod = ['label' => $best['label'] ?? 'N/A', 'income' => $best['income'] ?? 0];

        // 7. Format for Chart.js
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
     * Logic to interpret filter_value into start/end dates
     */
    /**
     * Logic to interpret filter_value into start/end dates
     */
    private function calculateDateRange($periodType, $filterValue)
    {
        // Defaults
        $from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = Carbon::now()->endOfMonth()->format('Y-m-d');
        $groupingMode = 'daily_breakdown';

        if (!$filterValue) {
            return compact('from', 'to', 'groupingMode');
        }

        try {
            if ($periodType === 'daily') {
                // User picked "2024-12-25". Range is exact day.
                $from = $filterValue;

                // --- FIX STARTS HERE ---
                // We must set $to to the end of that day (23:59:59)
                // Otherwise 'whereBetween' ignores everything after midnight
                $to = Carbon::parse($filterValue)->endOfDay()->toDateTimeString();
                // --- FIX ENDS HERE ---

                $groupingMode = 'daily_single';
            } elseif ($periodType === 'monthly') {
                // User picked "2024-12". Range is Dec 1 to Dec 31. Breakdown by Day.
                $date = Carbon::parse($filterValue);
                $from = $date->startOfMonth()->format('Y-m-d');
                $to = $date->endOfMonth()->format('Y-m-d');
                $groupingMode = 'daily_breakdown';
            } elseif ($periodType === 'yearly') {
                // User picked "2024". Range is Jan 1 to Dec 31. Breakdown by Month.
                $date = Carbon::createFromFormat('Y', $filterValue);
                $from = $date->startOfYear()->format('Y-m-d');
                $to = $date->endOfYear()->format('Y-m-d');
                $groupingMode = 'monthly_breakdown';
            }
        } catch (\Exception $e) {
            Log::error("Date calculation error: " . $e->getMessage());
        }

        return compact('from', 'to', 'groupingMode');
    }

    // --- HELPER FUNCTIONS FOR SQL GROUPING ---

    private function getLabelExpression($column, $grouping)
    {
        switch ($grouping) {
            case 'daily_single':
            case 'daily_breakdown':
                // Label: "Oct 25 2023"
                return "DATE_FORMAT($column, '%b %d %Y')";
            case 'monthly_breakdown':
                // Label: "October 2023"
                return "DATE_FORMAT($column, '%M %Y')";
            default:
                return "DATE_FORMAT($column, '%Y-%m-%d')";
        }
    }

    private function getKeyExpression($column, $grouping)
    {
        switch ($grouping) {
            case 'daily_single':
            case 'daily_breakdown':
                // Group by full date "2023-10-25"
                return "DATE($column)";
            case 'monthly_breakdown':
                // Group by Month "2023-10"
                return "DATE_FORMAT($column, '%Y-%m')";
            default:
                return "DATE($column)";
        }
    }

    private function getPeriodKey($date, $grouping)
    {
        $d = Carbon::parse($date);
        switch ($grouping) {
            case 'daily_single':
            case 'daily_breakdown':
                return $d->format('Y-m-d');
            case 'monthly_breakdown':
                return $d->format('Y-m');
            default:
                return $d->format('Y-m-d');
        }
    }

    private function getPeriodLabel($date, $grouping)
    {
        $d = Carbon::parse($date);
        switch ($grouping) {
            case 'daily_single':
            case 'daily_breakdown':
                return $d->format('M d Y');
            case 'monthly_breakdown':
                return $d->format('F Y');
            default:
                return $d->format('Y-m-d');
        }
    }

    // --- DATA FETCHING ---

    private function getRoomIncomeByPeriod($grouping, $from, $to)
    {
        try {
            $bookings = FacilityBookingLog::with(['payments', 'summaries.facility', 'details'])
                ->whereHas('summaries.facility', fn($q) => $q->where('type', 'room'))
                ->where('status', '!=', 'pending_confirmation')
                ->whereHas('details', fn($q) => $q->whereBetween('checkin_date', [$from, $to]))
                ->get();

            $periodEarnings = [];

            foreach ($bookings as $booking) {
                $checkinDate = $booking->details->first()->checkin_date ?? null;
                if (!$checkinDate)
                    continue;

                $periodKey = $this->getPeriodKey($checkinDate, $grouping);
                $label = $this->getPeriodLabel($checkinDate, $grouping);

                // Financial Calculations
                $totalBookingPrice = $booking->details->sum('total_price');
                $totalAmountPaid = $booking->payments->sum('amount');
                $totalCheckinPaid = $booking->payments->sum('checkin_paid');

                foreach ($booking->summaries as $summary) {
                    if ($summary->facility->type !== 'room')
                        continue;

                    $roomBasePrice = $summary->facility_price + $summary->breakfast_price;
                    $roomRefunds = $this->calculateRoomRefunds($booking, $summary->facility_id);

                    // Apply Payment Rules (Full vs Half vs None)
                    $roomEarnings = $this->applyPaymentLogicPerRoom(
                        $roomBasePrice,
                        $totalBookingPrice,
                        $totalAmountPaid,
                        $totalCheckinPaid
                    );

                    // Deduct Refunds
                    $roomNetEarnings = max(0, $roomEarnings - $roomRefunds);

                    if (!isset($periodEarnings[$periodKey])) {
                        $periodEarnings[$periodKey] = ['label' => $label, 'income' => 0];
                    }
                    $periodEarnings[$periodKey]['income'] += $roomNetEarnings;
                }
            }
            return collect($periodEarnings)->values();

        } catch (\Exception $e) {
            Log::error('Error in room income: ' . $e->getMessage());
            return collect();
        }
    }

    private function getDayTourIncomeByPeriod($grouping, $from, $to)
    {
        try {
            $query = DB::table('day_tour_log_details as dt')
                ->select(
                    DB::raw($this->getLabelExpression('dt.date_tour', $grouping) . " as label"),
                    DB::raw($this->getKeyExpression('dt.date_tour', $grouping) . " as period_key"),
                    DB::raw('SUM(dt.total_price) as total_income')
                )
                ->where('dt.reservation_status', 'paid')
                ->whereBetween('dt.date_tour', [$from, $to]);

            // Ensure it has valid guests
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNull('bgd.facility_id')
                    ->where('bgd.quantity', '>', 0);
            });

            return $query->groupBy('period_key', 'label')
                ->orderBy('period_key')
                ->get()
                ->map(fn($r) => ['label' => $r->label, 'income' => (float) $r->total_income]);

        } catch (\Exception $e) {
            Log::error('Error in day tour income: ' . $e->getMessage());
            return collect();
        }
    }

    // --- UTILITIES FOR FINANCIAL LOGIC ---


    private function calculateRoomRefunds($booking, $roomId)
    {
        try {
            $totalRefunds = 0;
            $refundPayments = $booking->payments->where('refund_amount', '>', 0);

            if ($refundPayments->isEmpty())
                return 0;

            $roomsInBooking = max($booking->summaries->count(), 1);
            $totalBookingPrice = $booking->details->sum('total_price');

            $roomSummary = $booking->summaries->where('facility_id', $roomId)->first();
            if (!$roomSummary)
                return 0;

            $roomBasePrice = $roomSummary->facility_price + $roomSummary->breakfast_price;
            $roomShare = $totalBookingPrice > 0 ? ($roomBasePrice / $totalBookingPrice) : (1 / $roomsInBooking);

            foreach ($refundPayments as $payment) {
                $totalRefunds += ($payment->refund_amount * $roomShare);
            }

            return $totalRefunds;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function applyPaymentLogicPerRoom($roomBasePrice, $bookingTotalPrice, $totalAmountPaid, $totalCheckinPaid)
    {
        if ($totalAmountPaid == 0)
            return 0;

        $totalPaymentsReceived = $totalAmountPaid + $totalCheckinPaid;

        // Rule 1: Full Payment
        if ($totalPaymentsReceived >= $bookingTotalPrice) {
            return $roomBasePrice;
        }

        // Rule 2: Exact Half Payment (Downpayment only)
        // Note: Using epsilon for float comparison safety
        if (abs($totalAmountPaid - ($bookingTotalPrice / 2)) < 1 && $totalCheckinPaid == 0) {
            return $roomBasePrice / 2;
        }

        return 0;
    }

    // --- EXPORTS ---

    // ... inside AccountingController class ...

    private function getTransactionDetails($from, $to)
    {
        // 1. Fetch Room Income (From Payments Table)
        $roomPayments = Payments::with(['booking.user', 'booking.summaries.facility'])
            ->whereBetween('payment_date', [$from, $to])
            ->where('status', 'paid')
            ->get()
            ->map(function ($payment) {
                $facilityNames = $payment->booking ? $payment->booking->summaries->map(fn($s) => $s->facility->name ?? 'Unknown')->join(', ') : 'N/A';

                return [
                    'timestamp' => Carbon::parse($payment->payment_date), // For sorting
                    'date' => Carbon::parse($payment->payment_date)->format('M d, Y h:i A'),
                    'type' => 'Room Income', // More specific label
                    'reference' => $payment->reference_no ?? '-',
                    'customer' => $payment->booking->user ? $payment->booking->user->firstname . ' ' . $payment->booking->user->lastname : 'Guest',
                    'description' => 'Room: ' . Str::limit($facilityNames, 30),
                    'method' => $payment->method ?? 'Cash',
                    'amount' => (float) $payment->amount,
                    'status' => ucfirst($payment->status),
                    'color' => 'green' // UI helper
                ];
            });

        // 2. Fetch Day Tour Income (From DayTourLogDetails Table)
        // Day Tours usually don't have a separate 'payments' table in your code, so we query the logs directly.
        $dayTours = DayTourLogDetails::with(['user', 'bookingGuestDetails.guestType'])
            ->whereBetween('date_tour', [$from, $to])
            ->where('reservation_status', 'paid') // Only paid tours
            ->get()
            ->map(function ($dt) {
                // Generate a description based on guests (e.g., "2 Adult (Pool), 1 Kid (Park)")
                $desc = $dt->bookingGuestDetails->map(function ($detail) {
                    return $detail->quantity . ' ' . ($detail->guestType->type ?? 'Guest');
                })->join(', ');

                return [
                    'timestamp' => Carbon::parse($dt->date_tour), // For sorting
                    'date' => Carbon::parse($dt->date_tour)->format('M d, Y'),
                    'type' => 'Day Tour',
                    'reference' => 'DT-' . str_pad($dt->id, 5, '0', STR_PAD_LEFT),
                    'customer' => $dt->user ? $dt->user->firstname . ' ' . $dt->user->lastname : 'Guest',
                    'description' => 'Day Tour: ' . Str::limit($desc, 30),
                    'method' => 'Cash', // Default if not stored
                    'amount' => (float) $dt->total_price,
                    'status' => 'Paid',
                    'color' => 'blue' // UI helper
                ];
            });

        // 3. Fetch Expenses
        $expenses = DB::table('expenses')
            ->whereBetween('expense_date', [$from, $to])
            ->get()
            ->map(function ($expense) {
                return [
                    'timestamp' => Carbon::parse($expense->expense_date), // For sorting
                    'date' => Carbon::parse($expense->expense_date)->format('M d, Y'),
                    'type' => 'Expense',
                    'reference' => '-',
                    'customer' => '-',
                    'description' => $expense->name ?? 'Operational Expense',
                    'method' => '-',
                    'amount' => -1 * abs((float) $expense->amount), // Negative for display logic
                    'status' => 'Paid',
                    'color' => 'red' // UI helper
                ];
            });

        // Merge all three collections and sort by date descending
        return $roomPayments
            ->merge($dayTours)
            ->merge($expenses)
            ->sortByDesc('timestamp')
            ->values();
    }

    public function export(Request $request)
    {
        try {
            $periodType = $request->query('period', 'monthly');
            $filterValue = $request->query('filter_value');

            // 1. Get Summary Data (Aggregates)
            $apiResponse = $this->monthlyIncomeApi($request);
            $summaryData = $apiResponse->getData(true);
            $combinedSummary = $summaryData['combined'] ?? [];

            // 2. Get Detailed Data (Transactions)
            $range = $this->calculateDateRange($periodType, $filterValue);
            $transactions = $this->getTransactionDetails($range['from'], $range['to']);

            $filename = 'financial_report_' . now()->format('Y_m_d_His') . '.csv';

            $response = new StreamedResponse(function () use ($combinedSummary, $summaryData, $transactions, $periodType, $filterValue) {
                $handle = fopen('php://output', 'w');
                fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

                // --- SECTION 1: HEADER ---
                fputcsv($handle, ['FINANCIAL REPORT']);
                fputcsv($handle, ['Generated', now()->format('F d, Y h:i A')]);
                fputcsv($handle, ['Period', ucfirst($periodType) . ' (' . ($filterValue ?: 'Current') . ')']);
                fputcsv($handle, []);

                // --- SECTION 2: EXECUTIVE SUMMARY ---
                fputcsv($handle, ['EXECUTIVE SUMMARY']);
                fputcsv($handle, ['Total Revenue', number_format($summaryData['totalIncome'], 2)]);
                fputcsv($handle, ['Total Expenses', number_format($summaryData['totalExpense'], 2)]);
                fputcsv($handle, ['Net Profit', number_format($summaryData['netTotal'], 2)]);
                fputcsv($handle, []);

                // --- SECTION 3: DAILY/MONTHLY BREAKDOWN ---
                fputcsv($handle, ['PERIOD BREAKDOWN']);
                fputcsv($handle, ['Period', 'Room Revenue', 'Day Tour Revenue', 'Total Revenue', 'Expenses', 'Net Profit']);
                foreach ($combinedSummary as $row) {
                    fputcsv($handle, [
                        $row['label'],
                        number_format($row['room'], 2),
                        number_format($row['daytour'], 2),
                        number_format($row['income'], 2),
                        number_format($row['expense'], 2),
                        number_format($row['net'], 2)
                    ]);
                }
                fputcsv($handle, []);
                fputcsv($handle, []);

                // --- SECTION 4: DETAILED TRANSACTION LOG (Dynamic Scheme/User info) ---
                fputcsv($handle, ['DETAILED TRANSACTION LOG']);
                fputcsv($handle, ['Date', 'Type', 'Reference #', 'Customer / Payee', 'Description', 'Payment Scheme', 'Amount', 'Status']);

                foreach ($transactions as $txn) {
                    fputcsv($handle, [
                        $txn['date'],
                        $txn['type'],
                        $txn['reference'],
                        $txn['customer'],
                        $txn['description'],
                        $txn['method'], // Shows "Visa", "GCash", etc.
                        number_format($txn['amount'], 2),
                        $txn['status']
                    ]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            return $response;

        } catch (\Exception $e) {
            Log::error("Export error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed');
        }
    }

    public function exportPdf(Request $request)
    {
        $periodType = $request->query('period', 'monthly');
        $filterValue = $request->query('filter_value');

        // 1. Get Summary Data
        $apiResponse = $this->monthlyIncomeApi($request);
        $summaryData = $apiResponse->getData(true);

        // 2. Get Detailed Transactions
        $range = $this->calculateDateRange($periodType, $filterValue);
        $transactions = $this->getTransactionDetails($range['from'], $range['to']);

        $pdf = Pdf::loadView('admin.accounting.report_pdf', [
            'summary' => $summaryData,
            'transactions' => $transactions,
            'period' => $periodType,
            'filterValue' => $filterValue,
            'generatedAt' => now()
        ])->setPaper('a4', 'landscape'); // Landscape allows more columns

        return $pdf->download('financial_report.pdf');
    }

    public function topPerformersApi(Request $request)
    {
        // You can implement top rooms/daytours logic here if needed
        return response()->json(['message' => 'Not implemented in this snippet']);
    }
}