<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyBookingsController extends Controller
{
    public function index () 
    {
        return view('customer_pages.Bookings.index');
    }
}
