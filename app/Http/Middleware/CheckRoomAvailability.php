<?php

namespace App\Http\Middleware;

use App\Models\RoomHold;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoomAvailability
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get booking data from session
        $bookingData = session('bookingData');
        
        if ($bookingData && isset($bookingData['facilities'])) {
            foreach ($bookingData['facilities'] as $facility) {
                $hasActiveHold = RoomHold::active()
                    ->where('room_id', $facility['facility_id'])
                    ->where('date_from', $bookingData['checkin_date'])
                    ->where('date_to', $bookingData['checkout_date'])
                    ->where('session_id', '!=', session()->getId())
                    ->exists();
                
                if ($hasActiveHold) {
                    return redirect()->route('bookings.index')
                        ->with('error', 'One or more rooms are no longer available. Please select different rooms or dates.');
                }
            }
        }
        
        return $next($request);
    }
}