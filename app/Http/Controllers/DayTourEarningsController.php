<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DayTourEarningsController extends Controller
{
    public function index()
{
    // Get actual facility names from the database
    $facilities = DB::table('facilities')
        ->where('type', 'day-tour')
        ->where('category', 'Cottage')
        ->pluck('name')
        ->toArray();
    
    $categories = ['Pool', 'Park', 'Both'];
    
    return view('admin.room_earnings_report.daytour-earnings', compact('categories', 'facilities'));
}

    public function getAnalyticsData(Request $request)
    {
        try {
            $category = $request->get('category');
            $facilityType = $request->get('facility_type');
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));

            Log::info('Fetching analytics data', [
                'month' => $month,
                'year' => $year,
                'category' => $category,
                'facilityType' => $facilityType
            ]);

            // Get all the data
            $revenueData = $this->getRevenueData($month, $year, $category, $facilityType);
            $guestData = $this->getGuestDemographics($month, $year, $category, $facilityType);
            $categoryData = $this->getCategoryDistribution($month, $year, $facilityType);
            $facilityData = $this->getFacilityUtilization($month, $year, $category);
            $stats = $this->getStatistics($month, $year, $category, $facilityType);
            $recentBookings = $this->getRecentBookings($month, $year, $category, $facilityType);
            $bookings = $this->getDetailedBookings($month, $year, $category, $facilityType);

            $data = [
                'success' => true,
                'revenue_data' => $revenueData['data'],
                'labels' => $revenueData['labels'],
                'guest_data' => $guestData,
                'category_data' => $categoryData,
                'facility_data' => $facilityData,
                'stats' => $stats,
                'recent_bookings' => $recentBookings,
                'bookings' => $bookings,
            ];

            Log::info('Analytics data fetched successfully');

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('Error loading analytics data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getRevenueData($month, $year, $category = null, $facilityType = null)
{
    try {
        $query = DB::table('day_tour_log_details as dt')
            ->select(
                DB::raw('DATE(dt.date_tour) as date'),
                DB::raw('SUM(dt.total_price) as revenue')
            )
            ->where('dt.reservation_status', 'paid')
            ->whereMonth('dt.date_tour', $month)
            ->whereYear('dt.date_tour', $year);

        // Fix for "Both" category - only bookings that have BOTH Pool AND Park
        if ($category === 'Both') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNull('bgd.facility_id')
                    ->where('gt.location', 'Pool');
            })->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNull('bgd.facility_id')
                    ->where('gt.location', 'Park');
            });
        } elseif ($category) {
            $query->whereExists(function ($subQuery) use ($category) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->where('gt.location', $category)
                    ->whereNull('bgd.facility_id');
            });
        }

        // ... facility filter logic remains the same
        if ($facilityType && $facilityType !== 'None') {
            $query->whereExists(function ($subQuery) use ($facilityType) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('facilities as f', 'bgd.facility_id', '=', 'f.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->where('f.name', 'like', '%' . $facilityType . '%');
            });
        } elseif ($facilityType === 'None') {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNotNull('bgd.facility_id');
            });
        }

        $results = $query->groupBy(DB::raw('DATE(dt.date_tour)'))
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $labels[] = $day;
            
            $revenue = $results->firstWhere('date', $date);
            $data[] = $revenue ? (float)$revenue->revenue : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];

    } catch (\Exception $e) {
        Log::error('Error in getRevenueData: ' . $e->getMessage());
        return ['labels' => [], 'data' => []];
    }
}

    private function getGuestDemographics($month, $year, $category = null, $facilityType = null)
{
    try {
        $query = DB::table('booking_guest_details as bgd')
            ->select(
                'gt.type',
                'gt.location',
                DB::raw('SUM(bgd.quantity) as guest_count')
            )
            ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
            ->join('day_tour_log_details as dt', 'bgd.day_tour_log_details_id', '=', 'dt.id')
            ->where('dt.reservation_status', 'paid')
            ->whereMonth('dt.date_tour', $month)
            ->whereYear('dt.date_tour', $year)
            ->whereNull('bgd.facility_id')
            ->where('bgd.quantity', '>', 0);

        // Fix for "Both" category - only bookings that have BOTH Pool AND Park guests
        if ($category === 'Both') {
            $query->whereIn('dt.id', function ($subQuery) {
                $subQuery->select('dt2.id')
                    ->from('day_tour_log_details as dt2')
                    ->whereExists(function ($subQuery2) {
                        $subQuery2->select(DB::raw(1))
                            ->from('booking_guest_details as bgd2')
                            ->join('guest_type as gt2', 'bgd2.guest_type_id', '=', 'gt2.id')
                            ->whereColumn('bgd2.day_tour_log_details_id', 'dt2.id')
                            ->whereNull('bgd2.facility_id')
                            ->where('gt2.location', 'Pool');
                    })
                    ->whereExists(function ($subQuery2) {
                        $subQuery2->select(DB::raw(1))
                            ->from('booking_guest_details as bgd2')
                            ->join('guest_type as gt2', 'bgd2.guest_type_id', '=', 'gt2.id')
                            ->whereColumn('bgd2.day_tour_log_details_id', 'dt2.id')
                            ->whereNull('bgd2.facility_id')
                            ->where('gt2.location', 'Park');
                    });
            });
        } elseif ($category) {
            $query->where('gt.location', $category);
        }

            // Apply facility filter
            if ($facilityType && $facilityType !== 'None') {
                $query->whereExists(function ($subQuery) use ($facilityType) {
                    $subQuery->select(DB::raw(1))
                        ->from('booking_guest_details as bgd2')
                        ->join('facilities as f', 'bgd2.facility_id', '=', 'f.id')
                        ->whereColumn('bgd2.day_tour_log_details_id', 'dt.id')
                        ->where('f.name', 'like', '%' . $facilityType . '%');
                });
            } elseif ($facilityType === 'None') {
                $query->whereNotExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('booking_guest_details as bgd2')
                        ->whereColumn('bgd2.day_tour_log_details_id', 'dt.id')
                        ->whereNotNull('bgd2.facility_id');
                });
            }

            $results = $query->groupBy('gt.type', 'gt.location')
                ->orderBy('gt.location')
                ->orderBy('gt.type')
                ->get();

            $labels = [];
            $data = [];

            foreach ($results as $result) {
                $labels[] = $result->type . ' (' . $result->location . ')';
                $data[] = (int)$result->guest_count;
            }

            return [
                'labels' => $labels,
                'data' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Error in getGuestDemographics: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }
    }

    private function getCategoryDistribution($month, $year, $facilityType = null)
{
    try {
        $query = DB::table('booking_guest_details as bgd')
            ->select(
                'gt.location as category',
                DB::raw('SUM(bgd.quantity * gt.rate) as revenue')
            )
            ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
            ->join('day_tour_log_details as dt', 'bgd.day_tour_log_details_id', '=', 'dt.id')
            ->where('dt.reservation_status', 'paid')
            ->whereMonth('dt.date_tour', $month)
            ->whereYear('dt.date_tour', $year)
            ->whereNull('bgd.facility_id')
            ->where('bgd.quantity', '>', 0);

        // SIMPLIFIED: Remove category filter for distribution chart
        // The distribution chart should show ALL categories regardless of filter
        // Only apply facility filter
        if ($facilityType && $facilityType !== 'None') {
            $query->whereExists(function ($subQuery) use ($facilityType) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd2')
                    ->join('facilities as f', 'bgd2.facility_id', '=', 'f.id')
                    ->whereColumn('bgd2.day_tour_log_details_id', 'dt.id')
                    ->where('f.name', 'like', '%' . $facilityType . '%');
            });
        } elseif ($facilityType === 'None') {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd2')
                    ->whereColumn('bgd2.day_tour_log_details_id', 'dt.id')
                    ->whereNotNull('bgd2.facility_id');
            });
        }

        $results = $query->groupBy('gt.location')
            ->get();

        $categoryData = [];
        foreach ($results as $result) {
            $categoryData[$result->category] = (float)$result->revenue;
        }

        // If no data found, return empty array or sample data for debugging
        if (empty($categoryData)) {
            Log::warning('No category distribution data found', [
                'month' => $month,
                'year' => $year,
                'facilityType' => $facilityType
            ]);
            
            // For debugging, you can return sample data:
            // return ['Pool' => 45000.00, 'Park' => 19990.00];
        }

        return $categoryData;

    } catch (\Exception $e) {
        Log::error('Error in getCategoryDistribution: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return [];
    }
}

