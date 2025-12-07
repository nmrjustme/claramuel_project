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
        \Log::info('Room Hold Request:', [
            'request_data' => $request->all(),
            'session_id' => session()->getId(),
            'session_exists' => session()->has('_token'),
            'all_session_data' => session()->all()
        ]);

        $request->validate([
            'room_id' => 'required|exists:facilities,id',
            'date_from' => 'required|date|after_or_equal:today',
            'date_to' => 'required|date|after:date_from'
        ]);

        try {
            DB::beginTransaction();

            $roomId = $request->room_id;
            $dateFrom = $request->date_from;
            $dateTo = $request->date_to;

            // Get current session ID
            $currentSessionId = session()->getId();

            // Initialize room holds in session if not exists
            if (!session()->has('room_holds')) {
                session(['room_holds' => []]);
            }

            // Get current room holds from session
            $roomHolds = session('room_holds', []);

            // Clean up expired holds from session
            $roomHolds = array_filter($roomHolds, function ($hold) {
                return strtotime($hold['expires_at']) > time();
            });

            \Log::info('Current session room holds:', $roomHolds);

            // Check if room is already on hold by someone else (in database)
            // UPDATED LOGIC: Allows checkout date to overlap with checkin date
            $existingHold = RoomHold::active()
                ->where('facility_id', $roomId)
                ->where('session_id', '!=', $currentSessionId)
                ->where(function ($query) use ($dateFrom, $dateTo) {
                    $query->where('date_from', '<', $dateTo)
                        ->where('date_to', '>', $dateFrom);
                })
                ->first();

            \Log::info('Existing Hold Check:', [
                'exists' => !empty($existingHold),
                'existing_hold' => $existingHold ? $existingHold->toArray() : null
            ]);

            // In RoomHoldController.php -> createHold()

            if ($existingHold) {
                \Log::warning('Room already on hold:', [
                    'room_id' => $roomId,
                    'existing_hold_session' => $existingHold->session_id,
                    'current_session' => $currentSessionId
                ]);

                // --- UPDATED LOGIC START ---
                $startDate = $existingHold->date_from;
                $lastNight = Carbon::parse($existingHold->date_to)->subDay();

                // Check if it's a single night (start date is same as last night)
                if ($startDate->format('Y-m-d') === $lastNight->format('Y-m-d')) {
                    // Output: "Dec 14"
                    $dateRange = $startDate->format('M d');
                } else {
                    // Output: "Dec 14 - Dec 15"
                    $dateRange = $startDate->format('M d') . ' - ' . $lastNight->format('M d');
                }
                // --- UPDATED LOGIC END ---

                return response()->json([
                    'success' => false,
                    'message' => "This room is temporarily on hold.",
                    'conflict_dates' => $dateRange
                ], 409);
            }

            // Remove any existing holds for this session and room (both database and session)
            $deleted = RoomHold::where('session_id', $currentSessionId)
                ->where('facility_id', $roomId)
                ->delete();

            // Also remove from session
            $roomHolds = array_filter($roomHolds, function ($hold) use ($roomId) {
                return $hold['facility_id'] != $roomId;
            });

            \Log::info('Deleted old holds:', ['count' => $deleted]);

            // Create new hold in database
            // Note: We still save the full date_to here for records
            $hold = RoomHold::create([
                'facility_id' => $roomId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'session_id' => $currentSessionId,
                'expires_at' => now()->addMinutes(10),
                'status' => 'pending'
            ]);

            // Store hold in session
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

            // Save session immediately
            session()->save();

            \Log::info('Created new hold:', $hold->toArray());
            \Log::info('Session room holds after creation:', session('room_holds'));

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

    public function releaseHold($holdId)
    {
        $hold = RoomHold::where('session_id', session()->getId())
            ->where('id', $holdId)
            ->first();

        if ($hold) {
            $hold->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function releaseAllHolds()
    {
        RoomHold::where('session_id', session()->getId())->delete();
        return response()->json(['success' => true]);
    }
}