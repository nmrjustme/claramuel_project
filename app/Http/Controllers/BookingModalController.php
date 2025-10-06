<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;

class BookingModalController extends Controller
{
    public function showFacilityData()
    {
        $facilities = Facility::all();
        return view('admin.modals.book', ['facilities' => $facilities]);
    }
}
