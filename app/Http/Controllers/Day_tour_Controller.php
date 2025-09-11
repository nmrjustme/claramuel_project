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
        $cottages = $facilities->where('category', 'Cottage')->map(function($facility) use ($date) {
            $booked = BookingGuestDetails::where('facility_id', $facility->id)
                ->whereHas('dayTourLog', function($query) use ($date) {
                    $query->where('date_tour', $date);
                })
                ->sum('facility_quantity');
            
            $facility->available = max(0, $facility->quantity - $booked);
            return $facility;
        });

        // FIX: Use consistent category naming - 'Private Villa' instead of 'Villa'
        $villas = $facilities->where('category','Villa')->map(function($facility) use ($date) {
            $booked = BookingGuestDetails::where('facility_id', $facility->id)
                ->whereHas('dayTourLog', function($query) use ($date) {
                    $query->where('date_tour', $date);
                })
                ->sum('facility_quantity');
            
            $facility->available = max(0, $facility->quantity - $booked);
            return $facility;
        });

        $guestTypes = GuestType::all();

        return view('admin.daytour.index', compact('cottages', 'villas', 'guestTypes', 'date'));
    }


public function create(Request $request)
    {
        $selectedDate = $request->date_tour ?? now()->toDateString();

        // Fetch guest types
        $guestTypes = GuestType::all();

        // FIX: Use consistent category naming - 'Private Villa' instead of 'Villa'
        $cottages = Facility::where('category', 'Cottage')->get()->map(function($facility) use ($selectedDate) {
            $availability = $this->checkFacilityAvailability($facility->id, $selectedDate);
            $facility->available = $availability['available'];
            $facility->already_booked = $availability['already_booked'];
            return $facility;
        });

        // FIX: Changed 'Villa' to 'Private Villa' to match database
        $villas = Facility::where('category', 'Private Villa')->get()->map(function($facility) use ($selectedDate) {
            $availability = $this->checkFacilityAvailability($facility->id, $selectedDate);
            $facility->available = $availability['available'];
            $facility->already_booked = $availability['already_booked'];
            return $facility;
        });

        return view('admin.daytour.create', compact('guestTypes', 'cottages', 'villas', 'selectedDate'));
    }


public function facilityAvailability(Request $request)
    {
        $date = $request->date_tour;
        
        $facilities = Facility::all()->map(function($facility) use ($date) {
            $bookedQty = BookingGuestDetails::where('facility_id', $facility->id)
                ->whereHas('dayTourLog', function($query) use ($date) {
                    $query->where('date_tour', $date);
                })->sum('facility_quantity');
            
            $available = max(0, $facility->quantity - $bookedQty);
            
            return [
                'id' => $facility->id,
                'name' => $facility->name,
                'available' => $available
            ];
        });
        
        return response()->json($facilities);
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
    $log = DayTourLogDetails::with([
        'user',
        'bookingGuestDetails.guestType',
        'bookingGuestDetails.facility',
    ])->findOrFail($id);

    $serviceType = $this->getServiceType($log);

    return view('admin.daytour.logs_show', compact('log', 'serviceType'));
}

