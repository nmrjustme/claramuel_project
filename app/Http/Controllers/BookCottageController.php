<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\GuestType;
use App\Models\Breakfast;
use App\Models\FacilityBookingDetails;
use Illuminate\Support\Facades\DB;

class BookCottageController extends Controller
{
    public function index()
    {
        $facilities = Facility::with(['images', 'discounts' => function($query) {
            $query->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
        }])->where('status', 'Active')
           ->where('category', 'Cottage')->get();

        // Get guest types (adult, kids, senior)
        $guestTypes = GuestType::all();

        // Get breakfast pricing
        $breakfastPrice = Breakfast::first();

        $unavailable_dates = $this->unavailable_date();
        
        return view('customer_pages.book_cottage', [
            'facilities' => $facilities,
            'guestTypes' => $guestTypes,
            'breakfast_price' => $breakfastPrice,
            'unavailable_dates' => $unavailable_dates
        ]);
    }
    
    public function unavailable_date()
    {
        $results = DB::table('facilities as fac')
            ->join('facility_summary as fac_sum', 'fac_sum.facility_id', '=', 'fac.id')
            ->join('facility_booking_details as fac_details', 'fac_details.facility_summary_id', '=', 'fac_sum.id')
            ->join('facility_booking_log as fac_log', 'fac_log.id', '=', 'fac_details.facility_booking_log_id')
            ->join('payments', 'payments.facility_log_id', '=', 'fac_log.id')
            ->where([
                ['fac_log.status', 'Confirmed'],
                ['payments.status', 'Paid']
            ])
            ->select('fac.id as facility_id', 'fac_details.checkin_date', 'fac_details.checkout_date')
            ->get();
    
        $grouped = [];
    
        foreach ($results as $row) {
            $grouped[$row->facility_id][] = [
                'checkin_date' => $row->checkin_date,
                'checkout_date' => $row->checkout_date,
            ];
        }
    
        return $grouped;
    }
}
