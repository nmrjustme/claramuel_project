<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;

class CustomerBookingPageController extends Controller
{

    public function Data($id) 
    {
        // Find the specific facility with its images
        $facility = $this->Facilities($id);
        $categories = $facility->category;
        $firstImage = $facility->images->first();
        $remainingImage = $facility->images->slice(1);
        
        if ($categories == 'Pool' || $categories == 'Park')
        {
            $guest_type = $this->GuestType($categories);
        } 
        else
        {
            $guest_type = null;
        }
        
        return view('customer_pages.checkin_page', [
            'facility' => $facility,
            'firstImage' => $firstImage,
            'remainingImage' => $remainingImage,
            'category' => $categories,
            'guest_type' => $guest_type
        ]);
    }
    
    private function GuestType ($location) 
    {
        return DB::table('guest_type')->where('location', $location)->select('type', 'rate')->get();
    }
    
    private function Facilities ($id)
    {
        return Facility::with('images')->findOrFail($id);
    }    
    
}