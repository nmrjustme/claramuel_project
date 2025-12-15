<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Expense;
use App\Models\FacilityBookingLog;
use App\Models\Payments;
use App\Models\DayTourLogDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AccountingReportController extends Controller
{
    public function index()
    {
        return view('admin.report.index');
    }

    /**
     * MAIN API: Returns Summary, Breakdown Table, and Transaction Logs in one call
     * Used by the AJAX loadData() function in your view
     */
    public function getReportData(Request $request)
    {
        $periodType = $request->query('period', 'monthly');
        $filterValue = $request->query('filter_value');

        // 1. Calculate Date Range
        $range = $this->calculateDateRange($periodType, $filterValue);
        $from = $range['from'];
        $to = $range['to'];
        $groupingMode = $range['groupingMode'];

        // 2. Fetch Aggregated Data (For Summary Cards & Breakdown Table)
        $aggregated = $this->getAggregatedData($groupingMode, $from, $to, $periodType);

        // 3. Fetch Detailed Transaction Logs (For the bottom table)
        $transactions = $this->getTransactionDetails($from, $to);

        return response()->json([
            'totalIncome' => $aggregated['totalIncome'],
            'totalExpense' => $aggregated['totalExpense'],
            'netTotal' => $aggregated['netTotal'],
            'combined' => $aggregated['combined'], // The breakdown table data
            'transactions' => $transactions,           // The detailed logs
        ]);
    }

    // --- AGGREGATION LOGIC (Refactored from your reference) ---

    private function getAggregatedData($groupingMode, $from, $to, $periodType)
    {
        // A. Fetch Room Income
        $roomQ = $this->getRoomIncomeByPeriod($groupingMode, $from, $to);

        // B. Fetch Day Tour Income
        $dayTourQ = $this->getDayTourIncomeByPeriod($groupingMode, $from, $to);

        // C. Fetch Expenses
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

        // D. Combine Data
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
                'room' => (float) $room,
                'daytour' => (float) $daytour,
                'expense' => (float) $expense,
                'income' => (float) $income,
                'net' => (float) $net,
            ];
        }

        // Handle empty state for daily view
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

        // Summaries
        $totalIncome = collect($combined)->sum('income');
        $totalExpense = collect($combined)->sum('expense');
        $netTotal = $totalIncome - $totalExpense;

        return compact('totalIncome', 'totalExpense', 'netTotal', 'combined');
    }

    // --- DETAILED LOGS LOGIC ---

    // In app/Http/Controllers/AccountingReportController.php

    private function getTransactionDetails($from, $to)
    {
        // 1. Room Income (Fixed: Added ->toBase())
        $roomPayments = Payments::with(['booking.user', 'booking.summaries.facility'])
            ->whereBetween('payment_date', [$from, $to])
            ->whereNotNull('amount')
            ->get()
            ->toBase()
            ->map(function ($payment) {
                $facilityNames = $payment->booking ? $payment->booking->summaries->map(fn($s) => $s->facility->name ?? 'Unknown')->join(', ') : 'N/A';

                // --- MODIFIED SECTION START ---
                $customerName = 'Guest';
                if ($payment->booking && $payment->booking->user) {
                    $fullName = $payment->booking->user->firstname . ' ' . $payment->booking->user->lastname;
                    // strtolower converts "JOHN" to "john", ucwords converts "john" to "John"
                    $customerName = ucwords(strtolower($fullName));
                }
                // --- MODIFIED SECTION END ---
    
                return [
                    'timestamp' => Carbon::parse($payment->payment_date),
                    'date' => Carbon::parse($payment->payment_date)->format('M d, Y h:i A'),
                    'type' => 'Room',
                    'reference' => $payment->reference_no ?? '-',
                    'customer' => $customerName,
                    'description' => Str::limit($facilityNames, 40),
                    'method' => $payment->method ?? 'Cash',
                    'amount' => (float) $payment->amount,
                    'color' => 'green'
                ];
            });

        // 2. Day Tour Income (Fixed: Added ->toBase())
        $dayTours = DayTourLogDetails::with(['user', 'bookingGuestDetails.guestType'])
            // CHANGE THIS: Filter by created_at (or payment_date) instead of date_tour
            ->whereBetween('created_at', [$from, $to])
            ->where('reservation_status', 'paid')
            ->get()
            ->toBase()
            ->map(function ($dt) {
                $desc = $dt->bookingGuestDetails->map(function ($detail) {
                    return $detail->quantity . ' ' . ($detail->guestType->type ?? 'Guest');
                })->join(', ');

                // --- MODIFIED SECTION START ---
                $customerName = 'Guest';
                if ($dt->user) {
                    $fullName = $dt->user->firstname . ' ' . $dt->user->lastname;
                    $customerName = ucwords(strtolower($fullName));
                }
                // --- MODIFIED SECTION END ---
    
                return [
                    'timestamp' => Carbon::parse($dt->date_tour),
                    'date' => Carbon::parse($dt->date_tour)->format('M d, Y'),
                    'type' => 'Day Tour',
                    'reference' => 'DT-' . str_pad($dt->id, 5, '0', STR_PAD_LEFT),
                    'customer' => $customerName,
                    'description' => Str::limit($desc, 40),
                    'method' => 'Cash',
                    'amount' => (float) $dt->total_price,
                    'color' => 'blue'
                ];
            });

        // 3. Expenses (DB::table already returns a base collection)
        $expenses = DB::table('expenses')
            ->whereBetween('expense_date', [$from, $to])
            ->get()
            ->map(function ($expense) {
                return [
                    'timestamp' => Carbon::parse($expense->expense_date),
                    'date' => Carbon::parse($expense->expense_date)->format('M d, Y'),
                    'type' => 'Expense',
                    'reference' => '-',
                    'customer' => '-',
                    'description' => $expense->name ?? 'Operational Expense',
                    'method' => '-',
                    'amount' => -1 * abs((float) $expense->amount),
                    'color' => 'red'
                ];
            });

        // Merge and Sort Descending
        return $roomPayments
            ->merge($dayTours)
            ->merge($expenses)
            ->sortByDesc('timestamp')
            ->values();
    }

    // --- EXPORTS ---

    public function exportCsv(Request $request)
    {
        $periodType = $request->query('period', 'monthly');
        $filterValue = $request->query('filter_value');

        $range = $this->calculateDateRange($periodType, $filterValue);
        $aggregated = $this->getAggregatedData($range['groupingMode'], $range['from'], $range['to'], $periodType);
        $transactions = $this->getTransactionDetails($range['from'], $range['to']);

        $filename = 'financial_report_' . now()->format('Y_m_d_His') . '.csv';

        $response = new StreamedResponse(function () use ($aggregated, $transactions, $periodType, $filterValue) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($handle, ['FINANCIAL REPORT - MT. CLARAMUEL RESORT']);
            fputcsv($handle, ['Generated', now()->format('F d, Y h:i A')]);
            fputcsv($handle, ['Period', ucfirst($periodType) . ': ' . $filterValue]);
            fputcsv($handle, []);

            // Summary
            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Total Revenue', $aggregated['totalIncome']]);
            fputcsv($handle, ['Total Expenses', $aggregated['totalExpense']]);
            fputcsv($handle, ['Net Profit', $aggregated['netTotal']]);
            fputcsv($handle, []);

            // Breakdown
            fputcsv($handle, ['PERIOD BREAKDOWN']);
            fputcsv($handle, ['Period', 'Room', 'Day Tour', 'Expense', 'Total Revenue', 'Net Profit']);
            foreach ($aggregated['combined'] as $row) {
                fputcsv($handle, [$row['label'], $row['room'], $row['daytour'], $row['expense'], $row['income'], $row['net']]);
            }
            fputcsv($handle, []);

            // Transactions
            fputcsv($handle, ['TRANSACTION LOG']);
            fputcsv($handle, ['Date', 'Type', 'Ref #', 'Customer', 'Description', 'Method', 'Amount']);
            foreach ($transactions as $txn) {
                fputcsv($handle, [
                    $txn['date'],
                    $txn['type'],
                    $txn['reference'],
                    $txn['customer'],
                    $txn['description'],
                    $txn['method'],
                    $txn['amount']
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return $response;
    }

    public function exportPdf(Request $request)
    {
        $periodType = $request->query('period', 'monthly');
        $filterValue = $request->query('filter_value');

        $range = $this->calculateDateRange($periodType, $filterValue);
        $aggregated = $this->getAggregatedData($range['groupingMode'], $range['from'], $range['to'], $periodType);
        $transactions = $this->getTransactionDetails($range['from'], $range['to']);

        // Ideally you reuse the view or create a specific PDF view
        $pdf = Pdf::loadView('admin.report.report_pdf', [
            'summary' => $aggregated,
            'transactions' => $transactions,
            'period' => $periodType,
            'filterValue' => $filterValue
        ])->setPaper('a4', 'portrait');

        return $pdf->download('financial_report.pdf');
    }

    // --- HELPERS (Dates & SQL) ---

    private function calculateDateRange($periodType, $filterValue)
    {
        $from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = Carbon::now()->endOfMonth()->format('Y-m-d');
        $groupingMode = 'daily_breakdown';

        if (!$filterValue)
            return compact('from', 'to', 'groupingMode');

        if ($periodType === 'daily') {
            $from = $filterValue;
            $to = Carbon::parse($filterValue)->endOfDay()->toDateTimeString();
            $groupingMode = 'daily_single';
        } elseif ($periodType === 'monthly') {
            $date = Carbon::parse($filterValue);
            $from = $date->startOfMonth()->format('Y-m-d');
            $to = $date->endOfMonth()->format('Y-m-d');
            $groupingMode = 'daily_breakdown';
        } elseif ($periodType === 'yearly') {
            $date = Carbon::createFromFormat('Y', $filterValue);
            $from = $date->startOfYear()->format('Y-m-d');
            $to = $date->endOfYear()->format('Y-m-d');
            $groupingMode = 'monthly_breakdown';
        }

        return compact('from', 'to', 'groupingMode');
    }

    private function getRoomIncomeByPeriod($grouping, $from, $to)
    {
        $bookings = FacilityBookingLog::with(['payments', 'summaries.facility', 'details'])
            ->whereHas('summaries.facility', fn($q) => $q->where('type', 'room'))

            // ✅ ADD THIS BLOCK
            ->where(function ($q) {
                $q->where('status', '!=', 'cancelled')
                    ->orWhere(function ($q) {
                        $q->where('status', 'cancelled')
                            ->whereDoesntHave('payments', function ($q) {
                                $q->whereNotNull('refund_amount');
                            });
                    });
            })
            // ✅ END ADD

            ->whereHas('details', fn($q) => $q->whereBetween('checkin_date', [$from, $to]))
            ->get();

        $periodEarnings = [];

        foreach ($bookings as $booking) {
            $checkinDate = $booking->details->first()->checkin_date ?? null;
            if (!$checkinDate)
                continue;

            $periodKey = $this->getPeriodKey($checkinDate, $grouping);
            $label = $this->getPeriodLabel($checkinDate, $grouping);

            $totalBookingPrice = $booking->details->sum('total_price');
            $totalAmountPaid = $booking->payments->sum('amount');
            $totalCheckinPaid = $booking->payments->sum('checkin_paid');

            foreach ($booking->summaries as $summary) {
                if ($summary->facility->type !== 'room')
                    continue;

                $roomBasePrice = $summary->facility_price + $summary->breakfast_price;

                // Refund calculation stays the same
                $totalRefunds = 0;
                $refundPayments = $booking->payments->where('refund_amount', '>', 0);
                if ($refundPayments->isNotEmpty()) {
                    $roomsInBooking = max($booking->summaries->count(), 1);
                    $roomShare = $totalBookingPrice > 0
                        ? ($roomBasePrice / $totalBookingPrice)
                        : (1 / $roomsInBooking);

                    foreach ($refundPayments as $payment) {
                        $totalRefunds += ($payment->refund_amount * $roomShare);
                    }
                }

                // Payment rules
                $roomEarnings = 0;
                $totalReceived = $totalAmountPaid + $totalCheckinPaid;

                if ($totalReceived >= $totalBookingPrice) {
                    $roomEarnings = $roomBasePrice;
                } elseif (abs($totalAmountPaid - ($totalBookingPrice / 2)) < 1 && $totalCheckinPaid == 0) {
                    $roomEarnings = $roomBasePrice / 2;
                }

                $roomNetEarnings = max(0, $roomEarnings - $totalRefunds);

                if (!isset($periodEarnings[$periodKey])) {
                    $periodEarnings[$periodKey] = ['label' => $label, 'income' => 0];
                }

                $periodEarnings[$periodKey]['income'] += $roomNetEarnings;
            }
        }

        return collect($periodEarnings)->values();
    }

    private function getDayTourIncomeByPeriod($grouping, $from, $to)
    {
        $query = DB::table('day_tour_log_details as dt')
            ->select(
                DB::raw($this->getLabelExpression('dt.date_tour', $grouping) . " as label"),
                DB::raw($this->getKeyExpression('dt.date_tour', $grouping) . " as period_key"),
                DB::raw('SUM(dt.total_price) as total_income')
            )
            ->where('dt.reservation_status', 'paid')
            ->whereBetween('dt.date_tour', [$from, $to]);

        $query->whereExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('booking_guest_details as bgd')
                ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                ->where('bgd.quantity', '>', 0);
        });

        return $query->groupBy('period_key', 'label')
            ->orderBy('period_key')
            ->get()
            ->map(fn($r) => ['label' => $r->label, 'income' => (float) $r->total_income]);
    }

    private function getLabelExpression($column, $grouping)
    {
        if ($grouping === 'monthly_breakdown')
            return "DATE_FORMAT($column, '%M %Y')";
        return "DATE_FORMAT($column, '%b %d %Y')";
    }

    private function getKeyExpression($column, $grouping)
    {
        if ($grouping === 'monthly_breakdown')
            return "DATE_FORMAT($column, '%Y-%m')";
        return "DATE($column)";
    }

    private function getPeriodKey($date, $grouping)
    {
        return $grouping === 'monthly_breakdown' ? Carbon::parse($date)->format('Y-m') : Carbon::parse($date)->format('Y-m-d');
    }

    private function getPeriodLabel($date, $grouping)
    {
        return $grouping === 'monthly_breakdown' ? Carbon::parse($date)->format('F Y') : Carbon::parse($date)->format('M d Y');
    }
}