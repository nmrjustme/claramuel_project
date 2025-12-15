<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\FacilityBookingLog;
use App\Models\Payments;
use App\Models\DayTourLogDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Imported Log
use Carbon\Carbon;
use Illuminate\Support\Str;

class AccountingService
{
    // Added $traceId (optional) to link logs
    public function getFinancialSummary($periodType, $filterValue, $traceId = 'NO-TRACE')
    {
        // DEBUG: Date Calculation
        $range = $this->calculateDateRange($periodType, $filterValue);
        $from = $range['from'];
        $to = $range['to'];
        $groupingMode = $range['groupingMode'];

        Log::info("[ACCT-{$traceId}] DATE RANGE: $from TO $to (Mode: $groupingMode)");

        // DEBUG: Data Fetching
        $roomQ = $this->fetchRoomIncome($groupingMode, $from, $to, $traceId);
        $dayTourQ = $this->fetchDayTourIncome($groupingMode, $from, $to, $traceId);
        $expenseQ = $this->fetchExpenses($groupingMode, $from, $to, $traceId);

        Log::info("[ACCT-{$traceId}] RAW COUNTS: Rooms[" . $roomQ->count() . "] DayTours[" . $dayTourQ->count() . "] Expenses[" . $expenseQ->count() . "]");

        // DEBUG: Combination
        $combined = $this->combineFinancialData($roomQ, $dayTourQ, $expenseQ, $periodType, $from);

        $totalIncome = collect($combined)->sum('income');
        $totalExpense = collect($combined)->sum('expense');
        $netTotal = $totalIncome - $totalExpense;
        $average = count($combined) ? $netTotal / count($combined) : 0;

        $best = collect($combined)->sortByDesc('income')->first();
        $bestPeriod = ['label' => $best['label'] ?? 'N/A', 'income' => $best['income'] ?? 0];

        Log::info("[ACCT-{$traceId}] FINAL CALC: Total Income: $totalIncome, Net: $netTotal");

        $chartData = [
            'labels' => array_column($combined, 'label'),
            'datasets' => [
                ['label' => 'Room Income', 'data' => array_column($combined, 'room')],
                ['label' => 'Day Tour Income', 'data' => array_column($combined, 'daytour')],
                ['label' => 'Expenses', 'data' => array_column($combined, 'expense')],
            ]
        ];

        return [
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'netTotal' => $netTotal,
                'average' => $average,
                'best' => $bestPeriod,
                'combined' => $combined,
                'chartData' => $chartData,
            ],
            'date_range' => $range,
            'transaction_history' => $this->getTransactionHistory($from, $to, $traceId) // Added history call
        ];
    }

    public function getTransactionHistory($from, $to, $traceId = 'NO-TRACE')
    {
        // 1. Room Income
        $roomPayments = Payments::whereBetween('payment_date', [$from, $to])
            ->whereNotIn('status', ['pending', 'failed', 'cancelled', 'declined'])
            ->whereNotNull('amount')
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($payment) {
                // ... (Keep existing mapping logic) ...
                $booking = $payment->booking;
                $customerName = 'GUEST';
                if ($booking && $booking->user) {
                    $customerName = strtoupper(($booking->user->firstname ?? '') . ' ' . ($booking->user->lastname ?? ''));
                }

                $facilityNames = 'Facility Booking';
                if ($booking && $booking->summaries && $booking->summaries->isNotEmpty()) {
                    $names = [];
                    foreach ($booking->summaries as $s) {
                        if ($s->facility)
                            $names[] = $s->facility->name;
                    }
                    if (!empty($names))
                        $facilityNames = implode(', ', $names);
                }

                return [
                    'timestamp' => Carbon::parse($payment->payment_date),
                    'date' => Carbon::parse($payment->payment_date)->format('M d, Y h:i A'),
                    'type' => 'Room Income',
                    'reference' => $payment->reference_no ?? '-',
                    'customer' => trim($customerName) ?: 'GUEST',
                    'description' => 'Room: ' . Str::limit($facilityNames, 30),
                    'method' => $payment->method ?? 'Cash',
                    'amount' => (float) $payment->amount,
                    'status' => ucfirst($payment->status),
                    'color' => 'green'
                ];
            });

        // 2. Day Tour Income
        $dayTours = DayTourLogDetails::whereBetween('date_tour', [$from, $to])
            ->whereNotIn('reservation_status', ['pending', 'cancelled', 'declined'])
            ->orderBy('date_tour', 'desc')
            ->get()
            ->map(function ($dt) {
                // ... (Keep existing mapping logic) ...
                $customerName = 'GUEST';
                if ($dt->user) {
                    $customerName = strtoupper(($dt->user->firstname ?? '') . ' ' . ($dt->user->lastname ?? ''));
                }

                $desc = 'Day Tour';
                if ($dt->bookingGuestDetails && $dt->bookingGuestDetails->isNotEmpty()) {
                    $parts = [];
                    foreach ($dt->bookingGuestDetails as $detail) {
                        $type = $detail->guestType->type ?? 'Guest';
                        $parts[] = $detail->quantity . ' ' . $type;
                    }
                    if (!empty($parts))
                        $desc = 'Day Tour: ' . Str::limit(implode(', ', $parts), 30);
                }

                return [
                    'timestamp' => Carbon::parse($dt->date_tour),
                    'date' => Carbon::parse($dt->date_tour)->format('M d, Y'),
                    'type' => 'Day Tour',
                    'reference' => 'DT-' . str_pad($dt->id, 5, '0', STR_PAD_LEFT),
                    'customer' => trim($customerName) ?: 'GUEST',
                    'description' => $desc,
                    'method' => 'Cash',
                    'amount' => (float) $dt->total_price,
                    'status' => ucfirst($dt->reservation_status),
                    'color' => 'blue'
                ];
            });

        // 3. Expenses
        $expenses = collect(); // Default to empty base collection
        if (DB::getSchemaBuilder()->hasTable('expenses')) {
            $expenses = DB::table('expenses')
                ->whereBetween('expense_date', [$from, $to])
                ->whereNotNull('amount')
                ->orderBy('expense_date', 'desc')
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
                        'status' => 'Paid',
                        'color' => 'red'
                    ];
                });
        }

        // --- THE FIX IS HERE ---
        // We start with collect() to ensure we are using a "Support Collection"
        // instead of an "Eloquent Collection".
        return collect()
            ->merge($roomPayments)
            ->merge($dayTours)
            ->merge($expenses)
            ->sortByDesc('timestamp')
            ->values();
    }

    // --- INTERNAL HELPER METHODS ---

    public function calculateDateRange($periodType, $filterValue)
    {
        $from = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $to = Carbon::now()->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');
        $groupingMode = 'daily_breakdown';

        if (!$filterValue)
            return compact('from', 'to', 'groupingMode');

        try {
            if ($periodType === 'daily') {
                $from = Carbon::parse($filterValue)->startOfDay()->toDateTimeString();
                $to = Carbon::parse($filterValue)->endOfDay()->toDateTimeString();
                $groupingMode = 'daily_single';
            } elseif ($periodType === 'monthly') {
                $date = Carbon::parse($filterValue);
                $from = $date->copy()->startOfMonth()->startOfDay()->toDateTimeString();
                $to = $date->copy()->endOfMonth()->endOfDay()->toDateTimeString();
                $groupingMode = 'daily_breakdown';
            } elseif ($periodType === 'yearly') {
                $date = Carbon::createFromFormat('Y', $filterValue);
                $from = $date->copy()->startOfYear()->startOfDay()->toDateTimeString();
                $to = $date->copy()->endOfYear()->endOfDay()->toDateTimeString();
                $groupingMode = 'monthly_breakdown';
            }
        } catch (\Exception $e) {
            Log::error("Date calc error: " . $e->getMessage());
        }

        return compact('from', 'to', 'groupingMode');
    }

    private function combineFinancialData($roomQ, $dayTourQ, $expenseQ, $periodType, $from)
    {
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
            $combined[] = [
                'label' => $label,
                'room' => (float) $room,
                'daytour' => (float) $daytour,
                'expense' => (float) $expense,
                'income' => (float) $income,
                'net' => (float) ($income - $expense),
            ];
        }

        usort($combined, function ($a, $b) {
            return strtotime($a['label']) - strtotime($b['label']);
        });

        if ($periodType === 'daily' && empty($combined)) {
            $dateLabel = $from ? Carbon::parse($from)->format('M d Y') : 'Selected Date';
            $combined[] = [
                'label' => $dateLabel,
                'room' => 0.00,
                'daytour' => 0.00,
                'expense' => 0.00,
                'income' => 0.00,
                'net' => 0.00
            ];
        }

        return $combined;
    }

    private function fetchRoomIncome($grouping, $from, $to, $traceId = 'NO-TRACE')
    {
        $bookings = FacilityBookingLog::query()
            ->whereHas('summaries.facility', fn($q) => $q->where('type', 'room'))
            ->where(function ($q) {
                $q->where('status', '!=', 'cancelled')
                    ->orWhere(function ($q) {
                        $q->where('status', 'cancelled')
                            ->whereDoesntHave('payments', function ($q) {
                                $q->whereNotNull('refund_amount');
                            });
                    });
            })
            ->whereHas('details', fn($q) => $q->whereBetween('checkin_date', [$from, $to]))
            ->get();

        // DEBUG: How many bookings found?
        Log::info("[ACCT-{$traceId}] FETCH ROOMS: Found " . $bookings->count() . " bookings in range.");

        $periodEarnings = [];

        foreach ($bookings as $booking) {
            $firstDetail = $booking->details->first();
            if (!$firstDetail)
                continue;

            $checkinDate = $firstDetail->checkin_date;
            $periodKey = $this->getPeriodKey($checkinDate, $grouping);
            $label = $this->getPeriodLabel($checkinDate, $grouping);

            $totalBookingPrice = $booking->details->sum('total_price');
            $totalAmountPaid = $booking->payments->sum('amount');
            $totalCheckinPaid = $booking->payments->sum('checkin_paid');

            if ($booking->summaries) {
                foreach ($booking->summaries as $summary) {
                    if (!$summary->facility || $summary->facility->type !== 'room')
                        continue;

                    $roomBasePrice = $summary->facility_price + $summary->breakfast_price;
                    $roomRefunds = $this->calculateRoomRefunds($booking, $summary->facility_id);

                    $roomEarnings = $this->applyPaymentLogicPerRoom($roomBasePrice, $totalBookingPrice, $totalAmountPaid, $totalCheckinPaid);
                    $roomNetEarnings = max(0, $roomEarnings - $roomRefunds);

                    // DEBUG: Log if amount is weird
                    if ($roomNetEarnings < 0) {
                        Log::warning("[ACCT-{$traceId}] NEGATIVE EARNING: Booking #{$booking->id}, Amount: $roomNetEarnings");
                    }

                    if (!isset($periodEarnings[$periodKey])) {
                        $periodEarnings[$periodKey] = ['label' => $label, 'income' => 0];
                    }
                    $periodEarnings[$periodKey]['income'] += $roomNetEarnings;
                }
            }
        }
        return collect($periodEarnings)->values();
    }

    private function fetchDayTourIncome($grouping, $from, $to, $traceId = 'NO-TRACE')
    {
        $labelSql = $this->getLabelExpression('dt.date_tour', $grouping);
        $keySql = $this->getKeyExpression('dt.date_tour', $grouping);

        $results = DB::table('day_tour_log_details as dt')
            ->select(
                DB::raw("$labelSql as label"),
                DB::raw("$keySql as period_key"),
                DB::raw('SUM(dt.total_price) as total_income')
            )
            ->whereIn('dt.reservation_status', ['paid', 'approved', 'completed'])
            ->whereBetween('dt.date_tour', [$from, $to])
            ->whereNotNull('dt.total_price')
            ->groupBy(DB::raw($keySql), DB::raw($labelSql))
            ->orderBy('period_key')
            ->get()
            ->map(fn($r) => ['label' => $r->label, 'income' => (float) $r->total_income]);

        Log::info("[ACCT-{$traceId}] FETCH DAYTOUR: Groups found: " . $results->count());
        return $results;
    }

    private function fetchExpenses($grouping, $from, $to, $traceId = 'NO-TRACE')
    {
        if (!DB::getSchemaBuilder()->hasTable('expenses')) {
            Log::warning("[ACCT-{$traceId}] EXPENSES: Table not found.");
            return collect();
        }

        $labelSql = $this->getLabelExpression('expense_date', $grouping);
        $keySql = $this->getKeyExpression('expense_date', $grouping);

        $results = DB::table('expenses')
            ->select(
                DB::raw("$labelSql as label"),
                DB::raw("$keySql as period_key"),
                DB::raw('SUM(amount) as total_expense')
            )
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy(DB::raw($keySql), DB::raw($labelSql))
            ->orderBy('period_key')
            ->get()
            ->map(fn($r) => ['label' => $r->label, 'expense' => (float) $r->total_expense]);

        Log::info("[ACCT-{$traceId}] FETCH EXPENSE: Groups found: " . $results->count());
        return $results;
    }

    // --- HELPER LOGIC (Unchanged) ---

    private function getLabelExpression($column, $grouping)
    {
        return match ($grouping) {
            'monthly_breakdown' => "DATE_FORMAT($column, '%M %Y')",
            default => "DATE_FORMAT($column, '%b %d %Y')",
        };
    }

    private function getKeyExpression($column, $grouping)
    {
        return match ($grouping) {
            'monthly_breakdown' => "DATE_FORMAT($column, '%Y-%m')",
            default => "DATE($column)",
        };
    }

    private function getPeriodKey($date, $grouping)
    {
        $d = Carbon::parse($date);
        return $grouping === 'monthly_breakdown' ? $d->format('Y-m') : $d->format('Y-m-d');
    }

    private function getPeriodLabel($date, $grouping)
    {
        $d = Carbon::parse($date);
        return $grouping === 'monthly_breakdown' ? $d->format('F Y') : $d->format('M d Y');
    }

    private function calculateRoomRefunds($booking, $roomId)
    {
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

        return $refundPayments->sum('refund_amount') * $roomShare;
    }

    private function applyPaymentLogicPerRoom($roomBasePrice, $bookingTotalPrice, $totalAmountPaid, $totalCheckinPaid)
    {
        if ($totalAmountPaid == 0)
            return 0;
        $totalReceived = $totalAmountPaid + $totalCheckinPaid;

        if ($totalReceived >= $bookingTotalPrice)
            return $roomBasePrice;
        if (abs($totalAmountPaid - ($bookingTotalPrice / 2)) < 1.0 && $totalCheckinPaid == 0)
            return $roomBasePrice / 2;
        return $roomBasePrice;
    }
}