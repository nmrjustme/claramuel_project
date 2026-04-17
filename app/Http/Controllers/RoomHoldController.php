<?php

namespace App\Http\Controllers;

use App\Models\RoomHold;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomHoldController extends Controller
{
    public function createHold(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:facilities,id',
            'date_from' => 'required|date|after_or_equal:today',
            'date_to' => 'required|date|after:date_from'
        ]);

        try {
            // 1. Start the Transaction
            DB::beginTransaction();

            $roomId = $request->room_id;
            $dateFrom = $request->date_from;
            $dateTo = $request->date_to;
            $currentSessionId = session()->getId();

            // =================================================================
            // 🛑 CRITICAL FIX: Lock the Facility Row
            // This forces Device B to PAUSE right here until Device A finishes.
            // =================================================================
            $facility = Facility::where('id', $roomId)->lockForUpdate()->first();

            // 2. Clean up expired holds from session (Housekeeping)
            if (!session()->has('room_holds')) {
                session(['room_holds' => []]);
            }
            $roomHolds = session('room_holds', []);
            $roomHolds = array_filter($roomHolds, function ($hold) {
                return strtotime($hold['expires_at']) > time();
            });

            // 3. Check availability (Now Thread-Safe because of the lock above)
            $existingHold = RoomHold::active()
                ->where('facility_id', $roomId)
                ->where('session_id', '!=', $currentSessionId)
                ->where(function ($query) use ($dateFrom, $dateTo) {
                    $query->where('date_from', '<', $dateTo)
                        ->where('date_to', '>', $dateFrom);
                })
                ->first();

            // 4. If Conflict Found
            if ($existingHold) {
                // Calculate display string for the error message
                $startDate = Carbon::parse($existingHold->date_from);
                $lastNight = Carbon::parse($existingHold->date_to)->subDay();

                if ($startDate->format('Y-m-d') === $lastNight->format('Y-m-d')) {
                    $dateRange = $startDate->format('M d');
                } else {
                    $dateRange = $startDate->format('M d') . ' - ' . $lastNight->format('M d');
                }

                // IMPORTANT: Rollback acts as the "unlock" mechanism so Device B can move on
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => "This room is temporarily on hold.",
                    'conflict_dates' => $dateRange
                ], 409);
            }

            // 5. Remove OLD holds for *this* user/room to prevent duplicates
            RoomHold::where('session_id', $currentSessionId)
                ->where('facility_id', $roomId)
                ->delete();

            // Remove from session array
            $roomHolds = array_filter($roomHolds, function ($hold) use ($roomId) {
                return $hold['facility_id'] != $roomId;
            });

            // 6. Create the New Hold
            $hold = RoomHold::create([
                'facility_id' => $roomId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'session_id' => $currentSessionId,
                'expires_at' => now()->addMinutes(15),
                'status' => 'pending'
            ]);

            // 7. Update Session
            $holdData = [
                'id' => $hold->id,
                'facility_id' => $roomId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'created_at' => now()->toDateTimeString(),
                'expires_at' => $hold->expires_at->toDateTimeString()
            ];

            $roomHolds[] = $holdData;
            session(['room_holds' => $roomHolds]);
            session()->save();

            // 8. Commit Transaction (This releases the Lock)
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room placed on hold',
                'hold_id' => $hold->id,
                'expires_at' => $hold->expires_at->format('Y-m-d H:i:s'),
                'session_holds_count' => count($roomHolds)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create room hold:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to place room on hold. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function verifyHolds(Request $request)
    {
        $request->validate([
            'hold_ids' => 'required|array',
            'hold_ids.*' => 'exists:room_holds,id'
        ]);

        $validHolds = [];
        $invalidHolds = [];

        foreach ($request->hold_ids as $holdId) {
            $hold = RoomHold::find($holdId);

            if (
                $hold &&
                $hold->session_id === session()->getId() &&
                $hold->status === 'pending' &&
                $hold->expires_at > now()
            ) {
                $validHolds[] = $holdId;
            } else {
                $invalidHolds[] = $holdId;
            }
        }

        return response()->json([
            'success' => count($invalidHolds) === 0,
            'valid' => count($invalidHolds) === 0,
            'valid_count' => count($validHolds),
            'invalid_count' => count($invalidHolds),
            'invalid_holds' => $invalidHolds,
            'message' => count($invalidHolds) === 0
                ? 'All holds are valid'
                : 'Some holds are no longer valid'
        ]);
    }

    public function getSessionHolds()
    {
        $roomHolds = session('room_holds', []);

        // Clean expired holds
        $validHolds = array_filter($roomHolds, function ($hold) {
            return strtotime($hold['expires_at']) > time();
        });

        // Update session with cleaned holds
        if (count($validHolds) !== count($roomHolds)) {
            session(['room_holds' => $validHolds]);
            session()->save();
        }

        return response()->json([
            'success' => true,
            'holds' => $validHolds,
            'count' => count($validHolds)
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:facilities,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after:date_from'
        ]);

        $unavailableRooms = [];
        $holds = [];

        foreach ($request->room_ids as $roomId) {
            // UPDATED LOGIC: Allows checkout date to overlap with checkin date
            $existingHold = RoomHold::active()
                ->where('facility_id', $roomId)
                ->where('session_id', '!=', session()->getId())
                ->where(function ($query) use ($request) {
                    $query->where('date_from', '<', $request->date_to)
                        ->where('date_to', '>', $request->date_from);
                })
                ->first();

            if ($existingHold) {
                $unavailableRooms[] = $roomId;
                $holds[$roomId] = [
                    'date_from' => $existingHold->date_from->format('Y-m-d'),
                    'date_to' => $existingHold->date_to->format('Y-m-d')
                ];
            }
        }

        return response()->json([
            'success' => true,
            'available' => empty($unavailableRooms),
            'unavailable_rooms' => $unavailableRooms,
            'holds' => $holds,
            'message' => empty($unavailableRooms)
                ? 'All rooms are available'
                : 'Some rooms are currently on hold'
        ]);
    }


    public function releaseAllHolds()
    {
        RoomHold::where('session_id', session()->getId())->delete();
        return response()->json(['success' => true]);
    }
}