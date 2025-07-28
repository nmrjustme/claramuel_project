<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\FacilityBookingLog;
use App\Models\Facility;
use App\Models\Breakfast;
use App\Models\FacilitySummary;
use App\Models\FacilityBookingDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// This is the excel package
use App\Exports\BookingsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use App\Events\NewBookingRequest;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date',
            'facilities' => 'required|array',
            'facilities.*.facility_id' => 'required|exists:facilities,id',
            'facilities.*.price' => 'required|numeric|min:0',
            'facilities.*.nights' => 'required|integer|min:1',
            'facilities.*.total_price' => 'required|numeric|min:0',
            'breakfast_included' => 'sometimes|boolean',
            'breakfast_price' => 'required_if:breakfast_included,true|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Parse dates explicitly with timezone
        $timezone = config('app.timezone', 'Asia/Manila');
        $checkinDate = Carbon::parse($request->checkin_date, $timezone)
            ->setTimezone('UTC')
            ->startOfDay();
        $checkoutDate = Carbon::parse($request->checkout_date, $timezone)
            ->setTimezone('UTC')
            ->startOfDay();

        // Verify dates after parsing
        if ($checkinDate >= $checkoutDate) {
            return response()->json([
                'success' => false,
                'message' => 'Check-out date must be after check-in date'
            ], 422);
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Create or update user (email is no longer unique)
            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => 'customer',
            ]);

            // Create booking log with confirmation token
            $bookingLog = FacilityBookingLog::create([
                'user_id' => $user->id,
                'booking_date' => now(),
                'confirmation_token' => Str::random(60),
            ]);


            // Get active breakfast price if included
            $breakfastId = null;
            if ($request->breakfast_included) {
                $breakfast = Breakfast::where('status', 'Active')->first();
                if ($breakfast) {
                    $breakfastId = $breakfast->id;
                }
            }

            // Process each facility
            foreach ($request->facilities as $facilityData) {
                $facility = Facility::findOrFail($facilityData['facility_id']);
                
                // Create facility summary
                $facilitySummary = FacilitySummary::create([
                    'facility_id' => $facility->id,
                    'breakfast_id' => $breakfastId,
                    'facility_booking_log_id' => $bookingLog->id,
                ]);

                // Calculate price including breakfast if applicable
                $facilityPrice = $facilityData['total_price'];
                if ($request->breakfast_included) {
                    $facilityPrice += ($request->breakfast_price / count($request->facilities));
                }

                // Create booking details
                FacilityBookingDetails::create([
                    'facility_summary_id' => $facilitySummary->id,
                    'facility_booking_log_id' => $bookingLog->id,
                    'checkin_date' => $checkinDate->format('Y-m-d'),
                    'checkout_date' => $checkoutDate->format('Y-m-d'),
                    'total_price' => $facilityPrice,
                ]);
            }

            // Commit transaction
            DB::commit();
            
            $bookingLog->load('user');
            // After booking creation
            event(new NewBookingRequest($bookingLog));
            
            return response()->json([
                'success' => true,
                'booking_id' => $bookingLog->id,
                'message' => 'Booking created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking failed:', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'dates' => [
                    'checkin' => $checkinDate->format('Y-m-d H:i:s'),
                    'checkout' => $checkoutDate->format('Y-m-d H:i:s')
                ]
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function WaitConfirmation(Request $request)
    {
        $email = $request->query('email');
        return view('customer_pages.wait_for_confirmation', ['email' => $email]);
    }
    
    public function index(Request $request)
    {
        $status = $request->input('status', 'fully_paid');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');

        $query = FacilityBookingLog::with([
                'user', 
                'details', 
                'payments.verifiedBy',
                'summaries.facility'
            ])
            ->orderBy('confirmed_at', 'desc')
            ->whereHas('payments', function($q) {
                $q->whereNotNull('reference_no')
                ->where('reference_no', '!=', '');
            });

        // Apply search filter if search term is provided
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('firstname', 'like', "%{$search}%")
                                ->orWhere('lastname', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('details', function($detailQuery) use ($search) {
                    $detailQuery->where('checkin_date', 'like', "%{$search}%")
                                ->orWhere('checkout_date', 'like', "%{$search}%");
                })
                ->orWhereHas('summaries.facility', function($facilityQuery) use ($search) {
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Status filter based on payments
        if (in_array($status, ['fully_paid', 'verified', 'pending', 'rejected', 'under_verification'])) {
            $query->whereHas('payments', function($q) use ($status) {
                $q->where('status', $status);
            });
        }
        
        $total = $query->count();
        $bookings = $query->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();

        return response()->json([
            'data' => $bookings,
            'total' => $total,
            'from' => ($page - 1) * $perPage + 1,
            'to' => min($page * $perPage, $total),
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ]);
    }
    
    public function Export(Request $request)
    {
        try {
            $status = $request->query('status', 'paid');
            
            $validStatuses = ['fully_paid', 'under_verification', 'verified', 'rejected', 'request', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                $status = 'paid';
            }
            
            // Early check for data existence
            if (!FacilityBookingLog::whereHas('payments', fn($q) => $q->where('status', $status))->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No bookings found for export',
                ], 404);
            }
            
            $filename = 'bookings_' . $status . '_' . now()->format('Y-m-d') . '.xlsx';
            
            return Excel::download(new BookingsExport($status), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Excel file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function nextCheckin()
    {
        try {
            // Get the next booking where checkin_date is in the future
            $nextBooking = FacilityBookingLog::with(['user', 'details'])
                ->whereHas('details', function($query) {
                    $query->where('checkin_date', '>', now()->timezone('Asia/Manila')->toDateString())
                        ->orderBy('checkin_date');
                })
                ->first();

            if (!$nextBooking) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No upcoming check-ins found'
                ]);
            }

            // Use checkin_date (scheduled date) not checked_in_at (actual arrival)
            $checkinDate = Carbon::parse($nextBooking->details[0]->checkin_date);
            $daysUntil = round(now()->diffInHours($checkinDate) / 24, 1);
            
            return response()->json([
                'success' => true,
                'data' => $nextBooking,
                'days_until' => $daysUntil
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching next check-in',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        
    public function show(FacilityBookingLog $booking)
    {
        $booking->load(['user', 'details', 'payments', 'summaries.facility']);
        
        return response()->json([
            'data' => $booking
        ]);
    }
}