<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use App\Models\Breakfast;
use Carbon\Carbon;
use App\Models\RoomHold;
use Illuminate\Http\Request;

class BookingsController extends Controller
{
    protected $facilities;
    protected $breakfast;

    public function __construct()
    {
        $this->loadFacilities();
        $this->breakfast = Breakfast::select('price', 'status')->first();
    }

    public function checkRoomHolds(Request $request)
    {
        $request->validate([
            'facilities' => 'required|array',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date'
        ]);

        $unavailableRooms = [];
        $holdDetails = [];

        foreach ($request->facilities as $facilityId) {
            $existingHold = RoomHold::active()
                ->where('facility_id', $facilityId)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('date_from', [$request->checkin_date, $request->checkout_date])
                        ->orWhereBetween('date_to', [$request->checkin_date, $request->checkout_date])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('date_from', '<=', $request->checkin_date)
                                ->where('date_to', '>=', $request->checkout_date);
                        });
                })
                ->where('session_id', '!=', session()->getId())
                ->first();

            if ($existingHold) {
                $unavailableRooms[] = $facilityId;
                $holdDetails[$facilityId] = [
                    'date_from' => $existingHold->date_from->format('M d, Y'),
                    'date_to' => $existingHold->date_to->format('M d, Y'),
                    'expires_at' => $existingHold->expires_at->format('H:i:s')
                ];
            }
        }

        if (!empty($unavailableRooms)) {
            $firstHold = reset($holdDetails);
            $message = "This room is temporarily on hold from {$firstHold['date_from']} to {$firstHold['date_to']} for 10–15 minutes. Someone else is currently booking it. Please try again later or choose another date.";

            return response()->json([
                'success' => false,
                'message' => $message,
                'unavailable_rooms' => $unavailableRooms,
                'hold_details' => $holdDetails
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'All rooms are available'
        ]);
    }

    protected function loadFacilities()
    {
        $this->facilities = Facility::with([
            'amenities' => function ($query) {
                $query->select('amenities.id', 'amenities.name');
            },
            'images' => function ($query) {
                $query->select('id', 'fac_id', 'image as path')
                    ->orderBy('id')
                    ->limit(1);
            },
            'discounts' // Using a relationship for better performance
        ])
            ->where('type', 'room')
            ->orderBy('id', 'desc')
            ->get()
            ->each(function ($facility) {
                $this->processFacility($facility);
            });
    }

    protected function processFacility($facility)
    {
        // Set main image with fallback
        $facility->main_image = $facility->images->isNotEmpty()
            ? asset('imgs/facility_img/' . $facility->images->first()->path)
            : asset('images/default-facility.jpg'); // Better to use your own default image

        // Calculate discounted price
        $facility->discounted_price = $this->calculateDiscountedPrice($facility);
    }

    protected function calculateDiscountedPrice($facility)
    {
        if ($facility->discounts->isEmpty()) {
            return null;
        }

        $discount = $facility->discounts->first();
        $originalPrice = $facility->price;

        if ($discount->discount_type === 'percent') {
            $discountedPrice = $originalPrice * (1 - ($discount->discount_value / 100));
        } else {
            $discountedPrice = $originalPrice - $discount->discount_value;
        }

        return max(round($discountedPrice, 2), 0); // Ensure positive price
    }

    public function index()
    {
        // Note: We still pass data here for the initial load if you want server-side rendering,
        // but the JS will also fetch it via AJAX.
        // It is safer to pass an empty array or the data as json to a view variable if strictly needed,
        // but since your JS fetches it, we can just pass the facilities.
        return view('customer_pages.bookings', [
            'facilities' => $this->facilities,
            'unavailable_dates' => $this->getUnavailableDates()->getData(true), // Convert JsonResponse to array for view if needed
            'breakfast_price' => $this->breakfast,
        ]);
    }

    public function bookings_page()
    {
        return view('customer_pages.booking.index', [
            'facilities' => $this->facilities,
            'unavailable_dates' => $this->getUnavailableDates()->getData(true),
            'breakfast_price' => $this->breakfast,
        ]);
    }

    public function customerInfo(Request $request)
    {
        // 1. Retrieve booking data from session
        $bookingData = session()->get('booking_data');

        if (!$bookingData || !isset($bookingData['facilities'])) {
            return redirect()->route('dashboard.bookings')
                ->with('error', 'Session expired. Please select your rooms again.');
        }

        // 2. Iterate through selected rooms to verify Holds
        foreach ($bookingData['facilities'] as $facility) {
            $roomId = $facility['facility_id'];

            // CHECK 1: Does the CURRENT user have a valid, unexpired hold on this room?
            // We don't need to check dates again here, because if the hold exists 
            // for this session, it means the dates were already validated during creation.
            $myValidHold = RoomHold::where('facility_id', $roomId)
                ->where('session_id', session()->getId()) // Must belong to this browser session
                ->where('status', 'pending')
                ->where('expires_at', '>', now()) // Must not be expired
                ->exists();

            if (!$myValidHold) {

                // CHECK 2: If we don't have it, did someone else take it?
                // This is purely to give a more helpful error message.
                $checkin = Carbon::parse($bookingData['checkin_date']);
                $checkout = Carbon::parse($bookingData['checkout_date']);
                $effectiveCheckout = $checkout->copy()->subDay(); // Exclude checkout day

                $takenByOther = RoomHold::active()
                    ->where('facility_id', $roomId)
                    ->where('session_id', '!=', session()->getId()) // Not us
                    ->where(function ($query) use ($checkin, $effectiveCheckout) {
                        // Standard overlap logic
                        $query->where(function ($q) use ($checkin, $effectiveCheckout) {
                            $q->where('date_from', '<=', $checkin)
                                ->where('date_to', '>=', $effectiveCheckout); // Note: date_to in DB includes the last night
                        })->orWhere(function ($q) use ($checkin, $effectiveCheckout) {
                            $q->whereBetween('date_from', [$checkin, $effectiveCheckout])
                                ->orWhereBetween('date_to', [$checkin, $effectiveCheckout]);
                        });
                    })
                    ->first();

                if ($takenByOther) {
                    return redirect()->route('dashboard.bookings')->with(
                        'error',
                        "We're sorry, but the hold on {$facility['name']} expired and it was immediately secured by another guest. Please choose a different room or date."
                    );
                }

                // Fallback: No one else has it, but our time simply ran out.
                return redirect()->route('dashboard.bookings')->with(
                    'error',
                    "Your reservation time for {$facility['name']} has expired. Please select the room again."
                );
            }
        }

        // 3. All holds are valid, proceed to view
        return view('customer_pages.booking.customer_info', [
            'bookingData' => $bookingData
        ]);
    }

    public function storeBookingSession(Request $request)
    {
        try {
            $request->validate([
                'checkin_date' => 'required|date',
                'checkout_date' => 'required|date|after:checkin_date',
                'facilities' => 'required|array',
                'total_price' => 'required|numeric'
            ]);

            // Store in session
            session(['booking_data' => $request->all()]);

            // Also store each facility individually for easy access
            foreach ($request->facilities as $facility) {
                session(["{$facility['facility_id']}_hold" => true]);
            }

            // Save session immediately
            session()->save();

            return response()->json([
                'success' => true,
                'message' => 'Booking data saved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to store booking session:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save booking data'
            ], 500);
        }
    }

    public function getAmenities(Facility $facility)
    {
        $amenities = $facility->amenities->map(function ($amenity) {
            return [
                'name' => $amenity->name,
                'icon' => $amenity->icon ?? $this->getAmenityIcon($amenity->name)
            ];
        });

        return response()->json([
            'success' => true,
            'amenities' => $amenities
        ]);
    }

    protected function getAmenityIcon($amenityName)
    {
        $iconMap = [
            'wifi' => 'fas fa-wifi',
            'tv' => 'fas fa-tv',
            'air conditioning' => 'fas fa-snowflake',
            'kitchen' => 'fas fa-utensils',
            'parking' => 'fas fa-parking',
            'pool' => 'fas fa-swimming-pool',
            'breakfast' => 'fas fa-coffee',
            'gym' => 'fas fa-dumbbell',
        ];

        $lowerName = strtolower($amenityName);
        return $iconMap[$lowerName] ?? 'fas fa-check-circle';
    }

    /**
     * Fetch unavailable dates for all rooms.
     * Returns JSON response for AJAX consumption.
     */
    public function getUnavailableDates()
    {
        $now = now()->format('Y-m-d'); // Get current date in app timezone

        $dates = DB::table('facilities as fac')
            ->join('facility_summary as fac_sum', 'fac_sum.facility_id', '=', 'fac.id')
            ->join('facility_booking_details as fac_details', 'fac_details.facility_summary_id', '=', 'fac_sum.id')
            ->join('facility_booking_log as fac_log', 'fac_log.id', '=', 'fac_details.facility_booking_log_id')
            ->join('payments', 'payments.facility_log_id', '=', 'fac_log.id')
            
            // Only confirmed bookings that are paid (or remove payment check if needed)
            ->where('fac_log.status', '!=', 'cancelled')
            ->where('fac_log.status', '!=', 'checked_out')
            ->whereNotNull('payments.amount')
            
            // Filter out old bookings
            ->where('fac_details.checkout_date', '>=', $now)

            ->select([
                'fac.id as facility_id',
                'fac_details.checkin_date',
                'fac_details.checkout_date'
            ])
            ->get()
            
            // Group by Facility ID
            ->groupBy('facility_id')
            
            // Format dates
            ->map(function ($dates) {
                return $dates->map(function ($date) {
                    return [
                        'checkin_date' => Carbon::parse($date->checkin_date)
                            ->setTimezone(config('app.timezone'))
                            ->format('Y-m-d'),
                        'checkout_date' => Carbon::parse($date->checkout_date)
                            ->setTimezone(config('app.timezone'))
                            ->format('Y-m-d')
                    ];
                });
            });

        // Return as JSON response for proper header handling
        return response()->json($dates);
    }

    public function pendingVerification()
    {
        return view('customer_pages.booking.waiting_verification');
    }
}