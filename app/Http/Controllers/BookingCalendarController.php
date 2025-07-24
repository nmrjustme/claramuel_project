<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacilityBookingLog;
use App\Models\FacilityBookingDetails;
use Carbon\Carbon;

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
            \Log::error('Calendar data error: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
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
                        'amount_paid' => $log->payments->sum('amount') ?? 0,
                        'breakfast' => $detail->breakfast_id !== null
                    ];
                }
            }
                
            return response()->json([
                'success' => true,
                'bookings' => $formattedBookings
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Bookings by date error: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading bookings for date',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}