<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use App\Models\DayTourLogDetails;
use App\Models\BookingGuestDetails;
use App\Models\GuestType;
use App\Models\User;
use App\Models\FacilityBookingDetails;
use App\Models\FacilityBookingLog;
use App\Models\FacilitySummary;
use Illuminate\Support\Facades\Log; // Added for logging 

class Day_tour_Controller extends Controller
{
    // Display the day tour registration form
    public function index(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        // Get all facilities
        $facilities = Facility::all();
        
        // Calculate availability for each facility using BookingGuestDetails
         $cottages = $facilities->where('category', 'Cottage');
         $villas   = $facilities->where('category', 'Private Villa');

        $guestTypes = GuestType::all();

        return view('admin.daytour.index', compact('facilities','cottages', 'villas', 'guestTypes', 'date'));
    }


// Add this helper method to your controller
private function getServiceType($log)
{
    $poolCount = 0;
    $parkCount = 0;

    foreach ($log->bookingGuestDetails->where('facility_id', null) as $guest) {
        if ($guest->quantity > 0 && $guest->guestType) {
            if ($guest->guestType->location === 'Pool') {
                $poolCount += $guest->quantity;
            } elseif ($guest->guestType->location === 'Park') {
                $parkCount += $guest->quantity;
            }
        }
    }

    if ($poolCount > 0 && $parkCount > 0) {
        return [
            'type' => 'Both',
            'pool_count' => $poolCount,
            'park_count' => $parkCount,
            'total' => $poolCount + $parkCount
        ];
    } elseif ($poolCount > 0) {
        return [
            'type' => 'Pool',
            'pool_count' => $poolCount,
            'park_count' => 0,
            'total' => $poolCount
        ];
    } elseif ($parkCount > 0) {
        return [
            'type' => 'Park',
            'park_count' => $parkCount,
            'pool_count' => 0,
            'total' => $parkCount
        ];
    }

    return [
        'type' => 'Unknown',
        'pool_count' => 0,
        'park_count' => 0,
        'total' => 0
    ];
}

// Update your show method to include service type
public function show($id)
{
    $log = DayTourLogDetails::with('user', 'bookingGuestDetails.guestType', 'bookingGuestDetails.facility')
            ->findOrFail($id);

    // Count how many guests are in each location
    $poolCount = $log->bookingGuestDetails
                     ->where('guestType.location', 'Pool')
                     ->sum('quantity');

    $parkCount = $log->bookingGuestDetails
                     ->where('guestType.location', 'Park')
                     ->sum('quantity');

    // Determine service type based on counts
    if ($poolCount > 0 && $parkCount > 0) {
        $serviceTypeLabel = 'Both';
    } elseif ($poolCount > 0) {
        $serviceTypeLabel = 'Pool';
    } elseif ($parkCount > 0) {
        $serviceTypeLabel = 'Park';
    } else {
        $serviceTypeLabel = 'Day Tour'; // fallback
    }

    $serviceType = [
        'type' => $serviceTypeLabel,
        'total' => $log->bookingGuestDetails->sum('quantity'),
        'pool_count' => $poolCount,
        'park_count' => $parkCount,
    ];

    // Determine check-in / check-out status
    if ($log->checked_in_at && !$log->checked_out_at) {
        $status = 'checked_in';
    } elseif ($log->checked_out_at) {
        $status = 'checked_out';
    } else {
        $status = $log->reservation_status; // fallback (pending/approved/etc.)
    }

    return view('admin.daytour.logs_show', compact('log', 'serviceType', 'status'));
}



public function edit($id)
{
    $log = DayTourLogDetails::with([
        'user',
        'bookingGuestDetails.guestType',
        'bookingGuestDetails.facility'
    ])->findOrFail($id);

    $guestTypes = GuestType::all();

    // Only Cottages and Villas
    $facilities = Facility::whereIn('category', ['Cottage', 'Private Villa', 'Villa'])->get();

    // Separate booking details by guest vs facility
    $guestDetails = [];
    $facilityDetails = [];
    foreach ($log->bookingGuestDetails as $detail) {
        if ($detail->facility_id === null) {
            $guestDetails[$detail->guest_type_id] = [
                'quantity' => $detail->quantity,
                'guest_type_id' => $detail->guest_type_id
            ];
        } else {
            $facilityDetails[$detail->facility_id] = [
                'facility_quantity' => $detail->facility_quantity,
                'facility_id' => $detail->facility_id
            ];
        }
    }

    // Calculate availability using logic similar to checkAvailability
    $facilityAvailability = $facilities->mapWithKeys(function ($facility) use ($log, $facilityDetails) {
        // Count confirmed bookings excluding the current log
        $alreadyBooked = BookingGuestDetails::where('facility_id', $facility->id)
            ->whereHas('dayTourLog', function ($q) use ($log) {
                $q->whereDate('date_tour', $log->date_tour)
                  ->where('id', '!=', $log->id)
                  ->whereIn('reservation_status', ['paid', 'approved'])
                  ->whereNull('checked_out_at');
            })
            ->sum('facility_quantity');

        $currentQty = $facilityDetails[$facility->id]['facility_quantity'] ?? 0;

        $available = max(0, $facility->quantity - $alreadyBooked);

        return [
            $facility->id => [
                'available'      => $available,
                'already_booked' => $alreadyBooked,
                'current_qty'    => $currentQty,
                'max_allowed'    => min($available + $currentQty, $facility->quantity),
                'status'         => $available > 0 ? 'Available' : 'Occupied'
            ]
        ];
    });

    return view('admin.daytour.logs_edit', compact(
        'log',
        'guestTypes',
        'facilities',
        'guestDetails',
        'facilityDetails',
        'facilityAvailability'
    ));
}


public function update(Request $request, $id)
{
    $log = DayTourLogDetails::findOrFail($id);

    $validated = $request->validate([
        'guest_types' => 'required|array',
        'guest_types.*.quantity' => 'nullable|integer|min:0',
        'facilities' => 'nullable|array',
        'facilities.*.facility_quantity' => 'nullable|integer|min:0',
        'date_tour' => 'required|date',
        'reservation_status' => 'required|in:pending,paid,approved,rejected',
        'status' => 'nullable|in:checked_in,checked_out',
    ]);

    DB::beginTransaction();

    try {
        // Default guest type for facility bookings
        $defaultGuestType = GuestType::where('type', 'Adult')
            ->where('location', 'Pool')
            ->first() ?? GuestType::first();

        // 1. Recalculate total price
        $recalculatedTotal = 0;

        foreach ($validated['guest_types'] as $guestTypeId => $data) {
            $quantity = $data['quantity'] ?? 0;
            if ($quantity > 0) {
                $guestType = GuestType::find($guestTypeId);
                $recalculatedTotal += $quantity * ($guestType->rate ?? $guestType->price ?? 0);
            }
        }

        if (isset($validated['facilities'])) {
            $validFacilityIds = Facility::whereIn('category', ['Cottage', 'Villa', 'Private Villa'])
                ->pluck('id')->toArray();

            foreach ($validated['facilities'] as $facilityId => $data) {
                if (in_array($facilityId, $validFacilityIds)) {
                    $facilityQuantity = $data['facility_quantity'] ?? 0;
                    if ($facilityQuantity > 0) {
                        $facility = Facility::find($facilityId);
                        $recalculatedTotal += $facilityQuantity * ($facility->rate ?? $facility->price ?? 0);
                    }
                }
            }
        }

        // 2. Update log info
        $log->date_tour = $validated['date_tour'];
        $log->total_price = $recalculatedTotal;
        $log->reservation_status = $validated['reservation_status'];
        $log->status = $validated['status'];

        // 3. Handle check-in / check-out
        if (!empty($validated['status'])) {
            if ($validated['status'] === 'checked_in') {
                // ✅ Only allow check-in if today is the reservation date or later
                if (now()->toDateString() >= $log->date_tour) {
                    $log->checked_in_at = now();
                    $log->checked_out_at = null; // reset check-out
                    $log->status = 'Checked_in';
                } else {
                    // ❌ Prevent early check-in
                    return redirect()->back()
                        ->with('error', 'Guests cannot check in before their reservation date.')
                        ->withInput();
                }

            } elseif ($validated['status'] === 'checked_out') {
                // ✅ Only allow check-out if it's the reservation date or later AND already checked in
                if (now()->toDateString() < $log->date_tour) {
                    return redirect()->back()
                        ->with('error', 'Guests cannot check out before their reservation date.')
                        ->withInput();
                }

                if (!$log->checked_in_at) {
                    return redirect()->back()
                        ->with('error', 'Guests cannot check out without checking in first.')
                        ->withInput();
                }

                // Proceed with check-out
                $log->checked_out_at = now();
                $log->status = 'Checked_out';
            }
        }

        // 3b. Handle reservation status → overall status mapping
        if (empty($validated['status'])) {
            // Only if check-in/out was NOT explicitly triggered
            if (in_array($validated['reservation_status'], ['paid', 'approved'])) {
                if (now()->toDateString() < $log->date_tour) {
                    $log->status = 'Reserved'; // Future reservation
                } else {
                    // On the same day, but not yet checked in
                    $log->status = 'Awaiting Check-in';
                }
            } elseif ($validated['reservation_status'] === 'pending') {
                $log->status = 'Pending Payment';
            } elseif ($validated['reservation_status'] === 'rejected') {
                $log->status = 'Rejected';
            }
        }

        $log->save();

        // 4. Update guest type quantities
        foreach ($validated['guest_types'] as $guestTypeId => $data) {
            $quantity = $data['quantity'] ?? 0;

            if ($quantity > 0) {
                BookingGuestDetails::updateOrCreate(
                    [
                        'day_tour_log_details_id' => $log->id,
                        'guest_type_id' => $guestTypeId,
                        'facility_id' => null,
                    ],
                    [
                        'quantity' => $quantity,
                        'facility_quantity' => null,
                        'facility_id' => null,
                    ]
                );
            } else {
                BookingGuestDetails::where([
                    'day_tour_log_details_id' => $log->id,
                    'guest_type_id' => $guestTypeId,
                    'facility_id' => null,
                ])->delete();
            }
        }

        // 5. Update facility quantities
        if (isset($validated['facilities'])) {
            foreach ($validated['facilities'] as $facilityId => $data) {
                $facilityQuantity = $data['facility_quantity'] ?? 0;

                if ($facilityQuantity > 0) {
                    BookingGuestDetails::updateOrCreate(
                        [
                            'day_tour_log_details_id' => $log->id,
                            'facility_id' => $facilityId,
                        ],
                        [
                            'facility_quantity' => $facilityQuantity,
                            'quantity' => 0,
                            'guest_type_id' => $defaultGuestType->id,
                        ]
                    );
                } else {
                    BookingGuestDetails::where([
                        'day_tour_log_details_id' => $log->id,
                        'facility_id' => $facilityId,
                    ])->delete();
                }
            }
        }

        DB::commit();

        return redirect()->route('admin.daytour.logs.show', $log->id)
            ->with('success', 'Booking details updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Error updating booking details: ' . $e->getMessage())
            ->withInput();
    }
}


// ADD getPricingDetails() METHOD IF IT DOESN'T EXIST
    private function getPricingDetails($log)
    {
        $poolSubtotal = 0;
        $parkSubtotal = 0;
        $poolDetails = [];
        $parkDetails = [];
        
        // Calculate guest fees separated by Pool and Park
        foreach ($log->bookingGuestDetails->where('facility_id', null) as $guest) {
            if ($guest->quantity > 0 && $guest->guestType) {
                $typeName = $guest->guestType->type;
                $rate = $guest->guestType->rate ?? $guest->guestType->price ?? 0;
                $total = $rate * $guest->quantity;
                $location = $guest->guestType->location ?? 'Unknown';
                
                if ($location === 'Pool') {
                    $poolSubtotal += $total;
                    if (!isset($poolDetails[$typeName])) {
                        $poolDetails[$typeName] = [
                            'quantity' => 0,
                            'total' => 0,
                            'rate' => $rate,
                            'location' => 'Pool'
                        ];
                    }
                    $poolDetails[$typeName]['quantity'] += $guest->quantity;
                    $poolDetails[$typeName]['total'] += $total;
                } elseif ($location === 'Park') {
                    $parkSubtotal += $total;
                    if (!isset($parkDetails[$typeName])) {
                        $parkDetails[$typeName] = [
                            'quantity' => 0,
                            'total' => 0,
                            'rate' => $rate,
                            'location' => 'Park'
                        ];
                    }
                    $parkDetails[$typeName]['quantity'] += $guest->quantity;
                    $parkDetails[$typeName]['total'] += $total;
                }
            }
        }
// Calculate accommodation fees
        $accommodationSubtotal = 0;
        $accommodationDetails = [];
        
        foreach ($log->bookingGuestDetails->where('facility_id', '!=', null) as $accommodation) {
            if ($accommodation->facility_quantity > 0 && $accommodation->facility) {
                $facilityName = $accommodation->facility->name;
                $rate = $accommodation->facility->rate ?? $accommodation->facility->price ?? 0;
                $total = $rate * $accommodation->facility_quantity;
                $accommodationSubtotal += $total;
                
                if (!isset($accommodationDetails[$facilityName])) {
                    $accommodationDetails[$facilityName] = [
                        'quantity' => 0,
                        'total' => 0,
                        'rate' => $rate
                    ];
                }
                $accommodationDetails[$facilityName]['quantity'] += $accommodation->facility_quantity;
                $accommodationDetails[$facilityName]['total'] += $total;
            }
        }

        $guestSubtotal = $poolSubtotal + $parkSubtotal;

        return [
            'pool_details' => $poolDetails,
            'park_details' => $parkDetails,
            'pool_subtotal' => $poolSubtotal,
            'park_subtotal' => $parkSubtotal,
            'guest_subtotal' => $guestSubtotal,
            'accommodation_details' => $accommodationDetails,
            'accommodation_subtotal' => $accommodationSubtotal,
            'total_amount' => $guestSubtotal + $accommodationSubtotal
        ];
    }

