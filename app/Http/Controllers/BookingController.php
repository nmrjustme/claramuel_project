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
use App\Models\GuestType;
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
            'guestDetails.guestType'
        ]);

        return response()->json([
            'data' => $booking,
        ]);
    }

    public function getMyBookings(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $status = $request->input('status', '');
            $readStatus = $request->input('read_status', '');

            $query = FacilityBookingLog::with(['user', 'payments'])
                ->orderBy('created_at', 'desc');

            // 🔍 Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%$search%")
                        ->orWhere('code', 'like', "%$search%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('firstname', 'like', "%$search%")
                                ->orWhere('lastname', 'like', "%$search%");
                        });
                });
            }

            // 🎯 Status filtering (aligned with display status)
            if ($status) {
                switch ($status) {
                    case 'fully_paid':
                        $query->where(function ($q) {
                            $q->whereIn('status', ['pending_confirmation', 'confirmed'])
                                ->whereHas('payments', function ($paymentQuery) {
                                    $paymentQuery->where('remaining_balance_status', 'fully_paid');
                                });
                        });
                        break;

                    case 'verified':
                        $query->where(function ($q) {
                            $q->whereIn('status', ['pending_confirmation', 'confirmed'])
                                ->whereHas('payments', function ($paymentQuery) {
                                    $paymentQuery->where('status', 'verified')
                                        ->where('remaining_balance_status', '!=', 'fully_paid');
                                });
                        });
                        break;

                    case 'checked_in':
                        $query->where('status', 'checked_in');
                        break;

                    case 'checked_out':
                        $query->where('status', 'checked_out');
                        break;

                    case 'cancelled':
                        $query->where('status', 'cancelled');
                        break;

                    case 'no_show':
                        $query->where('status', 'no_show');
                        break;

                    case 'pending_confirmation':
                        $query->where('status', 'pending_confirmation');
                        break;

                    case 'confirmed':
                        $query->where('status', 'confirmed');
                        break;

                    case 'rejected':
                        $query->where('status', 'rejected');
                        break;
                }
            }

            // 👀 Read status filtering
            if ($readStatus) {
                switch ($readStatus) {
                    case 'read':
                        $query->where('is_read', true);
                        break;
                    case 'unread':
                        $query->where('is_read', false);
                        break;
                }
            }

            $bookings = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $bookings->map(function ($booking) {
                    // ✅ Display status logic
                    $displayStatus = $booking->status;

                    // Only override with payment status if booking is not yet checked in/out/cancelled
                    if (in_array($booking->status, ['pending_confirmation', 'confirmed'])) {
                        if ($booking->payments && count($booking->payments) > 0) {
                            $latestPayment = $booking->payments->sortByDesc('created_at')->first();

                            if ($latestPayment->remaining_balance_status === 'fully_paid') {
                                $displayStatus = 'fully_paid';
                            } elseif ($latestPayment->status === 'verified') {
                                $displayStatus = 'verified';
                            }
                        }
                    }

                    return [
                        'id' => $booking->id,
                        'user' => [
                            'firstname' => $booking->user->firstname,
                            'lastname' => $booking->user->lastname,
                            'email' => $booking->user->email,
                        ],
                        'created_at' => $booking->created_at->toDateTimeString(),
                        'status' => $displayStatus,
                        'is_read' => (bool) $booking->is_read,
                        'code' => $booking->code,
                        'payments' => $booking->payments,
                        'details' => $booking->details,
                        'summaries' => $booking->summaries,
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



    // public function getMyInquiries(Request $request)
    // {
    //     try {
    //         $perPage = $request->input('per_page', 15);
    //         $page = $request->input('page', 1);
    //         $search = $request->input('search', '');
    //         $status = $request->input('status', '');
    //         $readStatus = $request->input('read_status', '');

    //         $query = FacilityBookingLog::with([
    //             'user',
    //             'summaries.facility',
    //             'summaries.breakfast',
    //             'details',
    //             'payments',
    //         ])
    //         ->whereIn('status', [
    //             'pending_confirmation',
    //             'confirmed',
    //             'rejected'
    //         ])
    //         // 🔍 Search
    //         ->when($search, function($query) use ($search) {
    //             if (is_numeric($search)) {
    //                 $query->where('id', $search);
    //             } else {
    //                 $query->where(function ($q) use ($search) {
    //                     $q->whereHas('user', function($uq) use ($search) {
    //                         $uq->where('firstname', 'like', "%{$search}%")
    //                         ->orWhere('lastname', 'like', "%{$search}%")
    //                         ->orWhere('email', 'like', "%{$search}%")
    //                         ->orWhere('phone', 'like', "%{$search}%");
    //                     })
    //                     ->orWhere('code', 'like', "%{$search}%");
    //                 });
    //             }
    //         })
    //         // 📌 Status filtering
    //         ->when($status, function($query) use ($status) {
    //             if (in_array($status, ['pending_confirmation', 'confirmed', 'rejected'])) {
    //                 $query->where('status', $status);
    //             }
    //         })
    //         // 📌 Read status filtering
    //         ->when($readStatus, function($query) use ($readStatus) {
    //             if (in_array($readStatus, ['read', 'unread'])) {
    //                 $query->where('is_read', $readStatus === 'read');
    //             }
    //         })
    //         ->orderBy('created_at', 'desc')
    //         ->whereIn('status', [ 'pending_confirmation', 'confirmed', 'rejected' ]);

    //         // 📌 Status filtering
    //         if ($status) {
    //             switch ($status) {
    //                 case 'pending_confirmation':
    //                 case 'confirmed':
    //                 case 'rejected':
    //                     $query->where('status', $status);
    //                     break;
    //                 default:
    //                     // No additional filtering for 'all'
    //                     break;
    //             }
    //         }

    //         // 📌 Read status filtering
    //         if ($readStatus && in_array($readStatus, ['read', 'unread'])) {
    //             $query->where('is_read', $readStatus === 'read');
    //         }

    //         $bookings = $query->paginate($perPage, ['*'], 'page', $page);

    //         return response()->json([
    //             'data' => $bookings->map(function ($booking) {
    //                 return [
    //                     'id' => $booking->id,
    //                     'user' => [
    //                         'firstname' => $booking->user->firstname ?? null,
    //                         'lastname' => $booking->user->lastname ?? null,
    //                         'email' => $booking->user->email ?? null,
    //                     ],
    //                     'is_read' => (bool) $booking->is_read,
    //                     'status' => $booking->status, // ✅ Pure status only
    //                     'code' => $booking->code,
    //                     'created_at' => $booking->created_at->toDateTimeString(),
    //                 ];
    //             }),
    //             'current_page' => $bookings->currentPage(),
    //             'per_page' => $bookings->perPage(),
    //             'last_page' => $bookings->lastPage(),
    //             'total' => $bookings->total()
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Server Error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

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
}
