<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\User;
use App\Models\FacilityBookingLog;
use App\Models\FacilitySummary;
use App\Models\FacilityBookingDetails;
use App\Models\BookingGuestDetails;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    public function create()
    {
        // Fetch all active facilities to display in the Netflix-style scroll
        $facilities = Facility::with('images')->where('status', 'Active')->get();
        return view('admin.bookings.new_booking', compact('facilities'));
    }
    
    public function storeAdminBooking(Request $request)
    {
        // 1. Validation
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'room_id' => 'required|exists:facilities,id',
            'checkin' => 'required|date|after_or_equal:today',
            'checkout' => 'required|date|after:checkin',
        ]);

        DB::beginTransaction();
        try {
            // 2. Handle User (Find existing or create new)
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'firstname' => Str::upper($request->firstname),
                    'lastname' => Str::upper($request->lastname),
                    'phone' => $request->phone,
                    'role' => 'customer',
                    'password' => bcrypt(Str::random(12)), // Random password for new walk-ins
                ]
            );

            // 3. Create Booking Log
            $reservationCode = 'ADM-' . Str::upper(Str::random(8));
            $bookingLog = FacilityBookingLog::create([
                'user_id' => $user->id,
                'booking_date' => now(),
                'code' => $reservationCode,
                'status' => 'confirmed', // Admins bookings are usually auto-confirmed
            ]);

            // 4. Facility Details
            $facility = Facility::findOrFail($request->room_id);
            $checkin = Carbon::parse($request->checkin);
            $checkout = Carbon::parse($request->checkout);
            $nights = $checkin->diffInDays($checkout);
            $totalPrice = $facility->price * $nights;

            // 5. Create Summary & Details
            $summary = FacilitySummary::create([
                'facility_id' => $facility->id,
                'facility_price' => $facility->price,
                'facility_booking_log_id' => $bookingLog->id,
            ]);

            FacilityBookingDetails::create([
                'facility_summary_id' => $summary->id,
                'facility_booking_log_id' => $bookingLog->id,
                'checkin_date' => $request->checkin,
                'checkout_date' => $request->checkout,
                'total_price' => $totalPrice,
            ]);

            // 6. Record Payment
            Payments::create([
                'facility_log_id' => $bookingLog->id,
                'amount' => $totalPrice,
                'method' => $request->payment_method, // CASH, GCASH, etc.
                'reference_no' => $request->reference ?? 'WALK-IN',
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Booking recorded successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
