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
use App\Models\BookingGuestDetails;
use App\Models\Payments;
use App\Models\GuestAddons;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// This is the excel package
use App\Exports\BookingsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Events\BookingNew;
use App\Mail\AdminNotification;

class BookingController extends Controller
{

    public function stores(Request $request)
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

            'guest_types' => 'required|array',
            'guest_types.*' => 'array', // Each facility's guest types
            'guest_types.*.*' => 'nullable|integer|min:0',

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
            ->setTimezone('Asia/Manila')
            ->startOfDay();
        $checkoutDate = Carbon::parse($request->checkout_date, $timezone)
            ->setTimezone('Asia/Manila')
            ->startOfDay();

        // Verify dates after parsing
        if ($checkinDate >= $checkoutDate) {
            return response()->json([
                'success' => false,
                'message' => 'Check-out date must be after check-in date'
            ], 422);
        }

        // Validate guest counts match facility pax limits
        foreach ($request->facilities as $facilityData) {
            $facilityId = $facilityData['facility_id'];
            $facility = Facility::findOrFail($facilityId);

            $totalGuests = array_sum($request->guest_types[$facilityId] ?? []);

            if ($totalGuests > $facility->pax) {
                return response()->json([
                    'success' => false,
                    'message' => "Facility {$facility->name} exceeds maximum guest limit of {$facility->pax}"
                ], 422);
            }
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Create or update user
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

            \Log::info('Booking created', [
                'booking_id' => $bookingLog->id,
                'user_id' => $user->id
            ]);

            // Get active breakfast price if included
            $breakfastId = null;
            $breakfastPricePerFacilityPerDay = 0;

            if ($request->breakfast_included) {
                $breakfast = Breakfast::where('status', 'Active')->first();
                if ($breakfast) {
                    $breakfastId = $breakfast->id;
                    $breakfastPricePerFacilityPerDay = $breakfast->price;
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

                // Calculate breakfast cost for this facility (per facility per night)
                $breakfastCost = 0;
                if ($request->breakfast_included) {
                    $breakfastCost = $breakfastPricePerFacilityPerDay * $facilityData['nights'];
                }

                // Calculate total price for this facility (room + breakfast)
                $facilityTotalPrice = $facilityData['total_price'] + $breakfastCost;

                // Create booking details
                FacilityBookingDetails::create([
                    'facility_summary_id' => $facilitySummary->id,
                    'facility_booking_log_id' => $bookingLog->id,
                    'checkin_date' => $checkinDate->format('Y-m-d'),
                    'checkout_date' => $checkoutDate->format('Y-m-d'),
                    'total_price' => $facilityTotalPrice,
                    'breakfast_cost' => $breakfastCost,
                ]);

                // Process guest types for this facility
                if (isset($request->guest_types[$facility->id])) {
                    foreach ($request->guest_types[$facility->id] as $guestTypeId => $quantity) {
                        BookingGuestDetails::create([
                            'guest_type_id' => $guestTypeId,
                            'facility_booking_log_id' => $bookingLog->id,
                            'facility_id' => $facility->id,
                            'quantity' => $quantity
                        ]);
                    }
                }
            }

            // Commit transaction
            DB::commit();

            $bookingLog->load(['user']);

            event(new BookingNew($bookingLog)); // Event listener for new booking list

            // Sending active admin email
            if (User::where('role', 'Admin')->where('is_active', true)->exists()) {
                $this->sendEmailAdmin($bookingLog);
            }

            return response()->json([
                'success' => true,
                'booking_id' => $bookingLog->id,
                'message' => 'Booking created successfully',
                'redirect_url' => route('booking.submitted', ['email' => $request->email])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking failed:', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendEmailAdmin(FacilityBookingLog $booking)
    {
        // Get all admin users who are active
        $admins = User::where('role', 'Admin')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();

        if ($admins->isEmpty()) {
            Log::warning("No active admin with email found");
            return;
        }

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(
                    new AdminNotification($booking)
                );

                Log::info("Booking email sent to admin", [
                    'booking_id' => $booking->id,
                    'email' => $admin->email
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send admin email", [
                    'booking_id' => $booking->id,
                    'email' => $admin->email,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function WaitConfirmation(Request $request)
    {
        $email = $request->query('email');
        return view('customer_pages.wait_for_confirmation', ['email' => $email]);
    }

    public function bookingSubmitted(Request $request)
    {
        $email = $request->query('email');
        return view('customer_pages.booking.wait_for_confirmation', ['email' => $email]);
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
            'summaries.facility',
            'guestDetails.guestType'
        ])
            ->orderBy('created_at', 'desc');

        // Apply search filter if search term is provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('details', function ($detailQuery) use ($search) {
                        $detailQuery->where('checkin_date', 'like', "%{$search}%")
                            ->orWhere('checkout_date', 'like', "%{$search}%");
                    })
                    ->orWhereHas('summaries.facility', function ($facilityQuery) use ($search) {
                        $facilityQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter based on payments
        if (in_array($status, ['fully_paid', 'verified', 'pending', 'rejected', 'under_verification', 'In-House'])) {
            $query->whereHas('payments', function ($q) use ($status) {
                if ($status === 'fully_paid') {
                    // Check remaining_balance_status column for fully_paid condition
                    $q->where('remaining_balance_status', 'fully_paid');
                } else {
                    $q->where('status', $status);
                }
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
                ->whereHas('details', function ($query) {
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
        $booking->load([
            'user',
            'details',
            'payments',
            'summaries.facility',
            'guestDetails.guestType',
            'guestAddons'
        ]);

        return response()->json([
            'data' => $booking,
        ]);
    }

    public function getMyBookings(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 18);
            $page = $request->input('page', 1);
            $firstName = $request->input('firstname', '');
            $lastName = $request->input('lastname', '');
            $checkinDate = $request->input('checkin_date', '');
            $checkoutDate = $request->input('checkout_date', '');
            $dateType = $request->input('date_type', 'checkin'); // Default to check-in
            $status = $request->input('status', '');
            $search = $request->input('search', '');
            $id = $request->input('id', '');

            $query = FacilityBookingLog::with([
                'user:id,firstname,lastname,email,phone',
                'payments:id,facility_log_id,amount_paid,checkin_paid,remaining_balance_status,status',
                'details:id,facility_booking_log_id,checkin_date,checkout_date',
                'summaries.facility:id,name,price',
                'summaries.breakfast',
            ])
                ->orderBy('created_at', 'desc');

            // Search by ID
            if ($id) {
                $query->where('id', $id);
            }

            // General search across multiple fields
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%$search%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('firstname', 'like', "%$search%")
                                ->orWhere('lastname', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%")
                                ->orWhere('phone', 'like', "%$search%");
                        });
                });
            }

            // Search by first name
            if (!empty(trim($firstName))) {
                $query->whereHas('user', function ($q) use ($firstName) {
                    $q->where('firstname', 'like', "%$firstName%");
                });
            }

            if (!empty(trim($lastName))) {
                $query->whereHas('user', function ($q) use ($lastName) {
                    $q->where('lastname', 'like', "%$lastName%");
                });
            }

            // Search by check-in or check-out date
            if ($checkinDate && $dateType === 'checkin') {
                $query->whereHas('details', function ($q) use ($checkinDate) {
                    $q->whereDate('checkin_date', $checkinDate);
                });
            } elseif ($checkoutDate && $dateType === 'checkout') {
                $query->whereHas('details', function ($q) use ($checkoutDate) {
                    $q->whereDate('checkout_date', $checkoutDate);
                });
            }

            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            $bookings = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $bookings->map(function ($booking) {
                    // Display status logic
                    $displayStatus = $booking->status;

                    return [
                        'id' => $booking->id,
                        'user' => [
                            'firstname' => $booking->user->firstname,
                            'lastname' => $booking->user->lastname,
                            'email' => $booking->user->email,
                            'phone' => $booking->user->phone,
                        ],
                        'status' => $displayStatus,
                        'code' => $booking->code,
                        'details' => $booking->details,
                        'summaries' => $booking->summaries->map(function ($summary) {
                            return [
                                'facility' => $summary->facility,
                                'breakfast_id' => $summary->breakfast_id,
                                'breakfast' => $summary->breakfast,
                                'breakfast_price' => $summary->breakfast_price
                            ];
                        }),
                        'payments' => $booking->payments,
                    ];
                }),
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
                'from' => $bookings->firstItem(),
                'to' => $bookings->lastItem()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function paymentDetails($id)
    {
        $booking = FacilityBookingLog::with(['payments', 'details'])->find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'data' => $booking
        ]);
    }

    public function processMyPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $booking = FacilityBookingLog::with('payments')->find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        // Get the latest payment record for this booking
        $payment = $booking->payments()->first();

        if (!$payment) {
            return response()->json([
                'message' => 'No payment record found to update'
            ], 404);
        }

        // Update fields
        $payment->checkin_paid = $request->amount;
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'payment' => $payment
        ]);
    }


    /**
     * Check-in booking
     */
    public function checkin($id)
    {
        $booking = FacilityBookingLog::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        if ($booking->status === 'checked_in') {
            return response()->json([
                'message' => 'Booking already checked in'
            ], 400);
        }

        $booking->status = 'checked_in';
        $booking->checked_in_at = now();
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking checked in successfully',
            'booking_id' => $booking->id
        ]);
    }


    // In your Laravel controller
    public function processPayment(Request $request, $bookingId)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string'
            ]);

            $booking = FacilityBookingLog::findOrFail($bookingId);

            // Create payment record
            $payment = new Payments();
            $payment->booking_id = $bookingId;
            $payment->amount_paid = $validated['amount'];
            $payment->save();

            // Update booking status if needed
            if ($validated['payment_type'] === 'checkin') {
                // Check if balance is fully paid
                $totalAmount = $booking->details->sum('total_price');
                $totalPaid = $booking->payments->sum('amount_paid');

                if ($totalPaid >= $totalAmount) {
                    $booking->status = 'confirmed';
                    $booking->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getConfirmedInquiries(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $readStatus = $request->input('read_status', '');

            $query = FacilityBookingLog::with([
                'user',
                'summaries.facility',
                'summaries.breakfast',
                'details',
                'payments',
            ])
                ->where('status', 'confirmed')
                // 🔍 Search
                ->when($search, function ($query) use ($search) {
                    if (is_numeric($search)) {
                        $query->where('id', $search);
                    } else {
                        $query->where(function ($q) use ($search) {
                            $q->whereHas('user', function ($uq) use ($search) {
                                $uq->where('firstname', 'like', "%{$search}%")
                                    ->orWhere('lastname', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            })
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                    }
                })
                // 📌 Read status filtering
                ->when($readStatus, function ($query) use ($readStatus) {
                    if (in_array($readStatus, ['read', 'unread'])) {
                        $query->where('is_read', $readStatus === 'read');
                    }
                })
                ->orderBy('created_at', 'desc');

            $bookings = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $bookings->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'user' => [
                            'firstname' => $booking->user->firstname ?? null,
                            'lastname' => $booking->user->lastname ?? null,
                            'email' => $booking->user->email ?? null,
                        ],
                        'is_read' => (bool) $booking->is_read,
                        'status' => $booking->status,
                        'code' => $booking->code,
                        'created_at' => $booking->created_at->toDateTimeString(),
                    ];
                }),
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPendingInquiries(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $readStatus = $request->input('read_status', '');

            $query = FacilityBookingLog::with([
                'user',
                'summaries.facility',
                'summaries.breakfast',
                'details',
                'payments',
            ])
                ->where('status', 'pending_confirmation')
                // 🔍 Search
                ->when($search, function ($query) use ($search) {
                    if (is_numeric($search)) {
                        $query->where('id', $search);
                    } else {
                        $query->where(function ($q) use ($search) {
                            $q->whereHas('user', function ($uq) use ($search) {
                                $uq->where('firstname', 'like', "%{$search}%")
                                    ->orWhere('lastname', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            })
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                    }
                })
                // 📌 Read status filtering
                ->when($readStatus, function ($query) use ($readStatus) {
                    if (in_array($readStatus, ['read', 'unread'])) {
                        $query->where('is_read', $readStatus === 'read');
                    }
                })
                ->orderBy('created_at', 'desc');

            $bookings = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $bookings->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'user' => [
                            'firstname' => $booking->user->firstname ?? null,
                            'lastname' => $booking->user->lastname ?? null,
                            'email' => $booking->user->email ?? null,
                        ],
                        'is_read' => (bool) $booking->is_read,
                        'status' => $booking->status,
                        'code' => $booking->code,
                        'created_at' => $booking->created_at->toDateTimeString(),
                    ];
                }),
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bookingDetails($bookingId)
    {
        return view('admin.Log.booking_details', [
            'bookingId' => $bookingId
        ]);
    }

    public function guestDetailsList()
    {
        $bookings = FacilityBookingLog::with('user', 'details')->get();

        return response()->json([
            'data' => $bookings
        ]);
    }

    public function storeGuestAddons(Request $request)
    {
        $request->validate([
            'facility_booking_log_id' => 'required|exists:facility_booking_log,id',
            'guests' => 'required|array',
            'guests.*.type' => 'required|in:kid,adult,senior',
            'guests.*.cost' => 'required|numeric|min:0',
            'guests.*.quantity' => 'required|integer|min:1',
            'guests.*.total_cost' => 'required|numeric|min:0'
        ]);

        try {
            $facilityBookingLogId = $request->facility_booking_log_id;
            $totalNewCost = 0;

            // Save guest addons & calculate total new cost
            foreach ($request->guests as $guest) {
                GuestAddons::create([
                    'facility_booking_log_id' => $facilityBookingLogId,
                    'type' => $guest['type'],
                    'cost' => $guest['cost'],
                    'quantity' => $guest['quantity'],
                    'total_cost' => $guest['total_cost']
                ]);

                $totalNewCost += $guest['total_cost']; // accumulate
            }

            // Fetch booking log with details
            $bookingLog = FacilityBookingLog::with('details')->find($facilityBookingLogId);

            if ($bookingLog && $bookingLog->details()->exists()) {
                // Update all details rows by adding new addons cost
                $bookingLog->details()->update([
                    'total_price' => \DB::raw("total_price + " . (float) $totalNewCost)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Guest composition saved successfully',
                'added_cost' => $totalNewCost
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save guest composition: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAddon(Request $request, $addonId)
    {
        try {
            $bookingId = $request->input('booking_id');

            // Find the addon and verify it belongs to the booking
            $addon = GuestAddons::where('id', $addonId)
                ->where('facility_booking_log_id', $bookingId)
                ->firstOrFail();

            $costToRollback = $addon->total_cost;

            // Fetch booking log with details
            $bookingLog = FacilityBookingLog::with('details')->find($bookingId);

            if ($bookingLog && $bookingLog->details()->exists()) {
                // Subtract the addon cost from all detail rows
                $bookingLog->details()->update([
                    'total_price' => \DB::raw("total_price - " . (float) $costToRollback)
                ]);
            }

            // Now delete the addon
            $addon->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guest addon deleted successfully',
                'rolled_back_cost' => $costToRollback
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete guest addon: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelBooking(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Find the booking
            $booking = FacilityBookingLog::with(['payments', 'user', 'details'])
                ->findOrFail($id);

            // Check if booking can be cancelled
            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is already cancelled.'
                ], 400);
            }

            if ($booking->status === 'checked_out') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel a checked-out booking.'
                ], 400);
            }

            // Get cancellation data
            $refundType = $request->input('refund_type', 'non_refundable');
            $refundAmountType = $request->input('refund_amount_type', 'full');
            $reason = $request->input('reason', 'Cancelled by admin');

            // Calculate refund amount
            $refundAmount = 0;
            $payment = $booking->payments->first();

            if ($refundType === 'refundable' && $payment) {
                $totalPaid = ($payment->amount ?? 0) + ($payment->checkin_paid ?? 0);

                if ($refundAmountType === 'full') {
                    $refundAmount = $totalPaid;
                } elseif ($refundAmountType === 'half') {
                    $refundAmount = $totalPaid * 0.5;
                }

                // Update payment record with refund details
                $payment->update([
                    'refund_amount' => $refundAmount,
                    'refund_reason' => $reason,
                    'refund_date' => now(),
                    'refund_type' => $refundAmountType
                ]);
            } elseif ($payment) {
                // Non-refundable cancellation - just record the reason
                $payment->update([
                    'refund_amount' => 0,
                    'refund_reason' => $reason,
                    'refund_date' => now(),
                    'refund_type' => 'none'
                ]);
            }

            // Update booking status
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            // Send notification to user (you can implement this)
            $this->sendCancellationNotification($booking, $refundAmount, $reason);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully.',
                'data' => [
                    'booking_id' => $booking->id,
                    'refund_amount' => $refundAmount,
                    'refund_type' => $refundType,
                    'cancellation_reason' => $reason
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking cancellation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendCancellationNotification($booking, $refundAmount, $reason)
    {
        // Implement your notification logic here
        // This could be email, SMS, or in-app notification

        $user = $booking->user;
        $refundMessage = $refundAmount > 0
            ? "A refund of ₱" . number_format($refundAmount, 2) . " will be processed."
            : "This cancellation is non-refundable.";

        // Example email notification
        // Mail::to($user->email)->send(new BookingCancelledMail($booking, $refundAmount, $reason));

        Log::info('Cancellation notification sent', [
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'refund_amount' => $refundAmount,
            'reason' => $reason
        ]);
    }



}