private function getFacilityUtilization($month, $year, $category = null)
{
    try {
        $query = DB::table('booking_guest_details as bgd')
            ->select(
                'f.name as facility_name',
                DB::raw('COUNT(DISTINCT dt.id) as booking_count'),
                DB::raw('SUM(bgd.facility_quantity) as total_quantity')
            )
            ->join('day_tour_log_details as dt', 'bgd.day_tour_log_details_id', '=', 'dt.id')
            ->join('facilities as f', 'bgd.facility_id', '=', 'f.id')
            ->where('dt.reservation_status', 'paid')
            ->whereMonth('dt.date_tour', $month)
            ->whereYear('dt.date_tour', $year)
            ->whereNotNull('bgd.facility_id')
            ->where('bgd.facility_quantity', '>', 0); // Only count actual facility bookings

        if ($category) {
            $query->whereExists(function ($subQuery) use ($category) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd2')
                    ->join('guest_type as gt', 'bgd2.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd2.day_tour_log_details_id', 'dt.id')
                    ->where('gt.location', $category)
                    ->whereNull('bgd2.facility_id');
            });
        }

        $results = $query->groupBy('f.name')
            ->orderByDesc('booking_count')
            ->get();

        $labels = [];
        $data = [];

        foreach ($results as $result) {
            $labels[] = $result->facility_name . ' (' . $result->total_quantity . ')';
            $data[] = (int)$result->booking_count;
        }

        // Add "No Facility" option
        $noFacilityQuery = DB::table('day_tour_log_details as dt')
            ->select(DB::raw('COUNT(*) as booking_count'))
            ->where('dt.reservation_status', 'paid')
            ->whereMonth('dt.date_tour', $month)
            ->whereYear('dt.date_tour', $year)
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNotNull('bgd.facility_id')
                    ->where('bgd.facility_quantity', '>', 0);
            });

        if ($category) {
            $noFacilityQuery->whereExists(function ($subQuery) use ($category) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->where('gt.location', $category)
                    ->whereNull('bgd.facility_id');
            });
        }

        $noFacilityCount = $noFacilityQuery->count();

        if ($noFacilityCount > 0) {
            $labels[] = 'No Facility';
            $data[] = (int)$noFacilityCount;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];

    } catch (\Exception $e) {
        Log::error('Error in getFacilityUtilization: ' . $e->getMessage());
        return ['labels' => [], 'data' => []];
    }
}

