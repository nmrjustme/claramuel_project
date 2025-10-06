<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\Payments;
use App\Models\FacilityBookingLog;
use App\Models\FacilityBookingDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            \Log::info('=== ANALYTICS DEBUG START ===');

            $category = $request->input('category', '');
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            \Log::info('Request parameters:', [
                'category' => $category,
                'month' => $month,
                'year' => $year
            ]);

            // Base query for rooms - check if facilities exist
            $roomsQuery = Facility::where('type', 'room');

            if (!empty($category)) {
                $roomsQuery->where('category', $category);
            }

            $rooms = $roomsQuery->get();
            \Log::info('Found rooms: ' . $rooms->count());

            if ($rooms->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'earnings' => [],
                    'labels' => [],
                    'currency' => '₱',
                    'rooms' => [],
                    'categoryEarnings' => [], // Add empty category earnings
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
                \Log::info("Processing room: " . $room->name);
                $roomStats = $this->calculateRoomStats($room->id, $month, $year);

                $earningsData[] = $roomStats['earnings'];
                $labels[] = $room->name;
                $totalEarnings += $roomStats['earnings'];

                if ($roomStats['earnings'] > 0) {
                    $roomsBooked++;
                }

                if (!isset($categoryEarnings[$room->category])) {
                    $categoryEarnings[$room->category] = 0;
                }
                $categoryEarnings[$room->category] += $roomStats['earnings'];

                $roomDetails[] = [
                    'id' => $room->id,
                    'name' => $room->name,
                    'category' => $room->category,
                    'earnings' => $roomStats['earnings'],
                    'bookings' => $roomStats['booking_count'],
                    'occupancy' => $roomStats['occupancy_rate'],
                    'averageRate' => $roomStats['average_daily_rate'],
                    'recentBookings' => $roomStats['recent_bookings']
                ];

                $totalBookings += $roomStats['booking_count'];
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
                'categoryEarnings' => $categoryEarnings, // Add this line for pie chart
                'stats' => [
                    'totalEarnings' => $totalEarnings,
                    'roomsBooked' => $roomsBooked,
                    'topCategory' => $topCategory,
                    'occupancyRate' => $occupancyRate,
                    'totalBookings' => $totalBookings,
                    'comparison' => $comparisonStats
                ]
            ];

            \Log::info('Response data prepared successfully');
            \Log::info('Category Earnings:', $categoryEarnings);
            \Log::info('=== ANALYTICS DEBUG END ===');

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error in getEarningsData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

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
     * Calculate earnings by category
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
            $earnings = Payments::join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'log.id', '=', 'details.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->where('facilities.category', $category)
                ->where('log.status', '!=', 'pending_confirmation');

            // Apply date filtering
            if ($month && $year) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                $earnings->whereBetween('details.checkin_date', [$startDate, $endDate]);
            }

            $totalEarnings = (float) $earnings->sum(DB::raw('COALESCE(payments.amount, 0) + COALESCE(payments.checkin_paid, 0)'));

            if ($totalEarnings > 0) {
                $categoryEarnings[$category] = $totalEarnings;
            }
        }

        // Sort by earnings descending
        arsort($categoryEarnings);

        return $categoryEarnings;
    }

    private function calculateRoomStats($roomId, $month = null, $year = null)
    {
        try {
            \Log::info("Calculating stats for room: $roomId");

            // First, let's check if the necessary tables exist and have data
            $tableCheck = DB::select('SHOW TABLES LIKE "payments"');
            if (empty($tableCheck)) {
                \Log::warning('Payments table does not exist or is empty');
                return $this->getEmptyRoomStats();
            }

            $query = Payments::join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'log.id', '=', 'details.facility_booking_log_id')
                ->where('summary.facility_id', $roomId)
                ->where('log.status', '!=', 'pending_confirmation');

            // Apply date filtering
            $dateFilter = $this->applyDateFilter($query, $month, $year);

            // Calculate total earnings
            $totalEarnings = (float) $query->sum(DB::raw('COALESCE(payments.amount, 0) + COALESCE(payments.checkin_paid, 0)'));

            // Get booking count
            $bookingCount = $query->distinct()->count('log.id');

            // Calculate occupancy rate for this room
            $nightsBooked = $this->calculateNightsBooked($roomId, $dateFilter);
            $availableNights = $this->calculateAvailableNights($dateFilter);
            $occupancyRate = $availableNights > 0 ? round(($nightsBooked / $availableNights) * 100, 1) : 0;

            // Calculate average daily rate
            $averageDailyRate = $bookingCount > 0 ? round($totalEarnings / $bookingCount, 2) : 0;

            // Get recent bookings (last 5)
            $recentBookings = $this->getRecentBookings($roomId, 5, $month, $year);

            \Log::info("Room $roomId stats:", [
                'earnings' => $totalEarnings,
                'bookings' => $bookingCount,
                'occupancy' => $occupancyRate
            ]);

            return [
                'earnings' => $totalEarnings,
                'booking_count' => $bookingCount,
                'occupancy_rate' => $occupancyRate,
                'average_daily_rate' => $averageDailyRate,
                'recent_bookings' => $recentBookings
            ];

        } catch (\Exception $e) {
            \Log::error("Error calculating stats for room $roomId: " . $e->getMessage());
            return $this->getEmptyRoomStats();
        }
    }

    private function getEmptyRoomStats()
    {
        return [
            'earnings' => 0,
            'booking_count' => 0,
            'occupancy_rate' => 0,
            'average_daily_rate' => 0,
            'recent_bookings' => []
        ];
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

    private function calculateNightsBooked($roomId, $dateRange)
    {
        try {
            $nights = FacilityBookingDetails::join('facility_booking_log as log', 'facility_booking_details.facility_booking_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->where('summary.facility_id', $roomId)
                ->where('log.status', '!=', 'pending_confirmation')
                ->whereBetween('checkin_date', [$dateRange['start'], $dateRange['end']])
                ->sum(DB::raw('DATEDIFF(COALESCE(checkout_date, checkin_date), checkin_date)'));

            return $nights ?: 0;
        } catch (\Exception $e) {
            \Log::error("Error calculating nights booked for room $roomId: " . $e->getMessage());
            return 0;
        }
    }

    private function calculateAvailableNights($dateRange)
    {
        try {
            $daysInPeriod = $dateRange['start']->diffInDays($dateRange['end']);
            return $daysInPeriod;
        } catch (\Exception $e) {
            \Log::error("Error calculating available nights: " . $e->getMessage());
            return 30; // Fallback to 30 days
        }
    }

    private function calculateTotalNightsBooked($month, $year, $category)
    {
        try {
            $query = FacilityBookingDetails::join('facility_booking_log as log', 'facility_booking_details.facility_booking_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->where('log.status', '!=', 'pending_confirmation');

            if (!empty($category)) {
                $query->where('facilities.category', $category);
            }

            $dateFilter = $this->applyDateFilter($query, $month, $year);

            $nights = $query->sum(DB::raw('DATEDIFF(COALESCE(checkout_date, checkin_date), checkin_date)'));

            return $nights ?: 0;
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

    private function getRecentBookings($roomId, $limit = 5, $month = null, $year = null)
    {
        try {
            $query = Payments::join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'log.id', '=', 'details.facility_booking_log_id')
                ->where('summary.facility_id', $roomId)
                ->where('log.status', '!=', 'pending_confirmation');

            if ($month && $year) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                $query->whereBetween('details.checkin_date', [$startDate, $endDate]);
            }

            $bookings = $query->select(
                'details.checkin_date as date',
                DB::raw('(COALESCE(payments.amount, 0) + COALESCE(payments.checkin_paid, 0)) as total_amount'),
                'log.status'
            )
                ->orderBy('details.checkin_date', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($booking) {
                    return [
                        'date' => Carbon::parse($booking->date)->format('M j, Y'),
                        'amount' => number_format($booking->total_amount, 2),
                        'status' => $booking->status
                    ];
                })
                ->toArray();

            return $bookings;
        } catch (\Exception $e) {
            \Log::error("Error getting recent bookings for room $roomId: " . $e->getMessage());
            return [];
        }
    }

    private function getComparisonStats($month, $year, $category)
    {
        try {
            $currentStats = $this->getPeriodStats($month, $year, $category);

            $previousPeriod = $this->getPreviousPeriod($month, $year);
            $previousStats = $this->getPeriodStats(
                $previousPeriod['month'],
                $previousPeriod['year'],
                $category
            );

            return [
                'earnings_change' => $this->calculatePercentageChange($currentStats['earnings'], $previousStats['earnings']),
                'bookings_change' => $this->calculatePercentageChange($currentStats['bookings'], $previousStats['bookings']),
                'occupancy_change' => $this->calculatePercentageChange($currentStats['occupancy'], $previousStats['occupancy'])
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

    private function getPeriodStats($month, $year, $category)
    {
        try {
            $query = Payments::join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'log.id', '=', 'details.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->where('log.status', '!=', 'pending_confirmation');

            if (!empty($category)) {
                $query->where('facilities.category', $category);
            }

            $this->applyDateFilter($query, $month, $year);

            $earnings = (float) $query->sum(DB::raw('COALESCE(payments.amount, 0) + COALESCE(payments.checkin_paid, 0)'));
            $bookings = $query->distinct()->count('log.id');

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
                $yearData = $this->getYearlyData($compareYear, $category);
                $data[$compareYear] = $yearData;
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getYearlyData($year, $category)
    {
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $earnings = Payments::join('facility_booking_log as log', 'payments.facility_log_id', '=', 'log.id')
                ->join('facility_summary as summary', 'log.id', '=', 'summary.facility_booking_log_id')
                ->join('facility_booking_details as details', 'log.id', '=', 'details.facility_booking_log_id')
                ->join('facilities', 'summary.facility_id', '=', 'facilities.id')
                ->where('facilities.type', 'room')
                ->where('log.status', '!=', 'pending_confirmation')
                ->when($category, function ($q) use ($category) {
                    $q->where('facilities.category', $category);
                })
                ->whereBetween('details.checkin_date', [$startDate, $endDate])
                ->sum(DB::raw('payments.amount + payments.checkin_paid'));

            $monthlyData[] = $earnings ?: 0;
        }

        return $monthlyData;
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
            $stats = $this->calculateRoomStats($room->id, $month, $year);

            $exportData[] = [
                'Room Name' => $room->name,
                'Category' => $room->category,
                'Earnings' => $stats['earnings'], // Raw number without currency symbol
                'Bookings' => $stats['booking_count'],
                'Occupancy Rate' => $stats['occupancy_rate'],
                'Average Daily Rate' => $stats['average_daily_rate']
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