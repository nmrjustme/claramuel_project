<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use App\Models\Breakfast;
use App\Models\FacilityBookingLog;
use Carbon\Carbon;

class BookingsController extends Controller
{
    protected $facilities;
    protected $breakfast;
    
    public function __construct()
    {
        $this->loadFacilities();
        $this->breakfast = Breakfast::select('price', 'status')->first();
    }
    
    protected function loadFacilities()
    {
        $this->facilities = Facility::with([
            'amenities' => function($query) {
                $query->select('amenities.id', 'amenities.name');
            },
            'images' => function($query) {
                $query->select('id', 'fac_id', 'image as path')
                    ->orderBy('id')
                    ->limit(1);
            },
            'discounts' // Using a relationship for better performance
        ])
        ->whereNotIn('category', ['pool', 'park', 'cottage'])
        ->orderBy('id', 'desc')
        ->get()
        ->each(function($facility) {
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
        return view('customer_pages.bookings', [
            'facilities' => $this->facilities,
            'unavailable_dates' => $this->getUnavailableDates(),
            'breakfast_price' => $this->breakfast,
        ]);
    }
    
    public function trylang()
    {
        return view('customer_pages.try.bookings', [
            'facilities' => $this->facilities,
            'unavailable_dates' => $this->getUnavailableDates(),
            'breakfast_price' => $this->breakfast,
        ]);
    }

    public function customerInfo()
    {
        return view('customer_pages.try.customer_info');
    }
    
    public function getAmenities(Facility $facility)
    {
        $amenities = $facility->amenities->map(function($amenity) {
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

    protected function getUnavailableDates()
    {
        $now = now()->format('Y-m-d'); // Get current date in app timezone
        
        $dates = DB::table('facilities as fac')
            ->join('facility_summary as fac_sum', 'fac_sum.facility_id', '=', 'fac.id')
            ->join('facility_booking_details as fac_details', 'fac_details.facility_summary_id', '=', 'fac_sum.id')
            ->join('facility_booking_log as fac_log', 'fac_log.id', '=', 'fac_details.facility_booking_log_id')
            ->join('payments', 'payments.facility_log_id', '=', 'fac_log.id')
                ->where('fac_log.status', 'Confirmed')
                ->whereIn('payments.status', ['verified', 'fully_paid'])
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
        
        logger('Unavailable Dates:', $dates->toArray());
        return $dates;
    }
    
    public function booking_completed(FacilityBookingLog $booking)
    {
        return view('customer_pages.booking_completed', ['booking' => $booking]);
    }
    
}