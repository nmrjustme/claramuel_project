<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Redirect extends Controller
{

    public function redirectToBooking($redirectTo)
    {
        $allowedRoutes = ['bookings', 'Pools_Park']; // Whitelist allowed routes
        
        if (!in_array($redirectTo, $allowedRoutes)) {
            abort(404);
        }
    
        return view('booking-redirect', ['redirectTo' => $redirectTo]);
    }
}
