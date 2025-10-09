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
            // Use the FIXED calculation logic
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
                            'label' => 'Room Income',
                            'data' => array_column($combined, 'room'),
                            'borderColor' => 'rgba(54, 162, 235, 1)',
                            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        ],
                        [
                            'label' => 'Day Tour Income',
                            'data' => array_column($combined, 'daytour'),
                            'borderColor' => 'rgba(255, 206, 86, 1)',
                            'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
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
     * FIXED VERSION - Properly calculates monthly room income
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

                    // Calculate earnings for EACH ROOM in this booking (same logic as reference)
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

                        $monthlyIncome += $roomEarnings;
                    }
                }

                $monthlyData[] = [
                    'month' => $month->month_year,
                    'income' => $monthlyIncome
                ];

                Log::info('📊 Monthly Room Income (Fixed Per-Room)', [
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
     * ALTERNATIVE FIXED VERSION - Using the optimized SQL approach from RoomBookReportController
     */
    private function getMonthlyRoomIncomeOptimizedFixed()
    {
        try {
            $results = DB::table('facility_booking_log as log')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facilities as facility', 'summary.facility_id', '=', 'facility.id')
                ->join('facility_booking_details as details', 'log.id', '=', 'details.facility_booking_log_id')
                ->leftJoin('payments', 'log.id', '=', 'payments.facility_log_id')
                ->where('log.status', '!=', 'pending_confirmation')
                ->where('facility.type', 'room')
                ->select(
                    DB::raw('YEAR(details.checkin_date) as year'),
                    DB::raw('MONTH(details.checkin_date) as month'),
                    DB::raw("DATE_FORMAT(details.checkin_date, '%M %Y') as month_year"),
                    DB::raw('SUM(
                        CASE 
                            WHEN (COALESCE(SUM(payments.amount), 0) + COALESCE(SUM(payments.checkin_paid), 0)) >= details.total_price THEN 
                                (summary.facility_price + summary.breakfast_price)
                            WHEN (COALESCE(SUM(payments.amount), 0) + COALESCE(SUM(payments.checkin_paid), 0)) > 0 THEN 
                                (summary.facility_price + summary.breakfast_price) * 
                                ((COALESCE(SUM(payments.amount), 0) + COALESCE(SUM(payments.checkin_paid), 0)) / details.total_price)
                            ELSE 0
                        END
                    ) as total_income')
                )
                ->groupBy('year', 'month', 'month_year', 'log.id', 'summary.id', 'details.total_price')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            return $results->map(fn($r) => [
                'month' => $r->month_year,
                'income' => (float) $r->total_income
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMonthlyRoomIncomeOptimizedFixed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Apply payment logic per room (same as in your reference method)
     */
    // private function applyPaymentLogicPerRoom($roomBasePrice, $totalBookingPrice, $totalAmountPaid, $totalCheckinPaid)
    // {
    //     $totalPaymentsReceived = $totalAmountPaid + $totalCheckinPaid;
        
    //     if ($totalPaymentsReceived <= 0) {
    //         return 0;
    //     }
        
    //     if ($totalPaymentsReceived >= $totalBookingPrice) {
    //         // Fully paid - return full room base price
    //         return $roomBasePrice;
    //     }
        
    //     // Calculate payment ratio and apply to room base price
    //     $paymentRatio = $totalPaymentsReceived / $totalBookingPrice;
    //     $roomEarnings = $roomBasePrice * $paymentRatio;
        
    //     // Ensure we don't exceed room base price
    //     return min($roomEarnings, $roomBasePrice);
    // }
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
     * SIMPLIFIED FIXED VERSION - Based on actual payments received
     */
    private function getMonthlyRoomIncomeSimpleFixed()
    {
        try {
            $results = DB::table('payments')
                ->join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
                ->join('facility_booking_details as details', 'log.id', '=', 'details.facility_booking_log_id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->where('log.status', '!=', 'pending_confirmation')
                ->select(
                    DB::raw('YEAR(details.checkin_date) as year'),
                    DB::raw('MONTH(details.checkin_date) as month'),
                    DB::raw("DATE_FORMAT(details.checkin_date, '%M %Y') as month_year"),
                    DB::raw('SUM(
                        (payments.amount + payments.checkin_paid) * 
                        ((summary.facility_price + summary.breakfast_price) / details.total_price) /
                        GREATEST((SELECT COUNT(*) FROM facility_summary fs WHERE fs.facility_booking_log_id = log.id), 1)
                    ) as total_income')
                )
                ->groupBy('year', 'month', 'month_year')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            return $results->map(fn($r) => [
                'month' => $r->month_year,
                'income' => (float) $r->total_income
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyRoomIncomeSimpleFixed: ' . $e->getMessage());
            return collect();
        }
    }

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
     * Get real-time accounting summary using FIXED logic
     */
    public function accountingSummary()
    {
        try {
            $currentMonth = date('m');
            $currentYear = date('Y');

            // Current month room earnings using FIXED calculation
            $currentMonthRoomEarnings = $this->calculateCurrentMonthRoomEarningsFixed($currentMonth, $currentYear);

            // Current month day tour earnings
            $currentMonthDayTourEarnings = $this->calculateCurrentMonthDayTourEarnings($currentMonth, $currentYear);

            // Total received payments (all time)
            $totalReceived = Payments::whereHas('bookingLog', function ($query) {
                $query->where('status', '!=', 'pending_confirmation');
            })->sum(DB::raw('amount + checkin_paid'));

            // Pending payments
            $pendingPayments = Payments::where('status', 'pending')
                ->sum(DB::raw('amount + checkin_paid'));

            return response()->json([
                'success' => true,
                'summary' => [
                    'current_month' => [
                        'room' => $currentMonthRoomEarnings,
                        'day_tour' => $currentMonthDayTourEarnings,
                        'total' => $currentMonthRoomEarnings + $currentMonthDayTourEarnings
                    ],
                    'total_received' => $totalReceived,
                    'pending_payments' => $pendingPayments,
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
     * FIXED: Calculate current month room earnings
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

                // Calculate earnings for EACH ROOM in this booking
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

                    $totalEarnings += $roomEarnings;
                }
            }

            Log::info('💰 Current Month Room Earnings (Fixed Per-Room)', [
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
        // Use the FIXED calculation for export
        $roomIncome = $this->getMonthlyRoomIncomeFixed();
        $dayTourIncome = $this->getMonthlyDayTourIncome();

        $combined = $this->combineMonthlyIncome($roomIncome, $dayTourIncome);

        $response = new StreamedResponse(function () use ($combined) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, ['Month', 'Room Income', 'Day Tour Income', 'Total Income']);

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
     * TEST METHOD: Compare different calculation methods
     */
    public function testCalculationMethods(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $method1 = $this->getMonthlyRoomIncomeFixed();
            $method2 = $this->getMonthlyRoomIncomeOptimizedFixed();
            $method3 = $this->getMonthlyRoomIncomeSimpleFixed();

            $currentMonth = Carbon::create($year, $month, 1)->format('F Y');

            return response()->json([
                'success' => true,
                'comparison' => [
                    'detailed_method' => $method1->firstWhere('month', $currentMonth)['income'] ?? 0,
                    'optimized_method' => $method2->firstWhere('month', $currentMonth)['income'] ?? 0,
                    'simple_method' => $method3->firstWhere('month', $currentMonth)['income'] ?? 0,
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
