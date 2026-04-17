<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacilityBookingLog;
use App\Models\FacilityBookingDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingCalendarController extends Controller
{
    public function getCalendarData(Request $request)
    {
        try {
            // Validate inputs
            $validated = $request->validate([
                'month' => 'sometimes|integer|between:1,12',
                'year' => 'sometimes|integer|digits:4'
            ]);
            
            $month = $validated['month'] ?? date('m');
            $year = $validated['year'] ?? date('Y');
            
            // Get date range for the calendar
            $date = Carbon::createFromDate($year, $month, 1);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            // Get days for the calendar view (including padding days)
            $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
            $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);
            
            $days = [];
            $currentDay = $startOfCalendar->copy();
            
            while ($currentDay <= $endOfCalendar) {
                $days[] = [
                    'day' => $currentDay->day,
                    'isCurrentMonth' => $currentDay->month == $month,
                    'date' => $currentDay->format('Y-m-d')
                ];
                $currentDay->addDay();
            }
            
            // Get booking logs with their details for this month
            $bookingLogs = FacilityBookingLog::with([
                    'user:id,firstname,lastname,email',
                    'details.facilitySummary.facility:id,name',
                    'payments:id,facility_log_id,amount'
                ])
                ->whereHas('details', function($query) use ($startOfMonth, $endOfMonth) {
                    $query->where(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->whereBetween('checkin_date', [$startOfMonth, $endOfMonth])
                            ->orWhereBetween('checkout_date', [$startOfMonth, $endOfMonth]);
                    });
                })
                ->get();
        
            // Format booking data by date
            $formattedBookings = [];
            
            foreach ($bookingLogs as $log) {
                foreach ($log->details as $detail) {
                    $checkin = Carbon::parse($detail->checkin_date);
                    $checkout = Carbon::parse($detail->checkout_date);
                    
                    // Add entry for each day of the booking
                    for ($date = $checkin; $date->lte($checkout); $date->addDay()) {
                        $dateStr = $date->format('Y-m-d');
                        
                        if (!isset($formattedBookings[$dateStr])) {
                            $formattedBookings[$dateStr] = [];
                        }
                        
                        // Only add if not already added for this booking log
                        if (!in_array($log->id, array_column($formattedBookings[$dateStr], 'booking_log_id'))) {
                            $formattedBookings[$dateStr][] = [
                                'booking_log_id' => $log->id,
                                'user_name' => $log->user ? $log->user->firstname . ' ' . $log->user->lastname : 'Unknown User',
                                'user_email' => $log->user->email ?? '',
                                'facility_name' => optional($detail->facilitySummary)->facility->name ?? 'Unknown Facility',
                                'checkin_date' => $detail->checkin_date,
                                'checkout_date' => $detail->checkout_date,
                                'status' => $log->status,
                                'payment_status' => $log->payment_status,
                                'total_price' => $detail->total_price ?? 0,
                                'amount_paid' => $log->payments->sum('amount') ?? 0,
                                'breakfast' => $detail->breakfast_id !== null
                            ];
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'days' => $days,
                'bookings' => $formattedBookings
            ]);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading calendar data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function getBookingsByDate(Request $request)
    {
        try {
            $date = $request->input('date');
            $carbonDate = Carbon::parse($date);
            
            // Get booking logs with their details for the selected date
            $bookingLogs = FacilityBookingLog::with([
                    'user:id,firstname,lastname,email',
                    'details.facilitySummary.facility:id,name',
                    'payments:id,facility_log_id,amount'
                ])
                ->whereHas('details', function($query) use ($carbonDate) {
                    $query->whereDate('checkin_date', '<=', $carbonDate)
                          ->whereDate('checkout_date', '>=', $carbonDate);
                })
                ->get();
            
            // Format the bookings data
            $formattedBookings = [];
            
            foreach ($bookingLogs as $log) {
                foreach ($log->details as $detail) {
                    $formattedBookings[] = [
                        'booking_log_id' => $log->id,
                        'user_name' => $log->user ? $log->user->firstname . ' ' . $log->user->lastname : 'Unknown User',
                        'user_email' => $log->user->email ?? '',
                        'facility_name' => optional($detail->facilitySummary)->facility->name ?? 'Unknown Facility',
                        'checkin_date' => $detail->checkin_date,
                        'checkout_date' => $detail->checkout_date,
                        'status' => $log->status,
                        'payment_status' => $log->payment_status,
                        'total_price' => $detail->total_price ?? 0,
                        'amount_paid' => ($log->payments->sum('amount') + ($log->payments->sum('amount_paid') ?? 0)) ?? 0,
                        'breakfast' => $detail->facilitySummary->breakfast_id !== null,
                        
                    ];
                }
            }
                
            return response()->json([
                'success' => true,
                'bookings' => $formattedBookings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading bookings for date',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function getUnavailableDatesFacility()
    {
        try {
            $now = now()->format('Y-m-d');
            
            $dates = DB::table('facilities as fac')
                ->join('facility_summary as fac_sum', 'fac_sum.facility_id', '=', 'fac.id')
                ->join('facility_booking_details as fac_details', 'fac_details.facility_summary_id', '=', 'fac_sum.id')
                ->join('facility_booking_log as fac_log', 'fac_log.id', '=', 'fac_details.facility_booking_log_id')
                ->join('payments', 'payments.facility_log_id', '=', 'fac_log.id')
                ->where('fac_log.status', '!=', 'pending_confirmation')
                ->where('payments.status', 'verified')
                ->where('fac_details.checkout_date', '>=', $now)
                ->select([
                    'fac.id as facility_id', 
                    'fac_details.checkin_date', 
                    'fac_details.checkout_date'
                ])
                ->get()
                ->groupBy('facility_id')
                ->map(function($dates) {
                    return $dates->map(function($date) {
                        $checkin = Carbon::parse($date->checkin_date)
                            ->setTimezone(config('app.timezone'));
                        $checkout = Carbon::parse($date->checkout_date)
                            ->setTimezone(config('app.timezone'))
                            ->subDay(); // Subtract one day from checkout
                        
                        return [
                            'checkin_date' => $checkin->format('Y-m-d'),
                            'checkout_date' => $checkout->format('Y-m-d')
                        ];
                    });
                });
            
            return response()->json([
                'success' => true,
                'dates' => $dates
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching unavailable dates: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch unavailable dates',
                'dates' => []
            ], 500);
        }
    }
}