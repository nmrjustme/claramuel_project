<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;

class PoolParkbookingController extends Controller
{
    protected $facilities;
    protected $privateVilla;
    
    public function __construct()
    {
        // Load facilities with their first image
        $this->facilities = Facility::with(['images' => function($query) {
                $query->select('id', 'fac_id', 'image as path')
                      ->orderBy('id', 'asc')
                      ->limit(1);
            }])
            ->whereIn('category', ['Pool', 'Park'])
            ->get()
            ->map(function($facility) {
                if ($facility->images->isNotEmpty()) {
                    $facility->main_image = asset('imgs/facility_img/' . $facility->images->first()->path);
                } else {
                    $facility->main_image = 'https://via.placeholder.com/500x300?text=No+Image';
                }
                return $facility;
            });
    }
    
    public function index ()
    {
        $PoolGuest = $this->GuestType('Pool');
        $ParkGuest = $this->GuestType('Park');
        
        return view('customer_pages.bookings_pool_park', [
            'facilities' => $this->facilities,
            'PoolGuest' => $PoolGuest,
            'ParkGuest' => $ParkGuest,
        ]);
    }
    
    private function GuestType ($location) 
    {
        return DB::table('guest_type')->where('location', $location)->select('type', 'rate')->get();
    }

}

