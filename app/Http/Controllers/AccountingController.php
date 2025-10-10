<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Facility;
use App\Models\Payments;
use App\Models\FacilityBookingLog;
use App\Models\FacilityBookingDetails;
use App\Models\FacilitySummary;
use App\Models\DayTourLogDetails;
use Carbon\Carbon;
use App\Models\Expense;
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
        try {
            // Use the FIXED calculation logic for rooms WITH REFUND DEDUCTIONS
            $roomIncome = $this->getMonthlyRoomIncomeFixed();
            $dayTourIncome = $this->getMonthlyDayTourIncome();

            $combined = $this->combineMonthlyIncome($roomIncome, $dayTourIncome);

            $totalRevenue = collect($combined)->sum(fn($c) => $c['room'] + $c['daytour']);
            $averageMonthly = count($combined) ? $totalRevenue / count($combined) : 0;

            $bestMonthRecord = collect($combined)->sortByDesc(fn($c) => $c['room'] + $c['daytour'])->first();
            $bestMonth = [
                'month' => $bestMonthRecord['month'] ?? 'N/A',
                'income' => ($bestMonthRecord['room'] ?? 0) + ($bestMonthRecord['daytour'] ?? 0),
            ];

            return response()->json([
                'totalRevenue' => $totalRevenue,
                'averageMonthly' => $averageMonthly,
                'bestMonth' => $bestMonth,
                'chartData' => [
                    'labels' => array_column($combined, 'month'),
                    'datasets' => [
                        [
                            'label' => 'Room Income (Net of Refunds)',
                            'data' => array_column($combined, 'room'),
                            'borderColor' => 'rgba(34, 4, 10, 1)',
                            'backgroundColor' => 'rgba(218, 20, 59, 1)', // completely solid
                        ],
                        [
                            'label' => 'Day Tour Income',
                            'data' => array_column($combined, 'daytour'),
                            'borderColor' => 'rgba(34, 4, 10, 1)',
                            'backgroundColor' => 'rgba(178, 34, 34, 1)', // completely solid
                        ],

                    ],
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in monthlyIncomeApi: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load income data'
            ], 500);
        }
    }

    /**
     * FIXED VERSION - Properly calculates monthly room income WITH REFUND DEDUCTIONS
     */
    private function getMonthlyRoomIncomeFixed()
    {
        try {
            $monthlyData = [];

            // Get all distinct months with bookings
            $months = FacilityBookingDetails::select(
                DB::raw('YEAR(checkin_date) as year'),
                DB::raw('MONTH(checkin_date) as month'),
                DB::raw("DATE_FORMAT(checkin_date, '%M %Y') as month_year")
            )
                ->distinct()
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            foreach ($months as $month) {
                $startDate = Carbon::create($month->year, $month->month, 1)->startOfMonth();
                $endDate = Carbon::create($month->year, $month->month, 1)->endOfMonth();

                // Get all bookings for this month with proper relationships
                $bookings = FacilityBookingLog::with(['payments', 'summaries', 'details'])
                    ->whereHas('summaries', function ($query) {
                        $query->whereHas('facility', function ($q) {
                            $q->where('type', 'room');
                        });
                    })
                    ->whereHas('details', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('checkin_date', [$startDate, $endDate]);
                    })
                    ->where('status', '!=', 'pending_confirmation')
                    ->get();

                $monthlyIncome = 0;

                foreach ($bookings as $booking) {
                    $roomsInBooking = $booking->summaries->count();
                    $roomsInBooking = max($roomsInBooking, 1);

                    // Calculate total booking price
                    $totalBookingPrice = $booking->details->sum('total_price');

                    // Get payment totals
                    $totalAmountPaid = $booking->payments->sum('amount');
                    $totalCheckinPaid = $booking->payments->sum('checkin_paid');

                    // Calculate earnings for EACH ROOM in this booking WITH REFUND DEDUCTIONS
                    foreach ($booking->summaries as $summary) {
                        // Calculate per-room prices
                        $perRoomFacilityPrice = $summary->facility_price;
                        $perRoomBreakfastPrice = $summary->breakfast_price;
                        $roomBasePrice = $perRoomFacilityPrice + $perRoomBreakfastPrice;

                        // Apply payment logic per room
                        $roomEarnings = $this->applyPaymentLogicPerRoom(
                            $roomBasePrice,
                            $totalBookingPrice,
                            $totalAmountPaid,
                            $totalCheckinPaid
                        );

                        // DEDUCT REFUNDS for this specific room
                        $roomRefunds = $this->calculateRoomRefunds($booking, $summary->facility_id);
                        $roomNetEarnings = max(0, $roomEarnings - $roomRefunds);

                        $monthlyIncome += $roomNetEarnings;

                        Log::info('💰 Room Earnings with Refund Deduction', [
                            'month' => $month->month_year,
                            'room_id' => $summary->facility_id,
                            'room_earnings' => $roomEarnings,
                            'room_refunds' => $roomRefunds,
                            'room_net_earnings' => $roomNetEarnings
                        ]);
                    }
                }

                $monthlyData[] = [
                    'month' => $month->month_year,
                    'income' => $monthlyIncome
                ];

                Log::info('📊 Monthly Room Income (With Refund Deductions)', [
                    'month' => $month->month_year,
                    'total_income' => $monthlyIncome,
                    'bookings_processed' => $bookings->count()
                ]);
            }

            return collect($monthlyData);
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyRoomIncomeFixed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Calculate refunds for a specific room in a booking
     * (Same logic as in RoomBookReportController)
     */
    private function calculateRoomRefunds($booking, $roomId)
    {
        try {
            $totalRefunds = 0;

            // Get all refund payments for this booking
            $refundPayments = $booking->payments->where('refund_amount', '>', 0);

            if ($refundPayments->isEmpty()) {
                return 0;
            }

            $roomsInBooking = $booking->summaries->count();
            $roomsInBooking = max($roomsInBooking, 1);

            // Calculate total booking price for refund distribution
            $totalBookingPrice = $booking->details->sum('total_price');

            // Get this room's base price
            $roomSummary = $booking->summaries->where('facility_id', $roomId)->first();
            if (!$roomSummary) {
                return 0;
            }

            $roomBasePrice = $roomSummary->facility_price + $roomSummary->breakfast_price;

            // Calculate room's share of total booking price
            $roomShare = $totalBookingPrice > 0 ? ($roomBasePrice / $totalBookingPrice) : (1 / $roomsInBooking);

            // Apply room's share to each refund
            foreach ($refundPayments as $payment) {
                $roomRefundAmount = $payment->refund_amount * $roomShare;
                $totalRefunds += $roomRefundAmount;
            }

            return $totalRefunds;
        } catch (\Exception $e) {
            Log::error('❌ Error calculating room refunds', [
                'booking_id' => $booking->id,
                'room_id' => $roomId,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Apply payment logic per room (same as in your reference method)
     */
    private function applyPaymentLogicPerRoom($roomBasePrice, $bookingTotalPrice, $totalAmountPaid, $totalCheckinPaid)
    {
        if ($totalAmountPaid == 0) {
            return 0;
        }

        $totalPaymentsReceived = $totalAmountPaid + $totalCheckinPaid;

        Log::info('💳 Payment Rules', [
            'room_base_price' => $roomBasePrice,
            'booking_total_price' => $bookingTotalPrice,
            'amount_paid' => $totalAmountPaid,
            'checkin_paid' => $totalCheckinPaid,
            'total_payments' => $totalPaymentsReceived
        ]);

        // Full payment
        if ($totalPaymentsReceived >= $bookingTotalPrice) {
            Log::info('✅ Full payment - room earns full price');
            return $roomBasePrice;
        }

        // Half payment
        if ($totalAmountPaid == ($bookingTotalPrice / 2) && $totalCheckinPaid == 0) {
            Log::info('✅ Half payment - room earns half price');
            return $roomBasePrice / 2;
        }

        Log::warning('❌ Payment amount does not match full or half price');
        return 0;
    }

    /**
     * Day Tour Income (unchanged)
     */
    private function getMonthlyDayTourIncome()
    {
        try {
            return DB::table('day_tour_log_details')
                ->where('reservation_status', 'paid')
                ->select(
                    DB::raw('SUM(total_price) as total_income'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw("DATE_FORMAT(created_at, '%M %Y') as month_year")
                )
                ->groupBy('year', 'month', 'month_year')
                ->orderBy('year')->orderBy('month')
                ->get()
                ->map(fn($r) => ['month' => $r->month_year, 'income' => (float) $r->total_income]);
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyDayTourIncome: ' . $e->getMessage());
            return collect();
        }
    }

    private function combineMonthlyIncome($roomIncome, $dayTourIncome)
    {
        $allMonths = collect(array_merge(
            $roomIncome->pluck('month')->toArray(),
            $dayTourIncome->pluck('month')->toArray(),
        ))->unique()->sort();

        $combined = [];
        foreach ($allMonths as $month) {
            $combined[] = [
                'month' => $month,
                'room' => (float) ($roomIncome->firstWhere('month', $month)['income'] ?? 0),
                'daytour' => (float) ($dayTourIncome->firstWhere('month', $month)['income'] ?? 0),
            ];
        }

        return $totalEarnings;

    } catch (\Exception $e) {
        \Log::error("Error calculating room earnings for accounting (room $roomId): " . $e->getMessage());
        return 0;
    }

    /**
     * Get real-time accounting summary WITH REFUND DEDUCTIONS for rooms
     */
    public function accountingSummary()
    {
        try {
            $currentMonth = date('m');
            $currentYear = date('Y');

            // Current month room earnings using FIXED calculation WITH REFUND DEDUCTIONS
            $currentMonthRoomEarnings = $this->calculateCurrentMonthRoomEarningsFixed($currentMonth, $currentYear);

            // Current month day tour earnings
            $currentMonthDayTourEarnings = $this->calculateCurrentMonthDayTourEarnings($currentMonth, $currentYear);

            // Total received payments for rooms (all time)
            $totalRoomReceived = Payments::whereHas('bookingLog', function ($query) {
                $query->where('status', '!=', 'pending_confirmation')
                    ->whereHas('summaries.facility', function ($q) {
                        $q->where('type', 'room');
                    });
            })->sum(DB::raw('amount + checkin_paid'));

            // Total refunds issued for rooms (all time)
            $totalRoomRefunds = Payments::whereHas('bookingLog', function ($query) {
                $query->whereHas('summaries.facility', function ($q) {
                    $q->where('type', 'room');
                });
            })
                ->sum('refund_amount');

            // Total day tour received (all time)
            $totalDayTourReceived = DayTourLogDetails::where('reservation_status', 'paid')
                ->sum('total_price');

            // Pending payments for rooms
            $pendingRoomPayments = Payments::where('status', 'pending')
                ->whereHas('bookingLog', function ($query) {
                    $query->whereHas('summaries.facility', function ($q) {
                        $q->where('type', 'room');
                    });
                })
                ->sum(DB::raw('amount + checkin_paid'));

            return response()->json([
                'success' => true,
                'summary' => [
                    'current_month' => [
                        'room_earnings' => $currentMonthRoomEarnings,
                        'day_tour_earnings' => $currentMonthDayTourEarnings,
                        'total' => $currentMonthRoomEarnings + $currentMonthDayTourEarnings
                    ],
                    'all_time_totals' => [
                        'room_received' => $totalRoomReceived,
                        'room_refunds' => $totalRoomRefunds,
                        'room_net' => $totalRoomReceived - $totalRoomRefunds,
                        'day_tour_received' => $totalDayTourReceived,
                        'total_net_revenue' => ($totalRoomReceived - $totalRoomRefunds) + $totalDayTourReceived
                    ],
                    'pending_payments' => [
                        'room' => $pendingRoomPayments,
                        'total' => $pendingRoomPayments
                    ],
                    'currency' => '₱'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in accountingSummary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load accounting summary'
            ], 500);
        }
    }

    /**
     * FIXED: Calculate current month room earnings WITH REFUND DEDUCTIONS
     */
    private function calculateCurrentMonthRoomEarningsFixed($month, $year)
    {
        try {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $bookings = FacilityBookingLog::with(['payments', 'summaries', 'details'])
                ->whereHas('summaries', function ($query) {
                    $query->whereHas('facility', function ($q) {
                        $q->where('type', 'room');
                    });
                })
                ->whereHas('details', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('checkin_date', [$startDate, $endDate]);
                })
                ->where('status', '!=', 'pending_confirmation')
                ->get();

            $totalEarnings = 0;

            foreach ($bookings as $booking) {
                $roomsInBooking = $booking->summaries->count();
                $roomsInBooking = max($roomsInBooking, 1);

                // Calculate total booking price
                $totalBookingPrice = $booking->details->sum('total_price');

                // Get payment totals
                $totalAmountPaid = $booking->payments->sum('amount');
                $totalCheckinPaid = $booking->payments->sum('checkin_paid');

                // Calculate earnings for EACH ROOM in this booking WITH REFUND DEDUCTIONS
                foreach ($booking->summaries as $summary) {
                    // Calculate per-room prices
                    $perRoomFacilityPrice = $summary->facility_price;
                    $perRoomBreakfastPrice = $summary->breakfast_price;
                    $roomBasePrice = $perRoomFacilityPrice + $perRoomBreakfastPrice;

                    // Apply payment logic per room
                    $roomEarnings = $this->applyPaymentLogicPerRoom(
                        $roomBasePrice,
                        $totalBookingPrice,
                        $totalAmountPaid,
                        $totalCheckinPaid
                    );

                    // DEDUCT REFUNDS for this specific room
                    $roomRefunds = $this->calculateRoomRefunds($booking, $summary->facility_id);
                    $roomNetEarnings = max(0, $roomEarnings - $roomRefunds);

                    $totalEarnings += $roomNetEarnings;
                }
            }

            Log::info('💰 Current Month Room Earnings (With Refund Deductions)', [
                'month' => $month,
                'year' => $year,
                'total_earnings' => $totalEarnings
            ]);

            return $totalEarnings;
        } catch (\Exception $e) {
            Log::error('Error in calculateCurrentMonthRoomEarningsFixed: ' . $e->getMessage());
            return 0;
        }
    }

    private function calculateCurrentMonthDayTourEarnings($month, $year)
    {
        $dayTourIncome = $this->getMonthlyDayTourIncome();
        $currentMonth = Carbon::create($year, $month, 1)->format('F Y');

        return (float) ($dayTourIncome->firstWhere('month', $currentMonth)['income'] ?? 0);
    }

    public function export()
    {
        // Use the FIXED calculation for export WITH REFUND DEDUCTIONS for rooms
        $roomIncome = $this->getMonthlyRoomIncomeFixed();
        $dayTourIncome = $this->getMonthlyDayTourIncome();

        $combined = $this->combineMonthlyIncome($roomIncome, $dayTourIncome);
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
        // Header row
        fputcsv($handle, ['Month', 'Room Income', 'Day Tour Income', 'Total']);
        // Data rows
        foreach ($combined as $row) {
            $total = $row['room'] + $row['daytour'];
            fputcsv($handle, [
                $row['month'],
                $row['room'],
                $row['daytour'],
                $total,
            ]);
        }
        fclose($handle);
    });

    $filename = 'accounting_report_' . now()->format('Y_m_d_His') . '.csv';
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

    return $response;
}
}
