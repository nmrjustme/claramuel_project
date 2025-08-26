<?php

namespace App\Http\Controllers;

use App\Models\FacilityBookingLog;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Mail\PaymentVerifiedMail;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode; 
use Illuminate\Support\Facades\Mail;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Breakfast;
use App\Models\Facility;
use App\Models\GuestType;
use App\Models\FacilitySummary;
use App\Models\FacilityBookingDetails;
use App\Models\BookingGuestDetails;
use App\Events\BookingNew;

use App\Mail\CustomerPay;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PaymentsController extends Controller
{
    
    public function payments($token)
    {
        // Retrieve booking data from cache
        $bookingData = Cache::get('booking_confirmation_' . $token);
        
        if (!$bookingData) {
            return redirect()->route('bookings.customer-info')->with('error', 'Invalid or expired booking session.');
        }
        
        // Extract data from cached booking
        $user_firstname = $bookingData['firstname'] ?? 'Guest';
        $user_lastname = $bookingData['lastname'] ?? 'Guest';
        $user_phone = $bookingData['phone'] ?? 'No Phone number';
        $user_email = $bookingData['email'] ?? 'No Email';
        $total_price = $bookingData['total_price'] ?? 0;
        $half_of_total_price = ($total_price * 0.5);
        $reservation_code = $bookingData['reservation_code'] ?? 'No reservation code';
        
        // Parse dates
        $timezone = config('app.timezone', 'Asia/Manila');
        $checkin = Carbon::parse($bookingData['checkin_date'], $timezone);
        $checkout = Carbon::parse($bookingData['checkout_date'], $timezone);
        $nights = $checkin->diffInDays($checkout);
        
        // Get breakfast price if included
        $breakfastPrice = null;
        if ($bookingData['breakfast_included']) {
            $breakfastPrice = Breakfast::where('status', 'Active')->first();
        }
        
        // Prepare facilities data
        $facilities = [];
        foreach ($bookingData['facilities'] as $facilityData) {
            $facility = Facility::find($facilityData['facility_id']);
            
            $guestDetails = [];
            if (isset($bookingData['guest_types'][$facilityData['facility_id']])) {
                foreach ($bookingData['guest_types'][$facilityData['facility_id']] as $guestTypeId => $quantity) {
                    if ($quantity > 0) {
                        $guestType = GuestType::find($guestTypeId);
                        $guestDetails[] = [
                            'type' => $guestType->type ?? 'Unknown',
                            'quantity' => $quantity
                        ];
                    }
                }
            }
            
            $facilities[] = [
                'name' => $facility->name ?? 'Unknown',
                'price' => $facilityData['price'] ?? 0,
                'guest_details' => $guestDetails
            ];
        }
        
        return view('customer_pages.booking.payments', [
            'token' => $token,
            'user_firstname' => $user_firstname,
            'user_lastname' => $user_lastname,
            'user_phone' => $user_phone,
            'user_email' => $user_email,
            'half_of_total_price' => $half_of_total_price,
            'total_price' => $total_price,
            'reservation_code' => $reservation_code,
            'facilities' => $facilities,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'nights' => $nights,
            'breakfastPrice' => $breakfastPrice
        ]);
    }
    
    public function submitBooking(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'gcash_number' => 'required|string|regex:/^09\d{9}$/',
            'reference_no' => 'required|string|max:50|unique:payments,reference_no',
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Retrieve booking data from cache
        $bookingData = Cache::get('booking_confirmation_' . $token);
        
        if (!$bookingData) {
            return response()->json([
                'success' => false,
                'message' => 'Booking session expired. Please start over.'
            ], 410);
        }

        DB::beginTransaction();
        try {
            // Store everything in database now
            $booking = $this->storeBookingInDatabase($bookingData);
            
            // Store payment receipt
            $file = $request->file('receipt');
            $fileName = 'receipt_'.time().'_'.$booking->id.'.'.$file->getClientOriginalExtension();
            $path = 'imgs/payment_receipts/';
            
            // Create directory if it doesn't exist
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0755, true);
            }

            // Move file to public directory
            $file->move(public_path($path), $fileName);
            $receiptPath = $path.$fileName;
            
            $total_price = $booking->details->sum('total_price');

            // Create payment record
            Payments::create([
                'facility_log_id' => $booking->id,
                'status' => 'under_verification',
                'amount' => (0.5 * $total_price),
                'gcash_number' => $request->gcash_number,
                'reference_no' => $request->reference_no,
                'receipt_path' => $receiptPath,
                'payment_date' => now(),
                'paid_at' => now(),
            ]);

            // Remove from cache after successful database storage
            Cache::forget('booking_confirmation_' . $token);
            
            // Clean up email reference
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

            DB::commit();

            return response()->json([
                'success' => true
            ]);
        
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment processing error: '.$e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment.'
            ], 500);
        }
    }
    
    private function storeBookingInDatabase($bookingData)
    {
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Parse dates explicitly with timezone
            $timezone = config('app.timezone', 'Asia/Manila');
            $checkinDate = Carbon::parse($bookingData['checkin_date'], $timezone)
                ->setTimezone('Asia/Manila')
                ->startOfDay();
            $checkoutDate = Carbon::parse($bookingData['checkout_date'], $timezone)
                ->setTimezone('Asia/Manila')
                ->startOfDay();
            
            // Verify dates after parsing
            if ($checkinDate >= $checkoutDate) {
                throw new \Exception('Check-out date must be after check-in date');
            }
            
            // Validate guest counts match facility pax limits
            foreach ($bookingData['facilities'] as $facilityData) {
                $facilityId = $facilityData['facility_id'];
                $facility = Facility::findOrFail($facilityId);
                
                $totalGuests = 0;
                if (isset($bookingData['guest_types'][$facilityId])) {
                    $totalGuests = array_sum($bookingData['guest_types'][$facilityId]);
                }
                
                if ($totalGuests > $facility->pax) {
                    throw new \Exception("Facility {$facility->name} exceeds maximum guest limit of {$facility->pax}");
                }
            }
            
            // Create user (always insert new, no update)
            $user = User::create([
                'email' => $bookingData['email'],
                'firstname' => $bookingData['firstname'],
                'lastname' => $bookingData['lastname'],
                'phone' => $bookingData['phone'],
                'role' => 'customer',
            ]);

            // Create booking log with confirmation token
            $bookingLog = FacilityBookingLog::create([
                'user_id' => $user->id,
                'booking_date' => now(),
                'code' => $bookingData['reservation_code']
            ]);

            \Log::info('Booking confirmed via email', [
                'booking_id' => $bookingLog->id,
                'user_id' => $user->id,
                'email' => $bookingData['email']
            ]);
            
            // Get active breakfast price if included
            $breakfastId = null;
            $breakfastPricePerFacilityPerDay = 0;
            
            if ($bookingData['breakfast_included']) {
                $breakfast = Breakfast::where('status', 'Active')->first();
                if ($breakfast) {
                    $breakfastId = $breakfast->id;
                    $breakfastPricePerFacilityPerDay = $breakfast->price;
                }
            }
            
            // Process each facility
            foreach ($bookingData['facilities'] as $facilityData) {
                $facility = Facility::findOrFail($facilityData['facility_id']);
                
                // Create facility summary
                $facilitySummary = FacilitySummary::create([
                    'facility_id' => $facility->id,
                    'breakfast_id' => $breakfastId,
                    'facility_booking_log_id' => $bookingLog->id,
                ]);
                
                // Calculate breakfast cost for this facility (per facility per night)
                $breakfastCost = 0;
                if ($bookingData['breakfast_included']) {
                    $breakfastCost = $breakfastPricePerFacilityPerDay * $facilityData['nights'];
                }
                
                // Calculate total price for this facility (room + breakfast)
                $facilityTotalPrice = $facilityData['total_price'] + $breakfastCost;
                
                // Create booking details
                FacilityBookingDetails::create([
                    'facility_summary_id' => $facilitySummary->id,
                    'facility_booking_log_id' => $bookingLog->id,
                    'checkin_date' => $checkinDate->format('Y-m-d'),
                    'checkout_date' => $checkoutDate->format('Y-m-d'),
                    'total_price' => $facilityTotalPrice,
                    'breakfast_cost' => $breakfastCost,
                ]);
                
                // Process guest types for this facility
                if (isset($bookingData['guest_types'][$facility->id])) {
                    foreach ($bookingData['guest_types'][$facility->id] as $guestTypeId => $quantity) {
                        if ($quantity > 0) {
                            BookingGuestDetails::create([
                                'guest_type_id' => $guestTypeId,
                                'facility_booking_log_id' => $bookingLog->id,
                                'facility_id' => $facility->id,
                                'quantity' => $quantity
                            ]);
                        }
                    }
                }
            }
            
            // Commit transaction
            DB::commit();
            
            $bookingLog->load(['user']);
            
            event(new BookingNew($bookingLog)); // Event listener for new booking list
            
            // Sending active admin email
            // if (User::where('role', 'Admin')->where('is_active', true)->exists()) {
            //     $this->sendEmailAdmin($bookingLog); 
            // }
            
            return $bookingLog;
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Email confirmation booking failed:', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'booking_data' => $bookingData
            ]);
            
            throw $e; // Re-throw to handle in the controller
        }
    }
        
    public function sendEmailAdmin(Payments $payment)
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
                    new CustomerPay($payment)
                );
                
                Log::info("Booking email sent to admin", [
                    'payment_id' => $payment->id,
                    'email' => $admin->email
                ]);
            
            } catch (\Exception $e) {
                Log::error("Failed to send admin email", [
                    'payment_id' => $payment->id,
                    'email' => $admin->email,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    // public function show(Payment $payment)
    // {
    //     // Authorization check - ensure user can view this payment
    //     $this->authorize('view', $payment);
        
    //     return view('payments.show', compact('payment'));
    // }
    
    /**
     * Verify payment and send receipt with QR code
     */
    // public function verifyPaymentWithReceipt(Request $request, $id, ?string $customMessage = null): void
    // {
    //     $request->validate([
    //         'amount_paid' => 'required|numeric|min:0',
    //     ]);
        
    //     $payment = Payments::with(
    //         'bookingLog.details',    
    //         'bookingLog.summaries.facility',
    //         'bookingLog.summaries.breakfast',
    //         'bookingLog.summaries.bookingDetails',
    //         'bookingLog.guestDetails.guestType'
    //     )->findOrFail($id);
        
    //     $checkout_date = $payment->bookingLog->details->first()->checkout_date;
    //     $expire_date = Carbon::parse($checkout_date)->endOfDay()->toDateTimeString();
    
    //     // ✅ Generate a unique token
    //     do {
    //         $verificationToken = Str::random(64);
    //     } while (Payments::where('verification_token', $verificationToken)->exists());
    
    //     // ✅ Encrypt QR code payload
    //     $payload = [
    //         'id' => $payment->id,
    //         'expires_at' => $expire_date
    //     ];

    
    //     // ✅ Build QR Code with encrypted string
    //     $result = Builder::create()
    //         ->writer(new PngWriter())
    //         ->data(json_encode($payload))
    //         ->size(300)
    //         ->margin(10)
    //         ->build();
    
    //     // ✅ Save QR Code Image
    //     $directory = public_path('imgs/qr_code/');
    //     $fileName = 'qr_payment_'.$payment->id.'_'.time().'.png';
    //     $filePath = $directory . $fileName;
    
    //     if (!File::exists($directory)) {
    //         File::makeDirectory($directory, 0755, true);
    //     }
    
    //     $result->saveToFile($filePath);
    
    //     // ✅ Update payment record
    //     $payment->update([
    //         'status' => 'verified',
    //         'verified_at' => now(),
    //         'verified_by' => auth()->id(),
    //         'amount_paid' => $request->amount_paid,
    //         'verification_token' => $verificationToken,
    //         'qr_code_path' => 'imgs/qr_code/' . $fileName
    //     ]);
    
    //     // ✅ Send Email with QR Code (Optional)
    //     $qrCodeUrl = asset('imgs/qr_code/' . $fileName);
    //     $this->sendVerificationEmail($payment, $qrCodeUrl);
    
    //     return response()->json([
    //         'success' => true,
    //         'payment' => $payment,
    //         'message' => 'Payment verified and receipt sent with QR code',
    //         'qr_code_url' => $qrCodeUrl,
    //         'guest_details' => $payment->bookingLog->guestDetails
    //     ]);
    // }
    
    // /**
    //  * Send verification email with QR code
    //  */
    // private function sendVerificationEmail($payment, $qrCodeUrl)
    // {
    //     try {
    //         // Validate the payment has all required data
    //         if (!$payment->bookingLog || !$payment->bookingLog->user) {
    //             throw new \Exception('Booking or user information missing');
    //         }
    
    //         $customer_email = $payment->bookingLog->user->email;
            
    //         // Validate email format
    //         if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    //             throw new \Exception('Invalid email format: ' . $customer_email);
    //         }
    
    //         $customMessage = "Thank you for your payment. Please present this QR code upon arrival at the resort to verify your reservation.";
    
    //         // Validate payment has required fields
    //         $requiredFields = ['reference_no', 'amount', 'verified_at'];
    //         foreach ($requiredFields as $field) {
    //             if (empty($payment->{$field})) {
    //                 throw new \Exception("Payment missing required field: $field");
    //             }
    //         }
    
    //         // Send email with error handling
    //         Mail::to($customer_email)->send(new PaymentVerifiedMail(
    //             $payment,
    //             $qrCodeUrl,
    //             $customMessage
    //         ));
    
    //         // Log successful email sending
    //         \Log::info("Verification email sent to {$customer_email} for payment {$payment->reference_no}");
    
    //     } catch (\Exception $e) {
    //         \Log::error("Failed to send verification email for payment {$payment->id}: " . $e->getMessage());
    //         throw $e; // Re-throw if you want calling method to handle it
    //     }
    // }
    
    public function updateRemainingStatus(Request $request, $id)
    {
        $request->validate([
            'remaining_status' => 'required|in:pending,fully_paid',
            'amount_paid' => 'required|numeric|min:0'
        ]);
        
        $payment = Payments::findOrFail($id);
        
        // Check if the status is actually changing
        if ($payment->remaining_balance_status === $request->remaining_status) {
            return response()->json([
                'success' => false,
                'message' => 'Status is already set to ' . $request->remaining_status
            ], 400);
        }
        
        try {
            DB::beginTransaction();
            
            // Update payment remaining balance status
            $payment->update([
                'checkin_paid' => $request->amount_paid,
                'remaining_balance_status' => $request->remaining_status,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Status and amount updated successfully',
                'payment' => $payment->fresh()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
        
    /**
     * Verify QR code
     */
    // public function verifyQrCode(Request $request)
    // {
    //     $request->validate([
    //         'qr_data' => 'required|string'
    //     ]);
        
    //     try {
    //         $data = json_decode($request->qr_data, true);
            
    //         $payment = Payments::where('id', $data['payment_id'])
    //             ->where('verification_token', $data['token'])
    //             ->where('status', 'paid')
    //             ->first();
                
    //         if (!$payment) {
    //             return response()->json(['valid' => false, 'message' => 'Invalid QR code']);
    //         }
            
    //         if (now()->gt($data['expires_at'])) {
    //             return response()->json(['valid' => false, 'message' => 'QR code expired']);
    //         }
            
    //         // Mark as claimed if needed
    //         $payment->update(['claimed_at' => now()]);
            
    //         return response()->json([
    //             'valid' => true,
    //             'payment' => $payment,
    //             'customer' => $payment->customer
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json(['valid' => false, 'message' => 'Invalid QR code format']);
    //     }
    // }
    
    // public function verifyScannedPayment(Request $request, $paymentId)
    // {
    //     try {
    //         $payment = Payments::findOrFail($paymentId);
            
    //         // Validate the request
    //         $request->validate([
    //             'token' => 'required|string'
    //         ]);
            
    //         // Check if token matches
    //         if ($payment->verification_token !== $request->token) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid verification token'
    //             ], 401);
    //         }
            
    //         // Check if token is expired
    //         if (now()->gt(Carbon::parse($payment->verified_at)->addDays(3))) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Verification token has expired'
    //             ], 401);
    //         }
            
    //         // Check if payment is already verified
    //         if ($payment->status !== 'paid') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Payment is not in a verifiable state'
    //             ], 400);
    //         }
            
    //         // Additional verification logic if needed
    //         // For example, check if the booking is still valid
            
    //         // Log the verification
    //         Log::info("Payment verified via QR scan", [
    //             'payment_id' => $payment->id,
    //             'reference' => $payment->reference_no,
    //             'verified_by' => auth()->id(),
    //             'ip_address' => $request->ip()
    //         ]);
            
    //         return response()->json([
    //             'success' => true,
    //             'payment' => $payment,
    //             'message' => 'Payment successfully verified'
    //         ]);
            
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Payment not found'
    //         ], 404);
    //     } catch (\Exception $e) {
    //         Log::error("QR verification failed: " . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred during verification'
    //         ], 500);
    //     }
    // }
}
