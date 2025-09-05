<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Mail\EmailConfirmation;
use App\Models\User;
use App\Models\FacilityBookingLog;
use App\Models\Facility;
use App\Models\Breakfast;
use App\Models\FacilitySummary;
use App\Models\FacilityBookingDetails;
use App\Models\BookingGuestDetails;
use App\Events\BookingNew;
use App\Mail\AdminNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmailConfirmationController extends Controller
{
    public function sendOTP(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date',
            'facilities' => 'required|array',
            'facilities.*.facility_id' => 'required|exists:facilities,id',
            'facilities.*.price' => 'required|numeric|min:0',
            'facilities.*.nights' => 'required|integer|min:1',
            'facilities.*.total_price' => 'required|numeric|min:0',
            
            'guest_types' => 'required|array',
            'guest_types.*' => 'array', // Each facility's guest types
            'guest_types.*.*' => 'nullable|integer|min:0',
            
            'breakfast_included' => 'sometimes|boolean',
            'breakfast_price' => 'required_if:breakfast_included,true|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'amount_to_pay' => 'required|numeric|min:0',
        ]);
        
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get validated data
        $validatedData = $validator->validated();
        
        // Additional validation for guest counts and dates
        try {
            // Parse dates explicitly with timezone
            $timezone = config('app.timezone', 'Asia/Manila');
            $checkinDate = Carbon::parse($validatedData['checkin_date'], $timezone)
                ->setTimezone('Asia/Manila')
                ->startOfDay();
            $checkoutDate = Carbon::parse($validatedData['checkout_date'], $timezone)
                ->setTimezone('Asia/Manila')
                ->startOfDay();
            
            // Verify dates after parsing
            if ($checkinDate >= $checkoutDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-out date must be after check-in date'
                ], 422);
            }
            
            // Validate guest counts match facility pax limits
            foreach ($validatedData['facilities'] as $facilityData) {
                $facilityId = $facilityData['facility_id'];
                $facility = Facility::findOrFail($facilityId);
                
                $totalGuests = 0;
                if (isset($validatedData['guest_types'][$facilityId])) {
                    $totalGuests = array_sum($validatedData['guest_types'][$facilityId]);
                }
                
                if ($totalGuests > $facility->pax) {
                    return response()->json([
                        'success' => false,
                        'message' => "Facility {$facility->name} exceeds maximum guest limit of {$facility->pax}"
                    ], 422);
                }
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        }
        
        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $token = Str::random(60);
        
        // Store booking data with OTP in cache
        $bookingData = array_merge($validatedData, ['otp' => $otp]);
        Cache::put('booking_confirmation_' . $token, $bookingData, now()->addMinutes(30)); // 30 min expiry
        
        // Store email reference
        $email = $validatedData['email'];
        $emailTokens = Cache::get('email_tokens_' . $email, []);
        $emailTokens[] = $token;
        Cache::put('email_tokens_' . $email, $emailTokens, now()->addMinutes(30));
        
        try {
            // Send OTP email instead of confirmation link
            Mail::to($validatedData['email'])->send(new EmailConfirmation($otp, $validatedData));
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email!',
                'token' => $token // This token will be used to verify OTP
            ]);
            
        } catch (\Exception $e) {
            Log::error('Email sending failed:', [
                'error' => $e->getMessage(),
                'email' => $validatedData['email']
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }
    
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request'
            ], 422);
        }

        $email = $request->email;
        $token = $request->token;

        // Retrieve booking data
        $bookingData = Cache::get('booking_confirmation_' . $token);
        
        if (!$bookingData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 422);
        }

        // Generate new OTP
        $newOtp = rand(100000, 999999);
        $bookingData['otp'] = $newOtp;

        // Update cache with new OTP
        Cache::put('booking_confirmation_' . $token, $bookingData, now()->addMinutes(30));

        try {
            // Send new OTP
            Mail::to($email)->send(new EmailConfirmation($newOtp, $bookingData));
            
            return response()->json([
                'success' => true,
                'message' => 'New OTP sent successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Resend OTP failed:', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'otp' => 'required|digits:6'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP format'
            ], 422);
        }
        
        $token = $request->token;
        $otp = $request->otp;

        // Retrieve booking data from cache
        $bookingData = Cache::get('booking_confirmation_' . $token);
        
        if (!$bookingData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 422);
        }

        // Verify OTP
        if ($bookingData['otp'] != $otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 422);
        }

        // OTP verified successfully
        $reservationCode = $this->reservationCode();
        $bookingData['reservation_code'] = $reservationCode;
        $bookingData['verified_at'] = now();
        $bookingData['c'] = true;

        // Update cache without OTP
        unset($bookingData['otp']);
        Cache::put('booking_confirmation_' . $token, $bookingData, now()->addHours(24));
        
        // Clean up email tokens
        $email = $bookingData['email'];
        $emailTokens = Cache::get('email_tokens_' . $email, []);
        $updatedTokens = array_filter($emailTokens, function($t) use ($token) {
            return $t !== $token;
        });
        
        if (!empty($updatedTokens)) {
            Cache::put('email_tokens_' . $email, $updatedTokens, now()->addHours(24));
        } else {
            Cache::forget('email_tokens_' . $email);
        }

        Log::info('OTP verified successfully', [
            'reservation_code' => $reservationCode,
            'email' => $bookingData['email']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully!',
            'redirect_url' => route('booking.redirect', ['token' => $token]),
            'reservation_code' => $reservationCode,
            'token' => $token,
        ]);
    }
    
    public function confirmEmail($token)
    {
        try {
            // Retrieve booking data from cache
            $bookingData = Cache::get('booking_confirmation_' . $token);
            
            if (!$bookingData) {
                Log::warning('Invalid or expired confirmation token');
                return redirect()->route('invalid_link');
            }
            
            // Generate a reservation code now but don't save to database yet
            $reservationCode = $this->reservationCode();
            
            // Store the reservation code with the booking data
            $bookingData['reservation_code'] = $reservationCode;
            $bookingData['verified_at'] = now();

            // Update cache with reservation code
            Cache::put('booking_confirmation_' . $token, $bookingData, now()->addHours(24));
            
            // Clean up email tokens reference
            $email = $bookingData['email'];
            $emailTokens = Cache::get('email_tokens_' . $email, []);
            $updatedTokens = array_filter($emailTokens, function($t) use ($token) {
                return $t !== $token;
            });
            
            if (!empty($updatedTokens)) {
                Cache::put('email_tokens_' . $email, $updatedTokens, now()->addHours(24));
            } else {
                Cache::forget('email_tokens_' . $email);
            }

            Log::info('Email confirmed successfully, ready for payment', [
                'reservation_code' => $reservationCode,
                'email' => $bookingData['email']
            ]);
            
            // Redirect to payments page with the token // customer.booking.payments
            return redirect()->route('booking.redirect', ['token' => $token])->with([
                'success' => 'Email confirmed successfully! Please complete your payment.',
                'reservation_code' => $reservationCode
            ]);
            
        } catch (\Exception $e) {
            Log::error('Email confirmation failed:', [
                'error_message' => $e->getMessage(),
            ]);
            
            return redirect()->route('invalid_link');
        }
    }
    
    public function sendEmailAdmin(FacilityBookingLog $booking)
    {
        // Get all admin users who are active
        $admins = User::where('role', 'Admin')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();
        
        if ($admins->isEmpty()) {
            Log::warning("No active admin with email found");
            return;
        }
        
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(
                    new AdminNotification($booking)
                );
                
                Log::info("Booking email sent to admin", [
                    'booking_id' => $booking->id,
                    'email' => $admin->email
                ]);
            
            } catch (\Exception $e) {
                Log::error("Failed to send admin email", [
                    'booking_id' => $booking->id,
                    'email' => $admin->email,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function resendConfirmation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if we have tokens for this email
        $emailTokens = Cache::get('email_tokens_' . $request->email, []);
        
        if (empty($emailTokens)) {
            return response()->json([
                'success' => false,
                'message' => 'No pending bookings found for this email or the confirmation link has expired.'
            ], 404);
        }
        
        // Find the first valid token with booking data
        $bookingData = null;
        $token = null;
        
        foreach ($emailTokens as $emailToken) {
            $cachedData = Cache::get('booking_confirmation_' . $emailToken);
            if ($cachedData) {
                $bookingData = $cachedData;
                $token = $emailToken;
                break;
            }
        }
        
        if (!$bookingData) {
            return response()->json([
                'success' => false,
                'message' => 'No pending bookings found for this email or the confirmation link has expired.'
            ], 404);
        }
        
        try {
            // Send confirmation email
            Mail::to($request->email)->send(new EmailConfirmation($token, $bookingData));
            
            return response()->json([
                'success' => true,
                'message' => 'Confirmation email resent! Please check your inbox.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Resend confirmation email failed:', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend confirmation email. Please try again.'
            ], 500);
        }
    }

    public function reservationCode()
    {
        $now = Carbon::now();
        
        return strtoupper(
            'CM' // Prefix
            . $now->format('y')  // Year (e.g. 25 for 2025)
            . $now->format('m')  // Month (e.g. 08)
            . $now->format('d')  // Day (e.g. 19)
            . $now->format('H')  // Hour (24h format)
            . $now->format('i')  // Minute
            . 'AA'               // Predefined separator
            . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT)// Six random digits
        );
    }
}