<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacilityBookingLog;
use App\Http\Controllers\Controller;

class BookingLogController extends Controller
{
    public function index()
    {
        $bookings = FacilityBookingLog::with('user')->latest()->get();
        
        return view('admin.try.index', compact('bookings'));
    }
}
