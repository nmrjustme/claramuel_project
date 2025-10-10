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
use Illuminate\Support\Facades\Log;

class AccountingController extends Controller
{
    public function index()
    {
        return view('admin.accounting.index');
    }

    public function monthlyIncomeApi()
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
                            'borderColor' => 'rgba(255, 0, 0, 1)', // solid red border
                            'backgroundColor' => 'rgba(255, 0, 0, 0.8)', // slightly transparent red fill
                        ],
                        [
                            'label' => 'Day Tour Income',
                            'data' => array_column($combined, 'daytour'),
                            'borderColor' => '#D9C6B2', // beige/yellow border
                            'backgroundColor' => 'rgba(217, 198, 178, 0.8)', // same color with 80% opacity
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
        return $combined;
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

        $response = new StreamedResponse(function () use ($combined) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, ['Month', 'Room Income (Net of Refunds)', 'Day Tour Income', 'Total Income']);

            // Data rows
            foreach ($combined as $row) {
                $total = $row['room'] + $row['daytour'];
                fputcsv($handle, [
                    $row['month'],
                    number_format($row['room'], 2),
                    number_format($row['daytour'], 2),
                    number_format($total, 2),
                ]);
            }
            fclose($handle);
        });

        $filename = 'accounting_report_' . now()->format('Y_m_d_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * Get refund analytics for rooms
     */
    public function getRefundAnalytics(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $refundStats = $this->getRoomRefundStats($month, $year);

            return response()->json([
                'success' => true,
                'refund_analytics' => $refundStats
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getRefundAnalytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load refund analytics'
            ], 500);
        }
    }

    /**
     * Get room refund statistics
     */
    private function getRoomRefundStats($month, $year)
    {
        try {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $refundStats = Payments::join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->whereBetween('payments.refund_date', [$startDate, $endDate])
                ->select(
                    DB::raw('SUM(refund_amount) as total_refunds'),
                    DB::raw('COUNT(*) as refund_count'),
                    DB::raw('AVG(refund_amount) as average_refund'),
                    DB::raw('COUNT(DISTINCT log.id) as affected_bookings')
                )
                ->first();

            // Calculate total room revenue for this period to get refund rate
            $totalRoomRevenue = $this->calculateCurrentMonthRoomEarningsFixed($month, $year);
            $refundRate = $totalRoomRevenue > 0 ? ($refundStats->total_refunds / $totalRoomRevenue) * 100 : 0;

            return [
                'total_refunds' => $refundStats->total_refunds ?? 0,
                'refund_count' => $refundStats->refund_count ?? 0,
                'average_refund' => $refundStats->average_refund ?? 0,
                'affected_bookings' => $refundStats->affected_bookings ?? 0,
                'refund_rate' => round($refundRate, 2),
                'total_room_revenue' => $totalRoomRevenue
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRoomRefundStats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * TEST METHOD: Compare different calculation methods
     */
    public function testCalculationMethods(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $method1 = $this->getMonthlyRoomIncomeFixed();

            $currentMonth = Carbon::create($year, $month, 1)->format('F Y');

            return response()->json([
                'success' => true,
                'comparison' => [
                    'detailed_method_with_refunds' => $method1->firstWhere('month', $currentMonth)['income'] ?? 0,
                ],
                'current_month' => $currentMonth
            ]);
        } catch (\Exception $e) {
            Log::error('Error in testCalculationMethods: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to compare calculation methods'
            ], 500);
        }
    }
}