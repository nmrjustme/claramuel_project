<?php

namespace App\Http\Controllers;


use App\Models\Facility;
use App\Models\BookingGuestDetails;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{

    public function checkAvailability(Request $request)
    {
        $date = $request->date;
        
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
        
        return response()->json(['facilities' => $facilities]);
    }
}

