<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Payments;
use App\Models\FacilityBookingLog;
use App\Models\FacilityBookingDetails;
use App\Models\FacilitySummary;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class RoomBookReportController extends Controller
{
    public function index()
    {
        try {
            $categories = Facility::where('type', 'room')
                ->distinct()
                ->pluck('category');

            return view('admin.room_earnings_report.index', compact('categories'));
        } catch (\Exception $e) {
            \Log::error('Error in index: ' . $e->getMessage());
            return back()->with('error', 'Failed to load analytics page');
        }
    }

    public function getEarningsData(Request $request)
    {
        try {
            Log::info('=== ANALYTICS DEBUG START ===');

            $category = $request->input('category', '');
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            Log::info('Request parameters:', [
                'category' => $category,
                'month' => $month,
                'year' => $year
            ]);

            // Base query for rooms using Eloquent
            $roomsQuery = Facility::where('type', 'room');

            if (!empty($category)) {
                $roomsQuery->where('category', $category);
            }

            $rooms = $roomsQuery->get();
            Log::info('Found rooms: ' . $rooms->count());

            if ($rooms->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'earnings' => [],
                    'labels' => [],
                    'currency' => '₱',
                    'rooms' => [],
                    'categoryEarnings' => [],
                    'stats' => [
                        'totalEarnings' => 0,
                        'roomsBooked' => 0,
                        'topCategory' => '-',
                        'occupancyRate' => 0,
                        'totalBookings' => 0,
                        'comparison' => [
                            'earnings_change' => 0,
                            'bookings_change' => 0,
                            'occupancy_change' => 0
                        ]
                    ]
                ]);
            }

            $earningsData = [];
            $labels = [];
            $totalEarnings = 0;
            $roomsBooked = 0;
            $categoryEarnings = [];
            $roomDetails = [];
            $totalRooms = $rooms->count();
            $totalBookings = 0;

            foreach ($rooms as $room) {
                Log::info("Processing room: " . $room->name);

                // Calculate earnings for THIS SPECIFIC ROOM
                $roomEarnings = $this->calculateRoomEarnings($room->id, $month, $year);
                $roomBookingCount = $this->calculateRoomBookings($room->id, $month, $year);

                $earningsData[] = $roomEarnings;
                $labels[] = $room->name;
                $totalEarnings += $roomEarnings;

                if ($roomEarnings > 0) {
                    $roomsBooked++;
                }

                if (!isset($categoryEarnings[$room->category])) {
                    $categoryEarnings[$room->category] = 0;
                }
                $categoryEarnings[$room->category] += $roomEarnings;

                // Calculate room-specific stats
                $occupancyRate = $this->calculateRoomOccupancy($room->id, $month, $year);
                $averageRate = $roomBookingCount > 0 ? round($roomEarnings / $roomBookingCount, 2) : 0;

                $roomDetails[] = [
                    'id' => $room->id,
                    'name' => $room->name,
                    'category' => $room->category,
                    'earnings' => $roomEarnings,
                    'bookings' => $roomBookingCount,
                    'occupancy' => $occupancyRate,
                    'averageRate' => $averageRate,
                    'recentBookings' => $this->getRoomRecentBookings($room->id, $month, $year)
                ];

                $totalBookings += $roomBookingCount;
            }

            // Find top category
            $topCategory = '-';
            if (!empty($categoryEarnings)) {
                arsort($categoryEarnings);
                $topCategory = array_key_first($categoryEarnings);
            }

            // Calculate overall occupancy rate
            $totalNightsBooked = $this->calculateTotalNightsBooked($month, $year, $category);
            $totalAvailableNights = $this->calculateTotalAvailableNights($month, $year, $totalRooms);
            $occupancyRate = $totalAvailableNights > 0 ? round(($totalNightsBooked / $totalAvailableNights) * 100, 1) : 0;

            // Calculate comparison stats
            $comparisonStats = $this->getComparisonStats($month, $year, $category);

            $response = [
                'success' => true,
                'earnings' => $earningsData,
                'labels' => $labels,
                'currency' => '₱',
                'rooms' => $roomDetails,
                'categoryEarnings' => $categoryEarnings,
                'stats' => [
                    'totalEarnings' => $totalEarnings,
                    'roomsBooked' => $roomsBooked,
                    'topCategory' => $topCategory,
                    'occupancyRate' => $occupancyRate,
                    'totalBookings' => $totalBookings,
                    'comparison' => $comparisonStats
                ]
            ];

            Log::info('Response data prepared successfully');
            Log::info('Room Earnings Data:', $earningsData);
            Log::info('=== ANALYTICS DEBUG END ===');

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error in getEarningsData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => 'Failed to load earnings data: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    private function calculateRoomEarnings($roomId, $month, $year)
    {
        try {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            // Use Eloquent relationships
            $bookings = FacilityBookingLog::with(['payments', 'summaries', 'details'])
                ->whereHas('summaries', function ($query) use ($roomId) {
                    $query->where('facility_id', $roomId);
                })
                ->whereHas('details', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('checkin_date', [$startDate, $endDate]);
                })
                ->where('status', '!=', 'pending_confirmation')
                ->get();

            $totalEarnings = 0;

            Log::info('🧾 Calculating room earnings with Eloquent', [
                'room_id' => $roomId,
                'month' => $month,
                'year' => $year,
                'bookings_found' => $bookings->count(),
            ]);

            foreach ($bookings as $booking) {
                // Get the specific facility summary for this room
                $summaries = $booking->summaries
                    ->where('facility_id', $roomId)
                    ->first();

                if (!$summaries) {
                    continue;
                }

                $roomsInBooking = $booking->summaries->count();
                $roomsInBooking = max($roomsInBooking, 1);

                // Calculate per-room prices
                $perRoomFacilityPrice = $summaries->facility_price;
                $perRoomBreakfastPrice = $summaries->breakfast_price;
                $roomBasePrice = $perRoomFacilityPrice + $perRoomBreakfastPrice;

                // Get payment totals
                $totalAmountPaid = $booking->payments->sum('amount');
                $totalCheckinPaid = $booking->payments->sum('checkin_paid');
                $bookingTotalPrice = $booking->details->sum('total_price');


                // Apply payment logic
                $roomEarnings = $this->applyPaymentLogicPerRoom(
                    $roomBasePrice,
                    $bookingTotalPrice,
                    $totalAmountPaid,
                    $totalCheckinPaid
                );

                Log::info('💰 Booking Calculation - Eloquent', [
                    'booking_id' => $booking->id,
                    'rooms_in_booking' => $roomsInBooking,
                    'per_room_facility_price' => $perRoomFacilityPrice,
                    'per_room_breakfast_price' => $perRoomBreakfastPrice,
                    'room_base_price' => $roomBasePrice,
                    'booking_total_price' => $bookingTotalPrice,
                    'total_amount_paid' => $totalAmountPaid,
                    'total_checkin_paid' => $totalCheckinPaid,
                    'room_earnings' => $roomEarnings,
                ]);

                $totalEarnings += $roomEarnings;
            }

            Log::info('✅ Total earnings computed', [
                'room_id' => $roomId,
                'total_earnings' => $totalEarnings,
            ]);

            return $totalEarnings;
        } catch (\Exception $e) {
            Log::error('❌ Error calculating room earnings', [
                'room_id' => $roomId,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }


    /**
     * Calculate earnings for a specific room - FIXED VERSION
     * Properly handles multi-room bookings and payment distribution
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
     * Calculate number of bookings for a specific room
     */
    private function calculateRoomBookings($roomId, $month, $year)
    {
        try {
            $query = FacilityBookingLog::join('facility_summary as summary', 'facility_booking_log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'facility_booking_log.id', '=', 'details.facility_booking_log_id')
                ->where('summary.facility_id', $roomId)
                ->where('facility_booking_log.status', '!=', 'pending_confirmation');

            // Apply date filtering
            if ($month && $year) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                $query->whereBetween('details.checkin_date', [$startDate, $endDate]);
            }

            $bookingCount = $query->distinct()->count('facility_booking_log.id');

            return $bookingCount;
        } catch (\Exception $e) {
            \Log::error("Error calculating bookings for room $roomId: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate occupancy rate for a specific room
     */
    private function calculateRoomOccupancy($roomId, $month, $year)
    {
        try {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            // Calculate nights booked for this room - FIXED calculation
            $nightsBooked = FacilityBookingDetails::join('facility_booking_log as log', 'facility_booking_details.facility_booking_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->where('summary.facility_id', $roomId)
                ->where('log.status', '!=', 'pending_confirmation')
                ->whereBetween('checkin_date', [$startDate, $endDate])
                ->get()
                ->sum(function ($booking) {
                    // Proper night calculation
                    $checkin = Carbon::parse($booking->checkin_date);
                    $checkout = $booking->checkout_date ? Carbon::parse($booking->checkout_date) : $checkin->copy()->addDay();

                    // Calculate actual nights stayed
                    return $checkin->diffInDays($checkout);
                });

            $nightsBooked = $nightsBooked ?: 0;

            // Use exact days in month
            $availableNights = $startDate->daysInMonth;

            $occupancyRate = $availableNights > 0 ? round(($nightsBooked / $availableNights) * 100, 1) : 0;

            \Log::info("Room $roomId occupancy: $occupancyRate% ($nightsBooked / $availableNights nights)");

            return $occupancyRate;
        } catch (\Exception $e) {
            \Log::error("Error calculating occupancy for room $roomId: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get recent bookings for a specific room - FIXED VERSION
     */
    private function getRoomRecentBookings($roomId, $month, $year, $limit = 5)
    {
        try {
            $query = FacilityBookingLog::join('facility_summary as summary', 'facility_booking_log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'facility_booking_log.id', '=', 'details.facility_booking_log_id')
                ->where('summary.facility_id', $roomId)
                ->where('facility_booking_log.status', '!=', 'pending_confirmation');

            if ($month && $year) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                $query->whereBetween('details.checkin_date', [$startDate, $endDate]);
            }

            $bookings = $query->select(
                'details.checkin_date as date',
                'facility_booking_log.status',
                'summary.facility_price',
                'summary.breakfast_price',
                DB::raw('(SELECT COUNT(*) FROM facility_summary fs WHERE fs.facility_booking_log_id = facility_booking_log.id) as rooms_in_booking')
            )
                ->orderBy('details.checkin_date', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($booking) {
                    $roomsInBooking = $booking->rooms_in_booking ?: 1;
                    $perRoomEarnings = ($booking->facility_price + $booking->breakfast_price) / $roomsInBooking;

                    return [
                        'date' => Carbon::parse($booking->date)->format('M j, Y'),
                        'amount' => number_format($perRoomEarnings, 2),
                        'status' => $booking->status,
                        'rooms_in_booking' => $roomsInBooking // for debugging
                    ];
                })
                ->toArray();

            return $bookings;
        } catch (\Exception $e) {
            \Log::error("Error getting recent bookings for room $roomId: " . $e->getMessage());
            return [];
        }
    }

    private function calculateTotalNightsBooked($month, $year, $category)
    {
        try {
            $bookings = FacilityBookingDetails::join('facility_booking_log as log', 'facility_booking_details.facility_booking_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->where('log.status', '!=', 'pending_confirmation')
                ->when(!empty($category), function ($query) use ($category) {
                    $query->where('facilities.category', $category);
                })
                ->where(function ($query) use ($month, $year) {
                    if ($month && $year) {
                        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                        $query->whereBetween('facility_booking_details.checkin_date', [$startDate, $endDate]);
                    }
                })
                ->get();

            // Calculate total nights using proper date difference
            $totalNights = $bookings->sum(function ($booking) {
                $checkin = Carbon::parse($booking->checkin_date);
                $checkout = $booking->checkout_date ? Carbon::parse($booking->checkout_date) : $checkin->copy()->addDay();

                return $checkin->diffInDays($checkout);
            });

            \Log::info("Total nights booked: $totalNights for $month/$year, category: $category");

            return $totalNights;
        } catch (\Exception $e) {
            \Log::error("Error calculating total nights booked: " . $e->getMessage());
            return 0;
        }
    }

    private function calculateTotalAvailableNights($month, $year, $totalRooms)
    {
        try {
            $dateFilter = $this->getDateRange($month, $year);
            $daysInPeriod = $dateFilter['start']->diffInDays($dateFilter['end']);
            return $daysInPeriod * $totalRooms;
        } catch (\Exception $e) {
            \Log::error("Error calculating total available nights: " . $e->getMessage());
            return 30 * $totalRooms; // Fallback
        }
    }

    private function applyDateFilter($query, $month, $year)
    {
        $startDate = null;
        $endDate = null;

        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        \Log::info("Date filter applied: $startDate to $endDate");

        $query->whereBetween('details.checkin_date', [$startDate, $endDate]);

        return [
            'start' => $startDate,
            'end' => $endDate
        ];
    }

    private function getComparisonStats($month, $year, $category)
    {
        try {
            // Get current period stats using the new monthly calculation
            $currentEarnings = $this->calculateMonthlyEarnings($month, $year, $category);
            $currentBookings = $this->getMonthlyBookingsCount($month, $year, $category);

            // Get previous period
            $previousPeriod = $this->getPreviousPeriod($month, $year);
            $previousEarnings = $this->calculateMonthlyEarnings($previousPeriod['month'], $previousPeriod['year'], $category);
            $previousBookings = $this->getMonthlyBookingsCount($previousPeriod['month'], $previousPeriod['year'], $category);

            return [
                'earnings_change' => $this->calculatePercentageChange($currentEarnings, $previousEarnings),
                'bookings_change' => $this->calculatePercentageChange($currentBookings, $previousBookings),

            ];
        } catch (\Exception $e) {
            \Log::error("Error calculating comparison stats: " . $e->getMessage());
            return [
                'earnings_change' => 0,
                'bookings_change' => 0,
                'occupancy_change' => 0
            ];
        }
    }

    private function getMonthlyBookingsCount($month, $year, $category = null)
    {
        try {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $query = FacilityBookingLog::join('facility_summary as summary', 'facility_booking_log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'facility_booking_log.id', '=', 'details.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->where('facility_booking_log.status', '!=', 'pending_confirmation')
                ->whereBetween('details.checkin_date', [$startDate, $endDate])
                ->when($category, function ($q) use ($category) {
                    $q->where('facilities.category', $category);
                });

            return $query->distinct()->count('facility_booking_log.id');
        } catch (\Exception $e) {
            \Log::error("Error getting monthly bookings count: " . $e->getMessage());
            return 0;
        }
    }

    private function getPeriodStats($month, $year, $category)
    {
        try {
            $query = FacilityBookingLog::join('facility_summary as summary', 'facility_booking_log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'facility_booking_log.id', '=', 'details.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->where('facility_booking_log.status', '!=', 'pending_confirmation');

            if (!empty($category)) {
                $query->where('facilities.category', $category);
            }

            $this->applyDateFilter($query, $month, $year);

            // Use per-room calculation by dividing by number of rooms in booking
            $earnings = (float) $query->select(DB::raw('
                SUM(
                    (COALESCE(summary.facility_price, 0) + COALESCE(summary.breakfast_price, 0)) / 
                    GREATEST(
                        (SELECT COUNT(*) FROM facility_summary fs WHERE fs.facility_booking_log_id = facility_booking_log.id),
                        1
                    )
                ) as total_earnings
            '))->value('total_earnings');

            $bookings = $query->distinct()->count('facility_booking_log.id');

            // Calculate occupancy for the period
            $nightsBooked = $this->calculateTotalNightsBooked($month, $year, $category);
            $totalRooms = Facility::where('type', 'room')
                ->when(!empty($category), function ($q) use ($category) {
                    $q->where('category', $category);
                })->count();
            $availableNights = $this->calculateTotalAvailableNights($month, $year, $totalRooms);
            $occupancy = $availableNights > 0 ? ($nightsBooked / $availableNights) * 100 : 0;

            return [
                'earnings' => $earnings,
                'bookings' => $bookings,
                'occupancy' => $occupancy
            ];
        } catch (\Exception $e) {
            \Log::error("Error getting period stats: " . $e->getMessage());
            return [
                'earnings' => 0,
                'bookings' => 0,
                'occupancy' => 0
            ];
        }
    }

    private function getPreviousPeriod($month, $year)
    {
        $currentDate = Carbon::create($year, $month, 1);
        $previousDate = $currentDate->copy()->subMonth();

        return [
            'month' => $previousDate->month,
            'year' => $previousDate->year
        ];
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getDateRange($month, $year)
    {
        if ($month && $year) {
            return [
                'start' => Carbon::create($year, $month, 1)->startOfMonth(),
                'end' => Carbon::create($year, $month, 1)->endOfMonth()
            ];
        } else {
            return [
                'start' => Carbon::now()->startOfMonth(),
                'end' => Carbon::now()->endOfMonth()
            ];
        }
    }

    /**
     * Get earnings by category for pie chart
     */
    public function getCategoryEarnings(Request $request)
    {
        try {
            $category = $request->input('category', '');
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $categoryEarnings = $this->calculateCategoryEarnings($month, $year, $category);

            return response()->json([
                'success' => true,
                'categoryEarnings' => $categoryEarnings
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getCategoryEarnings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load category earnings data'
            ], 500);
        }
    }

    /**
     * Calculate earnings by category - UPDATED to handle multiple rooms
     */
    private function calculateCategoryEarnings($month, $year, $filterCategory = '')
    {
        $categories = Facility::where('type', 'room')
            ->when($filterCategory, function ($query) use ($filterCategory) {
                $query->where('category', $filterCategory);
            })
            ->distinct()
            ->pluck('category');

        $categoryEarnings = [];

        foreach ($categories as $category) {
            $earnings = FacilityBookingLog::join('facility_summary as summary', 'facility_booking_log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'facility_booking_log.id', '=', 'details.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->where('facilities.category', $category)
                ->where('facility_booking_log.status', '!=', 'pending_confirmation');

            // Apply date filtering
            if ($month && $year) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                $earnings->whereBetween('details.checkin_date', [$startDate, $endDate]);
            }

            // Calculate per-room earnings by dividing by number of rooms in booking
            $totalEarnings = (float) $earnings->select(DB::raw('
                SUM(
                    (COALESCE(summary.facility_price, 0) + COALESCE(summary.breakfast_price, 0)) / 
                    GREATEST(
                        (SELECT COUNT(*) FROM facility_summary fs WHERE fs.facility_booking_log_id = facility_booking_log.id),
                        1
                    )
                ) as total_earnings
            '))->value('total_earnings');

            if ($totalEarnings > 0) {
                $categoryEarnings[$category] = $totalEarnings;
            }
        }

        // Sort by earnings descending
        arsort($categoryEarnings);

        return $categoryEarnings;
    }

    /**
     * Get comparison data for year-over-year analysis
     */
    public function getComparisonData(Request $request)
    {
        try {
            $category = $request->input('category', null);
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $currentYear = $year;
            $data = [];

            // Compare current year with previous 2 years
            for ($i = 0; $i < 3; $i++) {
                $compareYear = $currentYear - $i;
                $yearData = $this->getYearlyMonthlyData($compareYear, $category);
                $data[$compareYear] = $yearData;
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getComparisonData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getYearlyMonthlyData($year, $category)
    {
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlyEarnings = $this->calculateMonthlyEarnings($month, $year, $category);
            $monthlyData[] = $monthlyEarnings;
        }

        return $monthlyData;
    }

    private function calculateMonthlyEarnings($month, $year, $category = null)
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
                ->when($category, function ($query) use ($category) {
                    $query->whereHas('summaries.facility', function ($q) use ($category) {
                        $q->where('category', $category);
                    });
                })
                ->get();

            $totalMonthlyEarnings = 0;

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
                    // Calculate per-room prices (same logic as room-level calculation)
                    $perRoomFacilityPrice = $summary->facility_price;
                    $perRoomBreakfastPrice = $summary->breakfast_price;
                    $roomBasePrice = $perRoomFacilityPrice + $perRoomBreakfastPrice;

                    // Apply the SAME payment logic as room-level calculation
                    $roomEarnings = $this->applyPaymentLogicPerRoom(
                        $roomBasePrice,
                        $totalBookingPrice,
                        $totalAmountPaid,
                        $totalCheckinPaid
                    );

                    $totalMonthlyEarnings += $roomEarnings;
                }
            }

            \Log::info("Monthly earnings calculated", [
                'month' => $month,
                'year' => $year,
                'category' => $category,
                'total_earnings' => $totalMonthlyEarnings,
                'bookings_count' => $bookings->count()
            ]);

            return $totalMonthlyEarnings;
        } catch (\Exception $e) {
            \Log::error("Error calculating monthly earnings: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Export earnings data
     */
    public function exportEarningsData(Request $request)
    {
        try {
            $category = $request->input('category', null);
            $month = $request->input('month', null);
            $year = $request->input('year', null);

            // Get the data
            $data = $this->getExportData($category, $month, $year);

            // Generate CSV content with proper formatting for Excel
            $csvData = $this->generateExcelCompatibleCsv($data);

            $filename = "room_earnings_" . date('Y-m-d') . ".csv";

            return response($csvData)
                ->header('Content-Type', 'text/csv; charset=utf-8')
                ->header('Content-Disposition', "attachment; filename=\"$filename\"")
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    private function getExportData($category, $month, $year)
    {
        $roomsQuery = Facility::where('type', 'room');

        if ($category) {
            $roomsQuery->where('category', $category);
        }

        $rooms = $roomsQuery->get();
        $exportData = [];

        // Add header row
        $exportData[] = [
            'Room Name',
            'Category',
            'Earnings',
            'Bookings',
            'Occupancy Rate',
            'Average Daily Rate'
        ];

        foreach ($rooms as $room) {
            $roomEarnings = $this->calculateRoomEarnings($room->id, $month, $year);
            $roomBookings = $this->calculateRoomBookings($room->id, $month, $year);
            $occupancyRate = $this->calculateRoomOccupancy($room->id, $month, $year);
            $averageRate = $roomBookings > 0 ? round($roomEarnings / $roomBookings, 2) : 0;

            $exportData[] = [
                'Room Name' => $room->name,
                'Category' => $room->category,
                'Earnings' => $roomEarnings,
                'Bookings' => $roomBookings,
                'Occupancy Rate' => $occupancyRate,
                'Average Daily Rate' => $averageRate
            ];
        }

        return $exportData;
    }

    private function generateExcelCompatibleCsv($data)
    {
        $output = fopen('php://temp', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");

        foreach ($data as $row) {
            // Format numbers properly for Excel
            $formattedRow = [];
            foreach ($row as $index => $value) {
                if (is_numeric($value) && $index > 1) { // Skip first two columns (text fields)
                    // For numeric values, ensure they're formatted as plain numbers
                    $formattedRow[] = $value;
                } else {
                    // For text fields, enclose in quotes if they contain commas
                    if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
                        $value = '"' . str_replace('"', '""', $value) . '"';
                    }
                    $formattedRow[] = $value;
                }
            }
            fputcsv($output, $formattedRow);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    private function generateCsv($data)
    {
        $output = fopen('php://temp', 'w');

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Get available years for filter dropdown
     */
    public function getAvailableYears()
    {
        try {
            $years = Payments::join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
                ->join('facility_booking_details as details', 'log.id', '=', 'details.facility_booking_log_id')
                ->where('log.status', '!=', 'pending_confirmation')
                ->whereNotNull('details.checkin_date')
                ->select(DB::raw('YEAR(details.checkin_date) as year'))
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->filter()
                ->values();

            return response()->json([
                'success' => true,
                'years' => $years
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