private function getStatistics($month, $year, $category = null, $facilityType = null)
{
    try {
        // Get current period stats
        $currentStats = $this->calculateCurrentStats($month, $year, $category, $facilityType);
        
        // Get previous period stats (simplified)
        $previousStats = $this->getPreviousPeriodStats($month, $year, $category, $facilityType);
        
        // Calculate simple percentage changes
        $changes = [
            'revenue_change' => $this->calculateSimpleChange($currentStats['total_revenue'], $previousStats['revenue']),
            'bookings_change' => $this->calculateSimpleChange($currentStats['total_bookings'], $previousStats['bookings']),
            'guests_change' => $this->calculateSimpleChange($currentStats['total_guests'], $previousStats['guests'])
        ];

        return array_merge($currentStats, $changes);

    } catch (\Exception $e) {
        Log::error('Error in getStatistics: ' . $e->getMessage());
        return $this->getDefaultStats();
    }
}

private function calculateCurrentStats($month, $year, $category, $facilityType)
{
    // Total Revenue
    $revenueQuery = DB::table('day_tour_log_details as dt')
        ->where('dt.reservation_status', 'paid')
        ->whereMonth('dt.date_tour', $month)
        ->whereYear('dt.date_tour', $year);

    // Apply filters
    $this->applyFilters($revenueQuery, $category, $facilityType);
    $totalRevenue = $revenueQuery->sum('dt.total_price');

    // Total Bookings (same query, just count)
    $totalBookings = $revenueQuery->count();

    // Total Guests
    $guestsQuery = DB::table('booking_guest_details as bgd')
        ->join('day_tour_log_details as dt', 'bgd.day_tour_log_details_id', '=', 'dt.id')
        ->where('dt.reservation_status', 'paid')
        ->whereMonth('dt.date_tour', $month)
        ->whereYear('dt.date_tour', $year)
        ->whereNull('bgd.facility_id')
        ->where('bgd.quantity', '>', 0);

    $this->applyFilters($guestsQuery, $category, $facilityType, true);
    $totalGuests = $guestsQuery->sum('bgd.quantity');

    // Top Category
    $topCategory = $this->getTopCategory($month, $year, $category, $facilityType);

    return [
        'total_revenue' => (float)$totalRevenue,
        'total_bookings' => (int)$totalBookings,
        'total_guests' => (int)$totalGuests,
        'top_category' => $topCategory
    ];
}

