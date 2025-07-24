<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\FacilityImage;

class WelcomeController extends Controller
{
    protected $facilities;
    protected $roomFacilities;
    protected $privateVilla;
    
    public function __construct()
    {
        $this->facilities = Facility::with('images')->whereIn('category', ['Pool', 'Park'])->get();
        $this->roomFacilities = Facility::with('images')
            ->whereIn('category', ['Village', 'Room'])
            ->get();

        $this->privateVilla = Facility::with('images')->where('category', 'Village')->get();
    }
    
    public function index () {
        return view('welcome', [
                'facilities' => $this->facilities,
                'roomFacilities' => $this->roomFacilities,
                'privateVilla' => $this->privateVilla,
        ]);
    }
}
