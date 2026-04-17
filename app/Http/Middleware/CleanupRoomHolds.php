<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RoomHold;
use Symfony\Component\HttpFoundation\Response;

class CleanupRoomHolds
{
    public function handle(Request $request, Closure $next): Response
    {
        // Clean up expired holds for this session
        RoomHold::where('session_id', session()->getId())
                ->where('expires_at', '<', now())
                ->delete();
        
        // Also clean up any expired holds (just in case scheduler didn't run)
        RoomHold::where('expires_at', '<', now())->delete();
        
        return $next($request);
    }
}