private function applyFilters($query, $category, $facilityType, $isGuestQuery = false)
{
    // Category filter
    if ($category === 'Both') {
        $query->whereExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('booking_guest_details as bgd')
                ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                ->whereNull('bgd.facility_id')
                ->where('gt.location', 'Pool');
        })->whereExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('booking_guest_details as bgd')
                ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                ->whereNull('bgd.facility_id')
                ->where('gt.location', 'Park');
        });
    } elseif ($category) {
        $query->whereExists(function ($subQuery) use ($category, $isGuestQuery) {
            $subQuery->select(DB::raw(1))
                ->from('booking_guest_details as bgd')
                ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                ->where('gt.location', $category)
                ->whereNull('bgd.facility_id');
        });
    }

    // Facility filter
    if ($facilityType && $facilityType !== 'None') {
        $query->whereExists(function ($subQuery) use ($facilityType) {
            $subQuery->select(DB::raw(1))
                ->from('booking_guest_details as bgd')
                ->join('facilities as f', 'bgd.facility_id', '=', 'f.id')
                ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                ->where('f.name', 'like', '%' . $facilityType . '%');
        });
    } elseif ($facilityType === 'None') {
        $query->whereNotExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('booking_guest_details as bgd')
                ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                ->whereNotNull('bgd.facility_id');
        });
    }
}

private function getPreviousPeriodStats($month, $year, $category, $facilityType)
{
    // Calculate previous month
    $currentDate = date("$year-$month-01");
    $prevDate = date('Y-m-d', strtotime('-1 month', strtotime($currentDate)));
    $prevMonth = date('m', strtotime($prevDate));
    $prevYear = date('Y', strtotime($prevDate));

    // Revenue for previous period
    $revenueQuery = DB::table('day_tour_log_details as dt')
        ->where('dt.reservation_status', 'paid')
        ->whereMonth('dt.date_tour', $prevMonth)
        ->whereYear('dt.date_tour', $prevYear);

    $this->applyFilters($revenueQuery, $category, $facilityType);
    $prevRevenue = $revenueQuery->sum('dt.total_price');

    // Bookings for previous period
    $prevBookings = $revenueQuery->count();

    // Guests for previous period
    $guestsQuery = DB::table('booking_guest_details as bgd')
        ->join('day_tour_log_details as dt', 'bgd.day_tour_log_details_id', '=', 'dt.id')
        ->where('dt.reservation_status', 'paid')
        ->whereMonth('dt.date_tour', $prevMonth)
        ->whereYear('dt.date_tour', $prevYear)
        ->whereNull('bgd.facility_id')
        ->where('bgd.quantity', '>', 0);

    $this->applyFilters($guestsQuery, $category, $facilityType, true);
    $prevGuests = $guestsQuery->sum('bgd.quantity');

    return [
        'revenue' => (float)$prevRevenue,
        'bookings' => (int)$prevBookings,
        'guests' => (int)$prevGuests
    ];
}

private function calculateSimpleChange($current, $previous)
{
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    
    return (($current - $previous) / $previous) * 100;
}

private function getTopCategory($month, $year, $category, $facilityType)
{
    if ($category === 'Both') {
        return 'Both';
    } elseif ($category) {
        return $category;
    }

    // Find actual top category
    $topCategoryQuery = DB::table('booking_guest_details as bgd')
        ->select(
            'gt.location as category',
            DB::raw('COUNT(DISTINCT dt.id) as booking_count')
        )
        ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
        ->join('day_tour_log_details as dt', 'bgd.day_tour_log_details_id', '=', 'dt.id')
        ->where('dt.reservation_status', 'paid')
        ->whereMonth('dt.date_tour', $month)
        ->whereYear('dt.date_tour', $year)
        ->whereNull('bgd.facility_id')
        ->where('bgd.quantity', '>', 0)
        ->groupBy('gt.location')
        ->orderByDesc('booking_count')
        ->first();

    return $topCategoryQuery ? $topCategoryQuery->category : 'N/A';
}