    // ADD THE PRINT METHOD
    public function print($id)
    {
        $log = DayTourLogDetails::with([
            'user',
            'bookingGuestDetails.guestType',
            'bookingGuestDetails.facility',
        ])->findOrFail($id);

        $serviceType = $this->getServiceType($log);
        $pricingDetails = $this->getPricingDetails($log);

        return view('admin.daytour.logs_print', compact('log', 'serviceType', 'pricingDetails'));
    }


public function getCottages($date)
{
    // Get cottage facility
    $facility = Facility::where('category', 'Cottage')->first();

    if (!$facility) {
        return response()->json(['error' => 'No cottage facility found'], 404);
    }

    // Count booked quantity for the given date
    $bookedQty = BookingGuestDetails::where('facility_id', $facility->id)
        ->whereHas('dayTourLog', function ($q) use ($date) {
            $q->where('date_tour', $date);
        })
        ->sum('facility_quantity');

    // Generate "virtual" list of cottages
    $cottages = [];
    for ($i = 1; $i <= $facility->quantity; $i++) {
        $cottages[] = [
            'name'   => "Cottage #{$i}",
            'status' => $i <= $bookedQty ? 'Occupied' : 'Available',
        ];
    }

    return response()->json($cottages);
}


public function store(Request $request)
{
    // 1️⃣ Validate request
    $request->validate([
        'first_name'   => 'required|string|max:255',
        'last_name'    => 'required|string|max:255',
        'phone'        => 'required|string|max:20',
        'email'        => 'nullable|email|max:255',
        'date_tour'    => 'required|date|after_or_equal:today',
        'reservation_status'=> 'required|string|in:pending,paid,approved',
        'status'=> 'nullable|string|in:pending,paid,approved,reserved',
        'service_type' => 'required|string|in:pool,themed_park,both',
    ]);

    $serviceType = $request->service_type;

    // 2️⃣ Count guests per category
    $totalPool = ($request->pool_adult ?? 0) + ($request->pool_kids ?? 0) + ($request->pool_seniors ?? 0);
    $totalPark = ($request->park_adult ?? 0) + ($request->park_kids ?? 0) + ($request->park_seniors ?? 0);

    // 3️⃣ Validate before touching DB
    if ($serviceType === 'pool' && $totalPool < 1) {
        return back()->with('error', 'Please add at least 1 Pool guest.')->withInput();
    }

    if ($serviceType === 'themed_park' && $totalPark < 1) {
        return back()->with('error', 'Please add at least 1 Park guest.')->withInput();
    }

    if ($serviceType === 'both' && ($totalPool < 1 || $totalPark < 1)) {
        return back()->with('error', 'Please add at least 1 guest for both Pool and Park.')->withInput();
    }

    DB::beginTransaction();

    try {
        // ✅ Always create a new user (email optional)
        $user = User::create([
            'firstname' => $request->first_name,
            'lastname'  => $request->last_name,
            'phone'     => $request->phone,
        ]);

        // 2️⃣ Create Day Tour Log
        $dayTourLog = DayTourLogDetails::create([
            'user_id'     => $user->id,
            'date_tour'   => $request->date_tour,
            'approved_by' => auth()->id(),
            'reservation_status'=> $request->reservation_status,
            'total_price' => 0,
            'status' => match ($request->reservation_status) {
            'paid', 'approved' => \Carbon\Carbon::parse($request->date_tour)->isToday()
                                ? 'Checked_in'
                                : 'Reserved',
        'pending'  => 'Pending Payment',
        'rejected' => 'Rejected',
        default    => 'Pending Payment',
    },
        ]);

        // ✅ Auto check-in ONLY if paid or approved AND today
if (
    in_array($request->reservation_status, ['paid', 'approved']) &&
    \Carbon\Carbon::parse($request->date_tour)->isToday()
) {
    $dayTourLog->update([
        'checked_in_at' => now(),
        'status' => 'Checked_in',
    ]);
}
        $totalPrice  = 0;
        $serviceType = $request->service_type;

        // 3️⃣ Save Pool Guest Types
        if (in_array($serviceType, ['pool', 'both'])) {
            $poolGuests = [
                'pool_adult'   => $request->pool_adult ?? 0,
                'pool_kids'    => $request->pool_kids ?? 0,
                'pool_seniors' => $request->pool_seniors ?? 0,
            ];

            foreach ($poolGuests as $key => $quantity) {
                if ($quantity > 0) {
                    $guestTypeName = ucfirst(str_replace('pool_', '', $key));
                    $guestType     = GuestType::where('type', $guestTypeName)
                        ->where('location', 'Pool')
                        ->first();

                    if ($guestType) {
                        BookingGuestDetails::create([
                            'guest_type_id'            => $guestType->id,
                            'day_tour_log_details_id'  => $dayTourLog->id,
                            'facility_id'              => null,
                            'facility_booking_log_id'  => null,
                            'quantity'                 => $quantity,
                            'facility_quantity'        => null,
                        ]);
                        $totalPrice += $guestType->rate * $quantity;
                    }
                }
            }
        }

        // 4️⃣ Save Park Guest Types
        if (in_array($serviceType, ['themed_park', 'both'])) {
            $parkGuests = [
                'park_adult'   => $request->park_adult ?? 0,
                'park_kids'    => $request->park_kids ?? 0,
                'park_seniors' => $request->park_seniors ?? 0,
            ];

            foreach ($parkGuests as $key => $quantity) {
                if ($quantity > 0) {
                    $guestTypeName = ucfirst(str_replace('park_', '', $key));
                    $guestType     = GuestType::where('type', $guestTypeName)
                        ->where('location', 'Park')
                        ->first();

                    if ($guestType) {
                        BookingGuestDetails::create([
                            'guest_type_id'            => $guestType->id,
                            'day_tour_log_details_id'  => $dayTourLog->id,
                            'facility_id'              => null,
                            'facility_booking_log_id'  => null,
                            'quantity'                 => $quantity,
                            'facility_quantity'        => null,
                        ]);
                        $totalPrice += $guestType->rate * $quantity;
                    }
                }
            }
        }

        // 5️⃣ Accommodations handling (cottages/villas)
        $accommodationInputs = $request->accommodations ?? [];

foreach ($accommodationInputs as $facilityId => $qty) {
    if ($qty <= 0) continue;

    $availability = $this->calculateFacilityAvailability($facilityId, $request->date_tour);

    if ($availability['available'] < $qty) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', "Sorry, only {$availability['available']} unit(s) available for {$availability['name']} on {$request->date_tour}")
            ->withInput();
    }

    // Book the available units
    $facility   = Facility::find($facilityId);
    $qtyToBook  = min($qty, $availability['available']);

    $defaultGuestType = GuestType::where('type', 'Adult')
        ->where('location', 'Pool')
        ->first() ?? GuestType::first();

    BookingGuestDetails::create([
        'day_tour_log_details_id' => $dayTourLog->id,
        'facility_id'             => $facilityId,
        'facility_quantity'       => $qtyToBook,
        'quantity'                => 0,
        'guest_type_id'           => $defaultGuestType->id,
        'facility_booking_log_id' => null,
    ]);

    $totalPrice += $facility->price * $qtyToBook;
}


        // 6️⃣ Update total price
        $dayTourLog->update(['total_price' => $totalPrice]);

        DB::commit();
        return redirect()->back()->with('success', 'Day Tour registered successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}

private function calculateFacilityAvailability($facilityId, $date)
{
    $facility = Facility::find($facilityId);
    if (!$facility) {
        return [
            'id' => $facilityId,
            'name' => 'Unknown',
            'available' => 0,
            'already_booked' => 0,
            'total' => 0,
            'price' => 0,
        ];
    }

    // Count only confirmed bookings that have not checked out
    $alreadyBooked = BookingGuestDetails::where('facility_id', $facilityId)
        ->whereHas('dayTourLog', function ($q) use ($date) {
            $q->whereDate('date_tour', $date)
              ->whereIn('reservation_status', ['approved', 'paid']) // only confirmed bookings
              ->whereNull('checked_out_at'); // ignore checked-out
        })
        ->sum('facility_quantity');

    return [
        'id' => $facility->id,
        'name' => $facility->name,
        'available' => max($facility->quantity - $alreadyBooked, 0),
        'already_booked' => $alreadyBooked,
        'total' => $facility->quantity,
        'price' => $facility->price,
    ];
}


public function checkAvailability(Request $request)
{
    $date = $request->input('date');

    // Only cottages and private villas
    $facilities = Facility::whereIn('category', ['Cottage', 'Private Villa'])->get();

    $availability = $facilities->map(function ($facility) use ($date) {

        // Count only confirmed bookings that are currently occupying or will occupy the facility
        $alreadyBooked = BookingGuestDetails::where('facility_id', $facility->id)
            ->whereHas('dayTourLog', function ($q) use ($date) {
                $q->whereDate('date_tour', $date)
                  ->whereIn('reservation_status', ['paid', 'approved']) // Only confirmed bookings
                  ->where(function ($s) {
                      // Not checked out yet
                      $s->whereNull('checked_out_at');
                  });
            })
            ->sum('facility_quantity');

        $available = max(0, $facility->quantity - $alreadyBooked);

        return [
            'id'        => $facility->id,
            'name'      => $facility->name,
            'category'  => $facility->category,
            'price'     => $facility->price,
            'total'     => $facility->quantity,
            'available' => $available,
            'status'    => $available > 0 ? 'Available' : 'Occupied',
        ];
    });

    return response()->json($availability);
}



public function logs(Request $request)
    {
    // 1️⃣ Auto mark NO-SHOW for unpaid/pending guests whose date has passed
    DayTourLogDetails::whereDate('date_tour', '<', today())
        ->where('reservation_status', 'pending')
        ->whereNull('checked_in_at')
        ->update([
            'status' => 'No Show',
            'checked_out_at' => now(),
        ]);

    // 2️⃣ Auto check-out ONLY for paid/approved guests whose reservation date has passed
    DayTourLogDetails::whereDate('date_tour', '<', today())
        ->whereIn('reservation_status', ['approved', 'paid'])
        ->whereNull('checked_out_at')
        ->update([
            'checked_out_at' => now(),
            'status' => 'checked_out',
        ]);

    // 3️⃣ Auto check-in ONLY for today's paid/approved guests (not yet checked in)
    DayTourLogDetails::whereDate('date_tour', today())
        ->whereIn('reservation_status', ['approved', 'paid'])
        ->whereNull('checked_in_at')
        ->update([
            'checked_in_at' => now(),
            'status' => 'checked_in',
        ]);

        // Validate input - FIXED: Added service_type validation
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'reservation_status' => 'nullable|in:paid,pending,approved,rejected',
            'status' => 'nullable|in:checked_in,checked_out',
            'service_type' => 'nullable|string|in:Pool,Park,both', // FIXED: Added correct validation
            'guest_type' => 'nullable|string|max:255',
            'facility' => 'nullable|string|max:255',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
        ]);

        // Start query
        $query = DayTourLogDetails::with([
            'user:id,firstname,lastname,email,phone',
            'bookingGuestDetails.guestType',
            'bookingGuestDetails.facility'
        ])->orderBy('created_at', 'desc');

        // SUPER SEARCH - searches across ALL possible fields
        if ($validated['search'] ?? null) {
            $searchTerm = $validated['search'];
            $query->where(function($q) use ($searchTerm) {
                // Search user information
                $q->whereHas('user', function($q) use ($searchTerm) {
                    $q->where('firstname', 'like', "%{$searchTerm}%")
                      ->orWhere('lastname', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%")
                      ->orWhere('phone', 'like', "%{$searchTerm}%");
                })
                // Search by booking ID
                ->orWhere('id', 'like', "%{$searchTerm}%")
                ->orWhere('reservation_status', 'like', "%{$searchTerm}%")
                ->orWhere('total_price', 'like', "%{$searchTerm}%")
                ->orWhere('date_tour', 'like', "%{$searchTerm}%")
                // Search guest types
                ->orWhereHas('bookingGuestDetails.guestType', function($q) use ($searchTerm) {
                    $q->where('type', 'like', "%{$searchTerm}%")
                      ->orWhere('location', 'like', "%{$searchTerm}%");
                })
                // Search facilities
                ->orWhereHas('bookingGuestDetails.facility', function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                });
            });
        }

        // Date filters
        if ($validated['date_from'] ?? null && $validated['date_to'] ?? null) {
            $query->whereBetween('date_tour', [
                $validated['date_from'],
                $validated['date_to']
            ]);
        } elseif ($validated['date'] ?? null) {
            $query->where('date_tour', $validated['date']);
        }

        // Status filter
        if ($validated['reservation_status'] ?? null) {
            $query->where('reservation_status', $validated['reservation_status']);
        }

         // Status filter for checked_in / checked_out
        if ($validated['status'] ?? null) {
            $query->where('status', $validated['status']);
        }

        // Price range filter
        if ($validated['min_price'] ?? null) {
            $query->where('total_price', '>=', $validated['min_price']);
        }
        if ($validated['max_price'] ?? null) {
            $query->where('total_price', '<=', $validated['max_price']);
        }

        // Service type filter - FIXED: Corrected the logic
        if ($validated['service_type'] ?? null) {
            $serviceType = $validated['service_type'];
            $query->whereHas('bookingGuestDetails', function($q) use ($serviceType) {
                $q->where('facility_id', null)
                  ->where('quantity', '>', 0)
                  ->whereHas('guestType', function($q) use ($serviceType) {
                      if ($serviceType === 'Pool') {
                          $q->where('location', 'Pool');
                      } elseif ($serviceType === 'Park') {
                          $q->where('location', 'Park');
                      } elseif ($serviceType === 'both') {
                          $q->whereIn('location', ['Pool', 'Park']);
                      }
                  });
            });
        }

        // Guest type filter
        if ($validated['guest_type'] ?? null) {
            $query->whereHas('bookingGuestDetails', function($q) use ($validated) {
                $q->where('facility_id', null)
                  ->where('quantity', '>', 0)
                  ->whereHas('guestType', function($q) use ($validated) {
                      $q->where('type', 'like', "%{$validated['guest_type']}%");
                  });
            });
        }

        // Facility filter
        if ($validated['facility'] ?? null) {
            $query->whereHas('bookingGuestDetails', function($q) use ($validated) {
                $q->where('facility_id', '!=', null)
                  ->where('facility_quantity', '>', 0)
                  ->whereHas('facility', function($q) use ($validated) {
                      $q->where('name', 'like', "%{$validated['facility']}%");
                  });
            });
        }

        $logs = $query->paginate(20)->appends($request->except('page'));

        return view('admin.daytour.day_tour_logs', compact('logs'));
    }

/**
 * Display cottage and villa monitoring dashboard
 */
public function monitorFacilities(Request $request)
{
    $validated = $request->validate([
        'date' => 'nullable|date',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
        'facility_type' => 'nullable|string|in:cottage,villa,both,other,all',
        'status' => 'nullable|string|in:available,occupied,maintenance,cleaning',
    ]);

    $date = $validated['date'] ?? now()->toDateString();
    $dateFrom = $validated['date_from'] ?? null;
    $dateTo = $validated['date_to'] ?? null;
    $facilityType = $validated['facility_type'] ?? 'all';

    // --------------------------
    // COTTAGES & VILLAS
    // --------------------------
    $query = Facility::query();

    if ($facilityType === 'cottage') {
        $query->where('category', 'Cottage');
    } elseif ($facilityType === 'villa') {
        $query->where('category', 'Private Villa');
    } elseif ($facilityType === 'both' || $facilityType === 'all') {
        $query->whereIn('category', ['Cottage', 'Private Villa']);
    }

    $cottagesVillas = $query->get();

    $allBookings = BookingGuestDetails::with(['dayTourLog.user'])
        ->whereIn('facility_id', $cottagesVillas->pluck('id'))
        ->whereHas('dayTourLog', function($q) use ($date, $dateFrom, $dateTo) {
            if ($dateFrom && $dateTo) {
                $q->whereBetween(DB::raw('DATE(date_tour)'), [$dateFrom, $dateTo]);
            } else {
                $q->whereDate('date_tour', $date);
            }
            $q->where('reservation_status', '!=', 'pending');
        })
        ->get()
        ->groupBy('facility_id');

   $cottagesVillas = $cottagesVillas->map(function($facility) use ($allBookings) {
    $facilityBookings = $allBookings->get($facility->id, collect());

    // Maintenance / cleaning override
    if (in_array($facility->status, ['maintenance','cleaning'])) {
        return $facility->setAttribute('display_status', $facility->status)
                        ->setAttribute('available', 0)
                        ->setAttribute('booked', 0)
                        ->setAttribute('occupancy_rate', 0)
                        ->setAttribute('bookings', collect())
                        ->setAttribute('units', []);
    }

    // Active bookings: currently occupying or approved/paid but not yet checked out
    $activeBookings = $facilityBookings->filter(function($b) {
        $log = $b->dayTourLog;
        return $log && !$log->checked_out_at && 
               (in_array($log->reservation_status, ['approved','paid']) || $log->checked_in_at);
    });

    $bookedQty = $activeBookings->sum(fn($b) => $b->facility_quantity ?? $b->quantity ?? 0);
    $available = max(0, $facility->quantity - $bookedQty);

    // Display status logic
    $displayStatus = $bookedQty > 0 ? 'occupied' : 'available';

    // Bookings list for UI
    $bookingsList = $facilityBookings->groupBy('day_tour_log_details_id')->map(function($group) {
        $log = $group->first()->dayTourLog;
        return [
            'booking_id'     => $log->id,
            'customer'       => $log->user ? $log->user->firstname . ' ' . $log->user->lastname : 'Unknown',
            'date'           => $log->date_tour,
            'quantity'       => $group->sum(fn($b) => $b->facility_quantity ?? $b->quantity ?? 0),
            'status'         => $log->checked_in_at && !$log->checked_out_at ? 'checked_in'
                               : ($log->checked_out_at ? 'checked_out' : $log->reservation_status),
            'checked_in_at'  => $log->checked_in_at,
            'checked_out_at' => $log->checked_out_at,
        ];
    })->values();

    // Units
    $units = [];
    for ($i = 1; $i <= $facility->quantity; $i++) {
        $units[] = [
            'name'   => "{$facility->name} #{$i}",
            'status' => $i <= $bookedQty ? 'Occupied' : 'Available',
        ];
    }

    return $facility->setAttribute('display_status', $displayStatus)
                    ->setAttribute('booked', $bookedQty)
                    ->setAttribute('available', $available)
                    ->setAttribute('occupancy_rate', $facility->quantity > 0 ? round(($bookedQty / $facility->quantity) * 100, 2) : 0)
                    ->setAttribute('bookings', $bookingsList)
                    ->setAttribute('units', $units);
});

    // --------------------------
    // OTHER FACILITIES / ROOMS
    // --------------------------
    $otherFacilities = Facility::where('type', 'room')->get()->map(function($facility) use ($date, $dateFrom, $dateTo) {
        $query = DB::table('facilities as fac')
            ->join('facility_summary as fac_sum', 'fac_sum.facility_id', '=', 'fac.id')
            ->join('facility_booking_details as fac_details', 'fac_details.facility_summary_id', '=', 'fac_sum.id')
            ->join('facility_booking_log as fac_log', 'fac_log.id', '=', 'fac_details.facility_booking_log_id')
            ->join('payments', 'payments.facility_log_id', '=', 'fac_log.id')
            ->join('users', 'users.id', '=', 'fac_log.user_id')
            ->where('fac.id', $facility->id)
            ->where('fac_log.status', '!=', 'pending_confirmation')
            ->where('payments.status', 'verified');

        if ($dateFrom && $dateTo) {
            $query->where(function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('fac_details.checkin_date', [$dateFrom, $dateTo])
                  ->orWhereBetween('fac_details.checkout_date', [$dateFrom, $dateTo]);
            });
        } else {
            $query->whereDate('fac_details.checkin_date', '<=', $date)
                  ->whereDate('fac_details.checkout_date', '>=', $date);
        }

        $unavailableDates = $query->select('fac_details.checkin_date', 'fac_details.checkout_date', 'users.firstname', 'users.lastname')->get();
        $bookedQty = $unavailableDates->count();
        $available = max(0, $facility->quantity - $bookedQty);

        $facility->setAttribute('display_status', $available > 0 ? 'available' : 'occupied');
        $facility->setAttribute('booked', $bookedQty);
        $facility->setAttribute('available', $available);
        $facility->setAttribute('occupancy_rate', $facility->quantity > 0 ? round(($bookedQty / $facility->quantity) * 100, 2) : 0);

        $units = [];
        for ($i = 1; $i <= $facility->quantity; $i++) {
            $units[] = [
                'name' => "{$facility->name} #{$i}",
                'status' => $i <= $bookedQty ? 'Occupied' : 'Available',
            ];
        }
        $facility->setAttribute('units', $units);

        $facility->setAttribute('bookings', $unavailableDates->map(function($d) {
            return [
                'customer' => $d->firstname . ' ' . $d->lastname,
                'date' => $d->checkin_date,
                'quantity' => 1,
                'status' => 'verified',
            ];
        }));

        return $facility;
    });