public function edit($id)
{
    $log = DayTourLogDetails::with([
        'user',
        'bookingGuestDetails.guestType',
        'bookingGuestDetails.facility'
    ])->findOrFail($id);

    $guestTypes = GuestType::all();
    
    // Filter facilities to show ONLY Cottages and Villas (no grouping)
    $facilities = Facility::whereIn('category', ['Cottage', 'Private Villa','Villa'])->get();

    // Group booking details by type and calculate availability
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

    // Calculate availability for each facility
    $facilityAvailability = [];
    foreach ($facilities as $facility) {
        $alreadyBooked = BookingGuestDetails::where('facility_id', $facility->id)
            ->whereHas('dayTourLog', function($query) use ($log) {
                $query->where('date_tour', $log->date_tour)
                      ->where('id', '!=', $log->id);
            })
            ->sum('facility_quantity');
        
        $available = max(0, $facility->quantity - $alreadyBooked);
        $currentQty = $facilityDetails[$facility->id]['facility_quantity'] ?? 0;
        
        $facilityAvailability[$facility->id] = [
            'available' => $available,
            'already_booked' => $alreadyBooked,
            'max_allowed' => min($available + $currentQty, $facility->quantity)
        ];
    }

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
        'status' => 'required|in:pending,paid,approved,rejected',
    ]);

    DB::beginTransaction();

    try {
        // Get a default guest type for facility bookings
        $defaultGuestType = GuestType::where('type', 'Adult')
            ->where('location', 'Pool')
            ->first();

        if (!$defaultGuestType) {
            $defaultGuestType = GuestType::first();
        }

        // 1. RECALCULATE TOTAL PRICE BASED ON SUBMITTED QUANTITIES
        $recalculatedTotal = 0;

        // Calculate guest type totals
        foreach ($validated['guest_types'] as $guestTypeId => $data) {
            $quantity = $data['quantity'] ?? 0;
            if ($quantity > 0) {
                $guestType = GuestType::find($guestTypeId);
                if ($guestType) {
                    $recalculatedTotal += $quantity * ($guestType->rate ?? $guestType->price ?? 0);
                }
            }
        }

        // Calculate facility totals - ONLY FOR VALID CATEGORIES
        if (isset($validated['facilities'])) {
            $validFacilityIds = Facility::whereIn('category', ['Cottage', 'Villa', 'Private Villa'])
                ->pluck('id')
                ->toArray();
            
            foreach ($validated['facilities'] as $facilityId => $data) {
                // Only process facilities from allowed categories
                if (in_array($facilityId, $validFacilityIds)) {
                    $facilityQuantity = $data['facility_quantity'] ?? 0;
                    if ($facilityQuantity > 0) {
                        $facility = Facility::find($facilityId);
                        if ($facility) {
                            $recalculatedTotal += $facilityQuantity * ($facility->rate ?? $facility->price ?? 0);
                        }
                    }
                }
            }
        }

        // 2. Update basic booking info WITH RECALCULATED TOTAL
        $log->update([
            'date_tour' => $validated['date_tour'],
            'status' => $validated['status'],
            'total_price' => $recalculatedTotal, // Use recalculated total
        ]);

        // 3. Update or create guest type quantities
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
                        'facility_id' => null
                    ]
                );
            } else {
                // Remove if quantity is 0
                BookingGuestDetails::where([
                    'day_tour_log_details_id' => $log->id,
                    'guest_type_id' => $guestTypeId,
                    'facility_id' => null,
                ])->delete();
            }
        }

        // 4. Update or create facility quantities
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
                            'guest_type_id' => $defaultGuestType->id
                        ]
                    );
                } else {
                    // Remove if quantity is 0
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
            ->withInput(); // Keep form data on error
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


public function checkAvailability(Request $request)
{
    $date = $request->date;
    
    \Log::info('Availability check requested for date: ' . $date);
    
    if (!$date) {
        \Log::warning('No date provided for availability check');
        return response()->json(['error' => 'Date is required'], 400);
    }
    
    try {
        \Log::info('Checking availability for facilities on date: ' . $date);
        
        $facilities = Facility::all()->map(function($facility) use ($date) {
            $booked = BookingGuestDetails::where('facility_id', $facility->id)
                ->whereHas('dayTourLog', function($query) use ($date) {
                    $query->where('date_tour', $date); // Use the provided date
                })
                ->sum('facility_quantity');
            
            $available = max(0, $facility->quantity - $booked);
            
            \Log::debug("Facility {$facility->id}: {$available} available ({$booked} booked) on {$date}");
            
            return [
                'id' => $facility->id,
                'name' => $facility->name,
                'available' => $available,
                'total' => $facility->quantity,
                'booked' => $booked
            ];
        });
        
        \Log::info('Availability check completed successfully for date: ' . $date);
        return response()->json($facilities);
        
    } catch (\Exception $e) {
        \Log::error('Availability check error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
}

public function store(Request $request)
{
    // 1️⃣ Validate request
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'phone'      => 'required|string|max:20',
        'email'      => 'nullable|email|max:255',
        'date_tour' => 'required|date|after_or_equal:today',
        'status'     => 'required|string|in:pending,paid',
        'service_type' => 'required|string|in:pool,themed_park,both',
    ]);

    DB::beginTransaction();

    try {
        // ✅ ALWAYS create a new user (handle null email)
        $userData = [
            'firstname' => $request->first_name,
            'lastname'  => $request->last_name,
            'phone'     => $request->phone,
        ];

        $user = User::create($userData);

        // 2️⃣ Create Day Tour Log
        $dayTourLog = DayTourLogDetails::create([
            'user_id'     => $user->id,
            'date_tour'   => $request->date_tour,
            'approved_by' => auth()->id(),
            'status'      => $request->status,
            'total_price' => 0,
        ]);

        $totalPrice = 0;
        $serviceType = $request->service_type;

        // 3️⃣ Save Pool Guest Types
        if ($serviceType === 'pool' || $serviceType === 'both') {
            $poolGuests = [
                'pool_adult'    => $request->pool_adult ?? 0,
                'pool_kids'     => $request->pool_kids ?? 0,
                'pool_seniors'  => $request->pool_seniors ?? 0,
            ];

            foreach ($poolGuests as $key => $quantity) {
                if ($quantity > 0) {
                    $guestTypeName = str_replace('pool_', '', $key);
                    $guestType = GuestType::where('type', ucfirst($guestTypeName))
                        ->where('location', 'Pool')
                        ->first();

                    if ($guestType) {
                        BookingGuestDetails::create([
                            'guest_type_id' => $guestType->id,
                            'day_tour_log_details_id' => $dayTourLog->id,
                            'facility_id' => null,
                            'facility_booking_log_id' => null,
                            'quantity' => $quantity,
                            'facility_quantity' => null,
                        ]);

                        $totalPrice += $guestType->rate * $quantity;
                    }
                }
            }
        }

        // 4️⃣ Save Park Guest Types
        if ($serviceType === 'themed_park' || $serviceType === 'both') {
            $parkGuests = [
                'park_adult'    => $request->park_adult ?? 0,
                'park_kids'     => $request->park_kids ?? 0,
                'park_seniors'  => $request->park_seniors ?? 0,
            ];

            foreach ($parkGuests as $key => $quantity) {
                if ($quantity > 0) {
                    $guestTypeName = str_replace('park_', '', $key);
                    $guestType = GuestType::where('type', ucfirst($guestTypeName))
                        ->where('location', 'Park')
                        ->first();

                    if ($guestType) {
                        BookingGuestDetails::create([
                            'guest_type_id' => $guestType->id,
                            'day_tour_log_details_id' => $dayTourLog->id,
                            'facility_id' => null,
                            'facility_booking_log_id' => null,
                            'quantity' => $quantity,
                            'facility_quantity' => null,
                        ]);

                        $totalPrice += $guestType->rate * $quantity;
                    }
                }
            }
        }

        // 5️⃣ Accommodations handling
        if ($serviceType === 'pool' || $serviceType === 'both') {
            $accommodationInputs = $request->accommodations ?? [];
            
            // ✅ AVAILABILITY VALIDATION FIRST ✅
            foreach ($accommodationInputs as $facilityId => $qty) {
                if ($qty > 0) {
                    $facility = Facility::find($facilityId);
                    if ($facility) {
                        $bookedQty = BookingGuestDetails::where('facility_id', $facilityId)
                            ->whereHas('dayTourLog', function($query) use ($request) {
                                $query->where('date_tour', $request->date_tour);
                            })->sum('facility_quantity');
                        
                        $available = max(0, $facility->quantity - $bookedQty);
                        
                        if ($available < $qty) {
                            DB::rollback();
                            return redirect()->back()
                                ->with('error', "Sorry, only {$available} {$facility->name} available on {$request->date_tour}")
                                ->withInput();
                        }
                    }
                }
            }
            
            // ✅ THEN PROCEED WITH BOOKING ✅
            foreach ($accommodationInputs as $facilityId => $qty) {
                if ($qty > 0) {
                    $facility = Facility::find($facilityId);
                    if ($facility) {
                        // Check availability again (double-check)
                        $bookedQty = BookingGuestDetails::where('facility_id', $facilityId)
                            ->whereHas('dayTourLog', function($query) use ($request) {
                                $query->where('date_tour', $request->date_tour);
                            })->sum('facility_quantity');
                        
                        $available = max(0, $facility->quantity - $bookedQty);
                        $qtyToBook = min($qty, $available);
                        
                        if ($qtyToBook > 0) {
                            // Get default guest type for facility bookings
                            $defaultGuestType = GuestType::where('type', 'Adult')
                                ->where('location', 'Pool')
                                ->first();
                            
                            if (!$defaultGuestType) {
                                // Fallback to any guest type
                                $defaultGuestType = GuestType::first();
                            }
                            
                            BookingGuestDetails::create([
                                'day_tour_log_details_id' => $dayTourLog->id,
                                'facility_id' => $facilityId,
                                'facility_quantity' => $qtyToBook,
                                'quantity' => 0,
                                'guest_type_id' => $defaultGuestType->id,
                                'facility_booking_log_id' => null,
                            ]);
                            
                            $totalPrice += $facility->price * $qtyToBook;
                        }
                    }
                }
            }
        }

        // 6️⃣ Update total price
        $dayTourLog->update(['total_price' => $totalPrice]);

        DB::commit();
        return redirect()->back()->with('success', 'Day Tour registered successfully!');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}

private function checkFacilityAvailability($facilityId, $date)
{
    $facility = Facility::find($facilityId);
    if (!$facility) {
        return ['available' => 0, 'already_booked' => 0];
    }

    $alreadyBooked = BookingGuestDetails::where('facility_id', $facilityId)
        ->whereHas('dayTourLog', function($query) use ($date) {
            $query->where('date_tour', $date);
        })
        ->sum('facility_quantity');

    $available = max(0, $facility->quantity - $alreadyBooked);

    return ['available' => $available, 'already_booked' => $alreadyBooked];
}

public function logs(Request $request)
    {
        // Validate input - FIXED: Added service_type validation
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|string|in:pending,paid,approved,rejected',
            'service_type' => 'nullable|string|in:pool,themed_park,both', // FIXED: Added correct validation
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
                ->orWhere('status', 'like', "%{$searchTerm}%")
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
                      if ($serviceType === 'pool') {
                          $q->where('location', 'Pool');
                      } elseif ($serviceType === 'themed_park') {
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


    // Add these methods to your Day_tour_Controller class
/**
 * Display cottage and villa monitoring dashboard
 */
public function monitorFacilities(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'date' => 'nullable|date',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
        'facility_type' => 'nullable|string|in:cottage,villa,both',
        'status' => 'nullable|string|in:available,occupied,maintenance,cleaning',
    ]);

    // Default to today if no date specified
    $date = $validated['date'] ?? now()->toDateString();
    $dateFrom = $validated['date_from'] ?? null;
    $dateTo = $validated['date_to'] ?? null;
    
    // Get facilities based on type filter
    $facilityType = $validated['facility_type'] ?? 'both';
    
    $query = Facility::query();
    
    if ($facilityType !== 'both') {
        $category = $facilityType === 'cottage' ? 'Cottage' : 'Private Villa';
        $query->where('category', $category);
    } else {
        $query->whereIn('category', ['Cottage', 'Private Villa']);
    }
    
    // Status filter
    if (isset($validated['status'])) {
        $query->where('status', $validated['status']);
    }
    
    $facilities = $query->get();
    
    // Preload bookings data for all facilities in a single query
    $bookingQuery = BookingGuestDetails::with(['dayTourLog.user', 'dayTourLog'])
        ->whereIn('facility_id', $facilities->pluck('id'))
        ->whereHas('dayTourLog', function($query) use ($date, $dateFrom, $dateTo) {
            if ($dateFrom && $dateTo) {
                $query->whereBetween('date_tour', [$dateFrom, $dateTo]);
            } else {
                $query->where('date_tour', $date);
            }
            $query->whereIn('status', ['approved', 'paid']);
        });
    
    $allBookings = $bookingQuery->get()->groupBy('facility_id');
    
    // Calculate availability and bookings for each facility
    $facilities = $facilities->map(function($facility) use ($allBookings) {
        $facilityBookings = $allBookings->get($facility->id, collect());
        
        // For facilities in maintenance or cleaning, set availability to 0
        if (in_array($facility->status, ['maintenance', 'cleaning'])) {
            $facility->available = 0;
            $facility->booked = 0;
            $facility->occupancy_rate = 0;
            $facility->bookings = collect();
            $facility->display_status = $facility->status;
            return $facility;
        }
        
        $bookedQty = $facilityBookings->sum('facility_quantity');
        $available = max(0, $facility->quantity - $bookedQty);
        
        // Update facility display status based on bookings
        if ($bookedQty > 0 && $facility->status === 'available') {
            $facility->display_status = 'occupied';
        } elseif ($bookedQty === 0 && $facility->status === 'occupied') {
            $facility->display_status = 'available';
        } else {
            $facility->display_status = $facility->status;
        }
        
        // Get detailed booking information
        $bookings = $facilityBookings->groupBy('day_tour_log_details_id')
            ->map(function($bookingGroup) {
                $log = $bookingGroup->first()->dayTourLog;
                return [
                    'booking_id' => $log->id,
                    'customer' => $log->user ? $log->user->firstname . ' ' . $log->user->lastname : 'Unknown',
                    'date' => $log->date_tour,
                    'quantity' => $bookingGroup->sum('facility_quantity'),
                    'status' => $log->status,
                ];
            })->values();
        
        $facility->available = $available;
        $facility->booked = $bookedQty;
        $facility->occupancy_rate = $facility->quantity > 0 ? 
            round(($bookedQty / $facility->quantity) * 100, 2) : 0;
        $facility->bookings = $bookings;
        
        return $facility;
    });
    
    // Calculate summary statistics - EXCLUDE maintenance/cleaning facilities from availability calculations
    $activeFacilities = $facilities->whereNotIn('status', ['maintenance', 'cleaning']);
    $totalCapacity = $activeFacilities->sum('quantity');
    $totalBooked = $activeFacilities->sum('booked');
    
    $summary = [
        'total_facilities' => $facilities->count(),
        'total_available' => $activeFacilities->sum('available'),
        'total_booked' => $totalBooked,
        'total_capacity' => $totalCapacity,
        'overall_occupancy' => $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0,
        'maintenance_count' => $facilities->where('status', 'maintenance')->count(),
        'cleaning_count' => $facilities->where('status', 'cleaning')->count(),
    ];
    
    return view('admin.daytour.cottages_monitoring', compact(
        'facilities', 
        'summary', 
        'date', 
        'dateFrom', 
        'dateTo',
        'facilityType'
    ));
}

/**
 * Update facility status (available, occupied, maintenance, cleaning)
 */
public function updateFacilityStatus(Request $request, $id)
{
    $validated = $request->validate([
        'status' => 'required|in:available,occupied,maintenance,cleaning',
        'notes' => 'nullable|string|max:500',
    ]);
    
    $facility = Facility::findOrFail($id);
    $previousStatus = $facility->status;
    
    // Update status
    $facility->update([
        'status' => $validated['status'],
        'notes' => $validated['notes'] ?? null,
    ]);
    
    // Log the status change
    Log::info("Facility status updated", [
        'facility_id' => $id,
        'facility_name' => $facility->name,
        'old_status' => $previousStatus,
        'new_status' => $validated['status'],
        'updated_by' => auth()->id(),
        'notes' => $validated['notes'] ?? null,
    ]);
    
    return redirect()->back()->with('success', $facility->name . ' status updated to ' . $validated['status'] . ' successfully!');
}

/**
 * Check out guests from a facility
 */
public function checkoutFacility(Request $request, $facilityId)
{
    $validated = $request->validate([
        'date' => 'required|date',
    ]);
    
    $date = $validated['date'];
    $facility = Facility::findOrFail($facilityId);
    
    DB::beginTransaction();
    
    try {
        // Update the facility status to available
        $facility->update(['status' => 'available']);
        
        DB::commit();
        
        return redirect()->back()
            ->with('success', 'Guests checked out from ' . $facility->name . ' successfully! Facility status set to available.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Checkout error: ' . $e->getMessage(), [
            'facility_id' => $facilityId,
            'date' => $date,
            'user_id' => auth()->id()
        ]);
        
        return redirect()->back()
            ->with('error', 'Error during check-out: ' . $e->getMessage());
    }
}


}