private function getDefaultStats()
{
    return [
        'total_revenue' => 0,
        'total_bookings' => 0,
        'total_guests' => 0,
        'top_category' => 'N/A',
        'revenue_change' => 0,
        'bookings_change' => 0,
        'guests_change' => 0
    ];
}

    private function getRecentBookings($month, $year, $category = null, $facilityType = null)
{
    try {
        $query = DB::table('day_tour_log_details as dt')
            ->select(
                'dt.id',
                'dt.date_tour as date',
                'dt.total_price as amount',
                'dt.reservation_status as status',
                DB::raw('(SELECT SUM(quantity) FROM booking_guest_details WHERE day_tour_log_details_id = dt.id AND facility_id IS NULL) as guests'),
                'u.firstname',
                'u.lastname',
                'u.phone',
                // Check if facility exists for this booking
                DB::raw('EXISTS (SELECT 1 FROM booking_guest_details WHERE day_tour_log_details_id = dt.id AND facility_id IS NOT NULL) as has_facility')
            )
            ->leftJoin('users as u', 'dt.user_id', '=', 'u.id')
            ->where('dt.reservation_status', 'paid')
            ->whereMonth('dt.date_tour', $month)
            ->whereYear('dt.date_tour', $year);

        if ($category) {
            $query->whereExists(function ($subQuery) use ($category) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->where('gt.location', $category)
                    ->whereNull('bgd.facility_id');
            });
        }

        if ($facilityType && $facilityType !== 'None') {
            $query->whereExists(function ($subQuery) use ($facilityType) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('facilities as f', 'bgd.facility_id', '=', 'f.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->where('f.name', 'like', '%' . $facilityType . '%');
            });
        } elseif ($facilityType === 'None') {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNotNull('bgd.facility_id');
            });
        }

        $results = $query->orderByDesc('dt.date_tour')
            ->limit(10)
            ->get();

        return $results->map(function ($booking) {
            $customerName = trim($booking->firstname . ' ' . $booking->lastname);
            $customerName = $customerName ?: 'Unknown Customer';
            
            return [
                'id' => $booking->id,
                'date' => $booking->date,
                'amount' => (float)$booking->amount,
                'status' => $booking->status,
                'guests' => (int)$booking->guests,
                'customer_name' => $customerName,
                'phone' => $booking->phone,
                'has_facility' => (bool)$booking->has_facility // Add this flag
            ];
        })->toArray();

    } catch (\Exception $e) {
        Log::error('Error in getRecentBookings: ' . $e->getMessage());
        return [];
    }
}
    private function getDetailedBookings($month, $year, $category = null, $facilityType = null)
{
    try {
        $query = DB::table('day_tour_log_details as dt')
            ->select(
                'dt.id',
                DB::raw('CONCAT("DT-", dt.id) as reference'),
                'dt.date_tour as date',
                'dt.total_price as amount',
                'dt.reservation_status as status',
                DB::raw('(SELECT SUM(quantity) FROM booking_guest_details WHERE day_tour_log_details_id = dt.id AND facility_id IS NULL) as guest_count'),
                'u.firstname',
                'u.lastname',
                'u.phone',
                'u.email'
            )
            ->leftJoin('users as u', 'dt.user_id', '=', 'u.id')
            ->where('dt.reservation_status', 'paid')
            ->whereMonth('dt.date_tour', $month)
            ->whereYear('dt.date_tour', $year);

        if ($category) {
            $query->whereExists(function ($subQuery) use ($category) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->where('gt.location', $category)
                    ->whereNull('bgd.facility_id');
            });
        }

        if ($facilityType && $facilityType !== 'None') {
            $query->whereExists(function ($subQuery) use ($facilityType) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->join('facilities as f', 'bgd.facility_id', '=', 'f.id')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->where('f.name', 'like', '%' . $facilityType . '%');
            });
        } elseif ($facilityType === 'None') {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('booking_guest_details as bgd')
                    ->whereColumn('bgd.day_tour_log_details_id', 'dt.id')
                    ->whereNotNull('bgd.facility_id');
            });
        }

        $results = $query->orderByDesc('dt.date_tour')
            ->get();

        $bookings = $results->map(function ($booking) {
            // Get guest breakdown (actual guests)
            $guestBreakdown = DB::table('booking_guest_details as bgd')
                ->select(
                    'gt.type',
                    'gt.location',
                    'gt.rate',
                    DB::raw('SUM(bgd.quantity) as count')
                )
                ->join('guest_type as gt', 'bgd.guest_type_id', '=', 'gt.id')
                ->where('bgd.day_tour_log_details_id', $booking->id)
                ->whereNull('bgd.facility_id')
                ->where('bgd.quantity', '>', 0)
                ->groupBy('gt.type', 'gt.location', 'gt.rate')
                ->get()
                ->map(function ($guest) {
                    return [
                        'type' => $guest->type,
                        'location' => $guest->location,
                        'rate' => (float)$guest->rate,
                        'count' => (int)$guest->count
                    ];
                })
                ->toArray();

            // Get ACTUAL facilities booked (not the placeholder)
            $facilities = DB::table('booking_guest_details as bgd')
                ->select(
                    'f.name',
                    'f.price',
                    'bgd.facility_quantity'
                )
                ->join('facilities as f', 'bgd.facility_id', '=', 'f.id')
                ->where('bgd.day_tour_log_details_id', $booking->id)
                ->whereNotNull('bgd.facility_id')
                ->where('bgd.facility_quantity', '>', 0) // Only count if they actually booked a facility
                ->get()
                ->map(function ($facility) {
                    return [
                        'name' => $facility->name,
                        'price' => (float)$facility->price,
                        'quantity' => (int)$facility->facility_quantity
                    ];
                })
                ->toArray();

            $customerName = trim($booking->firstname . ' ' . $booking->lastname);
            $customerName = $customerName ?: 'Unknown Customer';

            // Determine category from guest breakdown
            $category = 'Mixed';
            if (!empty($guestBreakdown)) {
                $locations = array_unique(array_column($guestBreakdown, 'location'));
                $category = count($locations) === 1 ? $locations[0] : 'Mixed';
            }

            return [
                'id' => $booking->id,
                'reference' => $booking->reference,
                'date' => $booking->date,
                'amount' => (float)$booking->amount,
                'status' => $booking->status,
                'guest_count' => (int)$booking->guest_count,
                'category' => $category,
                'facilities' => $facilities, // This will now show actual facilities
                'has_facility' => !empty($facilities), // Simple flag
                'customer_name' => $customerName,
                'phone' => $booking->phone,
                'email' => $booking->email,
                'guest_breakdown' => $guestBreakdown
            ];
        })->toArray();

        return $bookings;

    } catch (\Exception $e) {
        Log::error('Error in getDetailedBookings: ' . $e->getMessage());
        return [];
    }
}

    public function exportData(Request $request)
{
    try {
        $category = $request->get('category');
        $facilityType = $request->get('facility_type');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        Log::info('Exporting day tour data', [
            'month' => $month,
            'year' => $year,
            'category' => $category,
            'facilityType' => $facilityType
        ]);

        // Get all the data using your existing methods
        $revenueData = $this->getRevenueData($month, $year, $category, $facilityType);
        $guestData = $this->getGuestDemographics($month, $year, $category, $facilityType);
        $categoryData = $this->getCategoryDistribution($month, $year, $facilityType);
        $facilityData = $this->getFacilityUtilization($month, $year, $category);
        $stats = $this->getStatistics($month, $year, $category, $facilityType);
        $bookings = $this->getDetailedBookings($month, $year, $category, $facilityType);

        $filename = "day_tour_analytics_{$year}_{$month}" . 
                   ($category ? "_{$category}" : "") . 
                   ($facilityType ? "_{$facilityType}" : "") . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($stats, $revenueData, $guestData, $categoryData, $facilityData, $bookings, $month, $year, $category, $facilityType) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Summary Section
            fputcsv($handle, ['DAY TOUR ANALYTICS REPORT']);
            fputcsv($handle, ['Period', date('F Y', mktime(0, 0, 0, $month, 1, $year))]);
            fputcsv($handle, ['Category Filter', $category ?: 'All Categories']);
            fputcsv($handle, ['Facility Filter', $facilityType ?: 'All Facilities']);
            fputcsv($handle, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []); // Empty row

            // Key Statistics
            fputcsv($handle, ['KEY STATISTICS']);
            fputcsv($handle, ['Total Revenue', '₱' . number_format($stats['total_revenue'], 2)]);
            fputcsv($handle, ['Total Bookings', $stats['total_bookings']]);
            fputcsv($handle, ['Total Guests', $stats['total_guests']]);
            fputcsv($handle, ['Top Category', $stats['top_category']]);
            fputcsv($handle, ['Revenue Change', number_format($stats['revenue_change'], 1) . '%']);
            fputcsv($handle, ['Bookings Change', number_format($stats['bookings_change'], 1) . '%']);
            fputcsv($handle, ['Guests Change', number_format($stats['guests_change'], 1) . '%']);
            fputcsv($handle, []); // Empty row

            // Daily Revenue Trends
            fputcsv($handle, ['DAILY REVENUE TRENDS']);
            fputcsv($handle, ['Day', 'Revenue']);
            foreach ($revenueData['labels'] as $index => $day) {
                fputcsv($handle, [
                    'Day ' . $day,
                    '₱' . number_format($revenueData['data'][$index], 2)
                ]);
            }
            fputcsv($handle, []); // Empty row

            // Guest Demographics
            fputcsv($handle, ['GUEST DEMOGRAPHICS']);
            fputcsv($handle, ['Guest Type', 'Count']);
            foreach ($guestData['labels'] as $index => $label) {
                fputcsv($handle, [
                    $label,
                    $guestData['data'][$index]
                ]);
            }
            fputcsv($handle, []); // Empty row

            // Category Distribution
            fputcsv($handle, ['CATEGORY DISTRIBUTION']);
            fputcsv($handle, ['Category', 'Revenue']);
            $totalCategoryRevenue = array_sum($categoryData);
            foreach ($categoryData as $categoryName => $revenue) {
                $percentage = $totalCategoryRevenue > 0 ? ($revenue / $totalCategoryRevenue) * 100 : 0;
                fputcsv($handle, [
                    $categoryName,
                    '₱' . number_format($revenue, 2) . ' (' . number_format($percentage, 1) . '%)'
                ]);
            }
            fputcsv($handle, []); // Empty row

            // Facility Utilization
            fputcsv($handle, ['FACILITY UTILIZATION']);
            fputcsv($handle, ['Facility', 'Booking Count']);
            foreach ($facilityData['labels'] as $index => $label) {
                fputcsv($handle, [
                    $label,
                    $facilityData['data'][$index]
                ]);
            }
            fputcsv($handle, []); // Empty row

            // Detailed Bookings
            fputcsv($handle, ['DETAILED BOOKINGS']);
            fputcsv($handle, [
                'Booking ID',
                'Reference',
                'Date',
                'Customer Name',
                'Phone',
                'Email',
                'Total Amount',
                'Status',
                'Guest Count',
                'Category',
                'Has Facility',
                'Guest Types',
                'Facilities Booked',
                'Total Guest Revenue'
            ]);

            foreach ($bookings as $booking) {
                // Format guest breakdown
                $guestBreakdown = '';
                $totalGuestRevenue = 0;
                if (!empty($booking['guest_breakdown'])) {
                    $guestTypes = [];
                    foreach ($booking['guest_breakdown'] as $guest) {
                        $guestRevenue = $guest['count'] * $guest['rate'];
                        $totalGuestRevenue += $guestRevenue;
                        $guestTypes[] = "{$guest['type']} ({$guest['location']}) x{$guest['count']} @ ₱{$guest['rate']} = ₱" . number_format($guestRevenue, 2);
                    }
                    $guestBreakdown = implode('; ', $guestTypes);
                }

                // Format facilities
                $facilitiesBooked = '';
                if (!empty($booking['facilities'])) {
                    $facilityList = [];
                    foreach ($booking['facilities'] as $facility) {
                        $facilityList[] = "{$facility['name']} x{$facility['quantity']} @ ₱{$facility['price']}";
                    }
                    $facilitiesBooked = implode('; ', $facilityList);
                }

                fputcsv($handle, [
                    $booking['id'],
                    $booking['reference'],
                    $booking['date'],
                    $booking['customer_name'],
                    $booking['phone'] ?? 'N/A',
                    $booking['email'] ?? 'N/A',
                    '₱' . number_format($booking['amount'], 2),
                    $booking['status'],
                    $booking['guest_count'],
                    $booking['category'],
                    $booking['has_facility'] ? 'Yes' : 'No',
                    $guestBreakdown,
                    $facilitiesBooked,
                    '₱' . number_format($totalGuestRevenue, 2)
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);

    } catch (\Exception $e) {
        Log::error('Error exporting day tour data: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error exporting data: ' . $e->getMessage()
        ], 500);
    }
}
}