<?php

namespace App\Services;

use App\Models\RoomHold;

class RoomHoldService
{
    public function createHold($roomId, $dateFrom, $dateTo, $sessionId, $minutes = 10)
    {
        // Clean expired first
        $this->cleanupExpiredHolds();

        // Check if room is already held
        $existingHold = RoomHold::forRoomAndDates($roomId, $dateFrom, $dateTo)->first();

        if ($existingHold) {
            // If same session, extend expiry
            if ($existingHold->session_id === $sessionId) {
                $existingHold->update([
                    'expires_at' => now()->addMinutes($minutes)
                ]);
                return $existingHold;
            }

            return null; // Held by someone else
        }

        // Create new hold
        return RoomHold::create([
            'facility_id' => $roomId,
            'date_from'   => $dateFrom,
            'date_to'     => $dateTo,
            'session_id'  => $sessionId,
            'expires_at'  => now()->addMinutes($minutes),
            'status'      => 'pending'
        ]);
    }


    public function checkHold($roomId, $dateFrom, $dateTo, $sessionId = null)
    {
        $this->cleanupExpiredHolds();

        $query = RoomHold::forRoomAndDates($roomId, $dateFrom, $dateTo);

        if ($sessionId) {
            $query->where('session_id', '!=', $sessionId);
        }

        return $query->first();
    }


    public function releaseHold($roomId, $dateFrom, $dateTo, $sessionId)
    {
        $hold = RoomHold::where('facility_id', $roomId)
            ->where('date_from', $dateFrom)
            ->where('date_to', $dateTo)
            ->where('session_id', $sessionId)
            ->where('status', 'pending')
            ->first();

        if ($hold) {
            $hold->delete();  // delete immediately
            return true;
        }

        return false;
    }


    public function cleanupExpiredHolds()
    {
        // Delete holds whose expiration time passed
        RoomHold::where('expires_at', '<=', now())->delete();
    }


    public function getHeldRoomInfo($roomId, $dateFrom, $dateTo)
    {
        $hold = $this->checkHold($roomId, $dateFrom, $dateTo);

        if (!$hold) {
            return null;
        }

        return [
            'date_from'   => $hold->date_from->format('M d, Y'),
            'date_to'     => $hold->date_to->format('M d, Y'),
            'expires_at'  => $hold->expires_at->diffForHumans(['parts' => 2]),
            'is_own_hold' => $hold->session_id === session()->getId()
        ];
    }
}
