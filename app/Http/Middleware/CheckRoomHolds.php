<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\RoomHoldService;

class CheckRoomHolds
{
    protected $roomHoldService;
    
    public function __construct(RoomHoldService $roomHoldService)
    {
        $this->roomHoldService = $roomHoldService;
    }
    
    public function handle(Request $request, Closure $next): Response
    {
        // Only check on customer info page
        if ($request->route()->named('bookings.customer-info')) {
            $bookingData = session()->get('bookingData');
            
            if (!$bookingData) {
                return redirect()->route('bookings')->with('error', 'No booking data found.');
            }
            
            foreach ($bookingData['facilities'] as $facility) {
                $facilityId = $facility['facility_id'];
                $checkin = $bookingData['checkin_date'];
                $checkout = $bookingData['checkout_date'];
                
                // Check if any room is on hold by another session
                $holdInfo = $this->roomHoldService->checkHold(
                    $facilityId, 
                    $checkin, 
                    $checkout,
                    session()->getId()
                );
                
                if ($holdInfo) {
                    $roomName = $facility['name'];
                    $dateFrom = $holdInfo->date_from->format('M d, Y');
                    $dateTo = $holdInfo->date_to->format('M d, Y');
                    
                    return redirect()->route('bookings')->with('hold_error', [
                        'message' => "This room ($roomName) is temporarily on hold from $dateFrom to $dateTo for 5–10 minutes. Someone else is currently booking it. Please try again later or choose another date."
                    ]);
                }
            }
        }
        
        return $next($request);
    }
}