    // --------------------------
    // MERGE & SUMMARY
    // --------------------------
    $facilities = $cottagesVillas->merge($otherFacilities);

    $activeFacilities = $facilities->whereNotIn('display_status', ['maintenance','cleaning']);
    $totalCapacity = $activeFacilities->sum('available') + $activeFacilities->sum('booked');
    $totalBooked = $activeFacilities->sum('booked');

    $summary = [
        'total_facilities' => $facilities->count(),
        'total_available' => $activeFacilities->sum('available'),
        'total_booked' => $totalBooked,
        'total_capacity' => $totalCapacity,
        'overall_occupancy' => $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0,
        'maintenance_count' => $facilities->where('display_status','maintenance')->count(),
        'cleaning_count' => $facilities->where('display_status','cleaning')->count(),
    ];

    return view('admin.daytour.facility_monitoring', compact(
        'facilities','summary','date','dateFrom','dateTo','facilityType'
    
    ));
}


public function checkin($id)
{
    $dayTour = DayTourLogDetails::findOrFail($id);
    if (!$dayTour->checked_in_at) {
        $dayTour->checked_in_at = now();
        $dayTour->status = 'checked_in';
        $dayTour->save();
    }
    return back()->with('success','Checked in successfully!');
}


public function checkout($id)
{
    $dayTour = DayTourLogDetails::findOrFail($id);

    if (!$dayTour->checked_out_at) {
        $dayTour->checked_out_at = now();
        $dayTour->status = 'checked_out'; // optional
        $dayTour->save();
    }

    return back()->with('success', 'Checked out successfully!');
}

