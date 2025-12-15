<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\FacilityBookingLog;
use App\Models\Payments;
use App\Models\DayTourLogDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AccountingService
{
    /**
     * Main entry point for Dashboard Statistics
     */
    public function getFinancialSummary($periodType, $filterValue)
    {
        // 1. Determine Date Range
        $range = $this->calculateDateRange($periodType, $filterValue);
        $from = $range['from'];
        $to = $range['to'];
        $groupingMode = $range['groupingMode'];

        // 2. Fetch Data
        $roomQ = $this->fetchRoomIncome($groupingMode, $from, $to);
        $dayTourQ = $this->fetchDayTourIncome($groupingMode, $from, $to);
        $expenseQ = $this->fetchExpenses($groupingMode, $from, $to);

        // 3. Combine Data
        $combined = $this->combineFinancialData($roomQ, $dayTourQ, $expenseQ, $periodType, $from);

        // 4. Calculate Totals
        $totalIncome = collect($combined)->sum('income');
        $totalExpense = collect($combined)->sum('expense');
        $netTotal = $totalIncome - $totalExpense;
        $average = count($combined) ? $netTotal / count($combined) : 0;
        
        $best = collect($combined)->sortByDesc('income')->first();
        $bestPeriod = ['label' => $best['label'] ?? 'N/A', 'income' => $best['income'] ?? 0];

        // 5. Format for Charts
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
            'date_range' => $range // Returned for exports to use
        ];
    }

    /**
     * Main entry point for Detailed Transaction Logs
     */
    public function getTransactionHistory($from, $to)
    {
        // 1. Room Income (Payments)
        $roomPayments = Payments::with(['booking.user', 'booking.summaries.facility'])
            ->whereBetween('payment_date', [$from, $to])
            ->where('status', 'paid')
            ->get()
            ->map(function ($payment) {
                $facilityNames = $payment->booking ? $payment->booking->summaries->map(fn($s) => $s->facility->name ?? 'Unknown')->join(', ') : 'N/A';
                return [
                    'timestamp' => Carbon::parse($payment->payment_date),
                    'date' => Carbon::parse($payment->payment_date)->format('M d, Y h:i A'),
                    'type' => 'Room Income',
                    'reference' => $payment->reference_no ?? '-',
                    'customer' => $payment->booking->user ? $payment->booking->user->firstname . ' ' . $payment->booking->user->lastname : 'Guest',
                    'description' => 'Room: ' . Str::limit($facilityNames, 30),
                    'method' => $payment->method ?? 'Cash',
                    'amount' => (float) $payment->amount,
                    'status' => ucfirst($payment->status),
                    'color' => 'green'
                ];
            });

        // 2. Day Tour Income
        $dayTours = DayTourLogDetails::with(['user', 'bookingGuestDetails.guestType'])
            ->whereBetween('date_tour', [$from, $to])
            ->where('reservation_status', 'paid')
            ->get()
            ->map(function ($dt) {
                $desc = $dt->bookingGuestDetails->map(fn($detail) => $detail->quantity . ' ' . ($detail->guestType->type ?? 'Guest'))->join(', ');
                return [
                    'timestamp' => Carbon::parse($dt->date_tour),
                    'date' => Carbon::parse($dt->date_tour)->format('M d, Y'),
                    'type' => 'Day Tour',
                    'reference' => 'DT-' . str_pad($dt->id, 5, '0', STR_PAD_LEFT),
                    'customer' => $dt->user ? $dt->user->firstname . ' ' . $dt->user->lastname : 'Guest',
                    'description' => 'Day Tour: ' . Str::limit($desc, 30),
                    'method' => 'Cash',
                    'amount' => (float) $dt->total_price,
                    'status' => 'Paid',
                    'color' => 'blue'
                ];
            });

        // 3. Expenses
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
                    'status' => 'Paid',
                    'color' => 'red'
                ];
            });

        return $roomPayments->merge($dayTours)->merge($expenses)->sortByDesc('timestamp')->values();
    }

    // --- INTERNAL HELPER METHODS ---

    public function calculateDateRange($periodType, $filterValue)
    {
        $from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = Carbon::now()->endOfMonth()->format('Y-m-d');
        $groupingMode = 'daily_breakdown';

        if (!$filterValue) return compact('from', 'to', 'groupingMode');

        try {
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

        // Fill empty daily view
        if ($periodType === 'daily' && empty($combined)) {
            $dateLabel = $from ? Carbon::parse($from)->format('M d Y') : 'Selected Date';
            $combined[] = [
                'label' => $dateLabel, 'room' => 0, 'daytour' => 0, 'expense' => 0, 'income' => 0, 'net' => 0
            ];
        }

        return $combined;
    }

    private function fetchRoomIncome($grouping, $from, $to)
    {
        $bookings = FacilityBookingLog::with(['payments', 'summaries.facility', 'details'])
            ->whereHas('summaries.facility', fn($q) => $q->where('type', 'room'))
            ->where('status', '!=', 'pending_confirmation')
            ->whereHas('details', fn($q) => $q->whereBetween('checkin_date', [$from, $to]))
            ->get();

        $periodEarnings = [];

        foreach ($bookings as $booking) {
            $checkinDate = $booking->details->first()->checkin_date ?? null;
            if (!$checkinDate) continue;

            $periodKey = $this->getPeriodKey($checkinDate, $grouping);
            $label = $this->getPeriodLabel($checkinDate, $grouping);

            $totalBookingPrice = $booking->details->sum('total_price');
            $totalAmountPaid = $booking->payments->sum('amount');
            $totalCheckinPaid = $booking->payments->sum('checkin_paid');

            foreach ($booking->summaries as $summary) {
                if ($summary->facility->type !== 'room') continue;

                $roomBasePrice = $summary->facility_price + $summary->breakfast_price;
                $roomRefunds = $this->calculateRoomRefunds($booking, $summary->facility_id);

                // Apply Logic: Full Payment vs Half Payment
                $roomEarnings = $this->applyPaymentLogicPerRoom($roomBasePrice, $totalBookingPrice, $totalAmountPaid, $totalCheckinPaid);
                $roomNetEarnings = max(0, $roomEarnings - $roomRefunds);

                if (!isset($periodEarnings[$periodKey])) {
                    $periodEarnings[$periodKey] = ['label' => $label, 'income' => 0];
                }
                $periodEarnings[$periodKey]['income'] += $roomNetEarnings;
            }
        }
        return collect($periodEarnings)->values();
    }

    private function fetchDayTourIncome($grouping, $from, $to)
    {
        return DB::table('day_tour_log_details as dt')
            ->select(
                DB::raw($this->getLabelExpression('dt.date_tour', $grouping) . " as label"),
                DB::raw($this->getKeyExpression('dt.date_tour', $grouping) . " as period_key"),
                DB::raw('SUM(dt.total_price) as total_income')
            )
            ->where('dt.reservation_status', 'paid')
            ->whereBetween('dt.date_tour', [$from, $to])
            ->groupBy('period_key', 'label')
            ->orderBy('period_key')
            ->get()
            ->map(fn($r) => ['label' => $r->label, 'income' => (float) $r->total_income]);
    }

    private function fetchExpenses($grouping, $from, $to)
    {
        if (!DB::getSchemaBuilder()->hasTable('expenses')) return collect();

        return DB::table('expenses')
            ->select(
                DB::raw($this->getLabelExpression('expense_date', $grouping) . " as label"),
                DB::raw($this->getKeyExpression('expense_date', $grouping) . " as period_key"),
                DB::raw('SUM(amount) as total_expense')
            )
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('period_key', 'label')
            ->orderBy('period_key')
            ->get()
            ->map(fn($r) => ['label' => $r->label, 'expense' => (float) $r->total_expense]);
    }

    // --- SQL & Logic Helpers ---

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
        if ($refundPayments->isEmpty()) return 0;

        $roomsInBooking = max($booking->summaries->count(), 1);
        $totalBookingPrice = $booking->details->sum('total_price');
        $roomSummary = $booking->summaries->where('facility_id', $roomId)->first();

        if (!$roomSummary) return 0;

        $roomBasePrice = $roomSummary->facility_price + $roomSummary->breakfast_price;
        $roomShare = $totalBookingPrice > 0 ? ($roomBasePrice / $totalBookingPrice) : (1 / $roomsInBooking);
        
        return $refundPayments->sum('refund_amount') * $roomShare;
    }

    private function applyPaymentLogicPerRoom($roomBasePrice, $bookingTotalPrice, $totalAmountPaid, $totalCheckinPaid)
    {
        if ($totalAmountPaid == 0) return 0;
        $totalReceived = $totalAmountPaid + $totalCheckinPaid;

        if ($totalReceived >= $bookingTotalPrice) return $roomBasePrice; // Full Paid
        if (abs($totalAmountPaid - ($bookingTotalPrice / 2)) < 1 && $totalCheckinPaid == 0) return $roomBasePrice / 2; // Downpayment

        return 0; // Partial/Unknown
    }
}