public function showAccomodation(Request $request)
{
    $validated = $request->validate([
        'date' => 'nullable|date',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
        'facility_type' => 'nullable|string|in:cottage,villa,both,other,all',
        'status' => 'nullable|string|in:available,occupied,maintenance,cleaning',
    ]);

    $date = $validated['date'] ?? now()->toDateString();
    $dateFrom = $validated['date_from'] ?? null;
    $dateTo = $validated['date_to'] ?? null;
    $facilityType = $validated['facility_type'] ?? 'all';

    // --------------------------
    // COTTAGES & VILLAS
    // --------------------------
    $query = Facility::query();

    if ($facilityType === 'cottage') {
        $query->where('category', 'Cottage');
    } elseif ($facilityType === 'villa') {
        $query->where('category', 'Private Villa');
    } elseif ($facilityType === 'both' || $facilityType === 'all') {
        $query->whereIn('category', ['Cottage', 'Private Villa']);
    }

    $cottagesVillas = $query->get();

    $allBookings = BookingGuestDetails::with(['dayTourLog.user'])
        ->whereIn('facility_id', $cottagesVillas->pluck('id'))
        ->whereHas('dayTourLog', function($q) use ($date, $dateFrom, $dateTo) {
            if ($dateFrom && $dateTo) {
                $q->whereBetween(DB::raw('DATE(date_tour)'), [$dateFrom, $dateTo]);
            } else {
                $q->whereDate('date_tour', $date);
            }
            $q->where('reservation_status', '!=', 'pending');
        })
        ->get()
        ->groupBy('facility_id');

   $cottagesVillas = $cottagesVillas->map(function($facility) use ($allBookings) {
    $facilityBookings = $allBookings->get($facility->id, collect());

    // Maintenance / cleaning override
    if (in_array($facility->status, ['maintenance','cleaning'])) {
        return $facility->setAttribute('display_status', $facility->status)
                        ->setAttribute('available', 0)
                        ->setAttribute('booked', 0)
                        ->setAttribute('occupancy_rate', 0)
                        ->setAttribute('bookings', collect())
                        ->setAttribute('units', []);
    }

    // Active bookings: currently occupying or approved/paid but not yet checked out
    $activeBookings = $facilityBookings->filter(function($b) {
        $log = $b->dayTourLog;
        return $log && !$log->checked_out_at && 
               (in_array($log->reservation_status, ['approved','paid']) || $log->checked_in_at);
    });

    $bookedQty = $activeBookings->sum(fn($b) => $b->facility_quantity ?? $b->quantity ?? 0);
    $available = max(0, $facility->quantity - $bookedQty);

    // Display status logic
    $displayStatus = $bookedQty > 0 ? 'occupied' : 'available';

    // Bookings list for UI
    $bookingsList = $facilityBookings->groupBy('day_tour_log_details_id')->map(function($group) {
        $log = $group->first()->dayTourLog;
        return [
            'booking_id'     => $log->id,
            'customer'       => $log->user ? $log->user->firstname . ' ' . $log->user->lastname : 'Unknown',
            'date'           => $log->date_tour,
            'quantity'       => $group->sum(fn($b) => $b->facility_quantity ?? $b->quantity ?? 0),
            'status'         => $log->checked_in_at && !$log->checked_out_at ? 'checked_in'
                               : ($log->checked_out_at ? 'checked_out' : $log->reservation_status),
            'checked_in_at'  => $log->checked_in_at,
            'checked_out_at' => $log->checked_out_at,
        ];
    })->values();

    // Units
    $units = [];
    for ($i = 1; $i <= $facility->quantity; $i++) {
        $units[] = [
            'name'   => "{$facility->name} #{$i}",
            'status' => $i <= $bookedQty ? 'Occupied' : 'Available',
        ];
    }

    return $facility->setAttribute('display_status', $displayStatus)
                    ->setAttribute('booked', $bookedQty)
                    ->setAttribute('available', $available)
                    ->setAttribute('occupancy_rate', $facility->quantity > 0 ? round(($bookedQty / $facility->quantity) * 100, 2) : 0)
                    ->setAttribute('bookings', $bookingsList)
                    ->setAttribute('units', $units);
});

    // --------------------------
    // OTHER FACILITIES / ROOMS
    // --------------------------
    $otherFacilities = Facility::where('type', 'room')->get()->map(function($facility) use ($date, $dateFrom, $dateTo) {
        $query = DB::table('facilities as fac')
            ->join('facility_summary as fac_sum', 'fac_sum.facility_id', '=', 'fac.id')
            ->join('facility_booking_details as fac_details', 'fac_details.facility_summary_id', '=', 'fac_sum.id')
            ->join('facility_booking_log as fac_log', 'fac_log.id', '=', 'fac_details.facility_booking_log_id')
            ->join('payments', 'payments.facility_log_id', '=', 'fac_log.id')
            ->join('users', 'users.id', '=', 'fac_log.user_id')
            ->where('fac.id', $facility->id)
            ->where('fac_log.status', '!=', 'pending_confirmation')
            ->where('payments.status', 'verified');

        if ($dateFrom && $dateTo) {
            $query->where(function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('fac_details.checkin_date', [$dateFrom, $dateTo])
                  ->orWhereBetween('fac_details.checkout_date', [$dateFrom, $dateTo]);
            });
        } else {
            $query->whereDate('fac_details.checkin_date', '<=', $date)
                  ->whereDate('fac_details.checkout_date', '>=', $date);
        }

        $unavailableDates = $query->select('fac_details.checkin_date', 'fac_details.checkout_date', 'users.firstname', 'users.lastname')->get();
        $bookedQty = $unavailableDates->count();
        $available = max(0, $facility->quantity - $bookedQty);

        $facility->setAttribute('display_status', $available > 0 ? 'available' : 'occupied');
        $facility->setAttribute('booked', $bookedQty);
        $facility->setAttribute('available', $available);
        $facility->setAttribute('occupancy_rate', $facility->quantity > 0 ? round(($bookedQty / $facility->quantity) * 100, 2) : 0);

        $units = [];
        for ($i = 1; $i <= $facility->quantity; $i++) {
            $units[] = [
                'name' => "{$facility->name} #{$i}",
                'status' => $i <= $bookedQty ? 'Occupied' : 'Available',
            ];
        }
        $facility->setAttribute('units', $units);

        $facility->setAttribute('bookings', $unavailableDates->map(function($d) {
            return [
                'customer' => $d->firstname . ' ' . $d->lastname,
                'date' => $d->checkin_date,
                'quantity' => 1,
                'status' => 'verified',
            ];
        }));

        return $facility;
    });

    // --------------------------
    // MERGE & SUMMARY
    // --------------------------
    $facilities = $cottagesVillas->merge($otherFacilities);

    $activeFacilities = $facilities->whereNotIn('display_status', ['maintenance','cleaning']);
    $totalCapacity = $activeFacilities->sum('available') + $activeFacilities->sum('booked');
    $totalBooked = $activeFacilities->sum('booked');

    $summary = [
        'total_facilities' => $facilities->count(),
        'total_available' => $activeFacilities->sum('available'),
        'total_booked' => $totalBooked,
        'total_capacity' => $totalCapacity,
        'overall_occupancy' => $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0,
        'maintenance_count' => $facilities->where('display_status','maintenance')->count(),
        'cleaning_count' => $facilities->where('display_status','cleaning')->count(),
    ];

    return view('admin.daytour.index', compact(
        'facilities','summary','date','dateFrom','dateTo','facilityType'
    
    ));
}

}