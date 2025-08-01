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
use Endroid\QrCode\Color\Color;


// Encryption
use Illuminate\Support\Facades\Crypt;


class PaymentsController extends Controller
{
    
    public function payments(FacilityBookingLog $booking)
    {
        // Load the FacilityBookingLog relationships
        $booking->load([
            'user', 
            'details', 
            'summaries.facility', 
            'payments',
            'guestDetails.guestType'
        ]); 
        
        $verified_at = $booking->verified_at;
        $user_firstname = $booking->user->firstname ?? 'No user found';
        $total_price = $booking->details->sum('total_price') ?? 0;
        $half_of_total_price = ($total_price * 0.5) ?? 0;
        $reference = $booking->reference ?? 'No reference found';
        
        $facilities = $booking->summaries->map(function ($summary) use ($booking) {
            // Get guest details for this specific facility
            $guestDetails = $booking->guestDetails
                ->where('facility_id', $summary->facility_id)
                ->map(function($detail) {
                    return [
                        'type' => $detail->guestType->type ?? 'Unknown',
                        'quantity' => $detail->quantity
                    ];
                });
                
            return [
                'name' => $summary->facility->name ?? 'Unknown',
                'price' => $summary->facility->price ?? 0,
                'guest_details' => $guestDetails
            ];
        });
        
        // Improved breakfast summary handling
        $breakfastPrice = $booking->summaries->first()->breakfast ?? 0;
        
        
        $firstDetail = $booking->details->first();
        
        // Safer payment status check
        $paymentStatus = $booking->payments->first()->status ?? null;

        
        if ($firstDetail && $firstDetail->checkin_date && $firstDetail->checkout_date) {
            $checkin = Carbon::parse($firstDetail->checkin_date);
            $checkout = Carbon::parse($firstDetail->checkout_date);
            $nights = $checkin->diffInDays($checkout);
        } else {
            $checkin = null;
            $checkout = null;
            $nights = 0;
        }
        
        if (!empty($verified_at)) {
            if ($paymentStatus == 'under_verification') {
                return redirect()->route('payments.submitted');
            } else {
                return view('customer_pages.booking.payments', [
                    'booking' => $booking,
                    'verified_at' => $verified_at,
                    'user_firstname' => $user_firstname,
                    'half_of_total_price' => $half_of_total_price,
                    'total_price' => $total_price,
                    'reference' => $reference,
                    'facilities' => $facilities,
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                    'nights' => $nights,
                    'breakfastPrice' => $breakfastPrice
                ]);
            }
        } else {
            return view('customer_pages.confirm_first');
        }
    }
    
    public function updateCustomerPayment(Request $request, FacilityBookingLog $booking)
    {
        // Get the first payment (or however you want to determine which payment to update)
        $payment = $booking->payments->first();
        
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'No payment record found for this booking.'
            ], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'gcash_number' => 'required|string',
            'reference_no' => 'required|string|max:50|unique:payments,reference_no,'.$payment->id,
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'booking_id' => 'required|exists:facility_booking_log,id'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
        try {
            // Delete old receipt if exists
            if ($payment->receipt_path) {
                $oldPath = public_path($payment->receipt_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
    
            // Store new receipt using public_path()
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
    
            // Update payment record
            $payment->update([
                'gcash_number' => $request->gcash_number,
                'reference_no' => $request->reference_no,
                'receipt_path' => $receiptPath, // Store relative path
                'payment_date' => now()->setTimezone('Asia/Manila'),
                'status' => 'under_verification',
                'paid_at' => now(),
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'redirect' => route('booking.completed', ['booking' => $booking->id])
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
    
    // public function show(Payment $payment)
    // {
    //     // Authorization check - ensure user can view this payment
    //     $this->authorize('view', $payment);
        
    //     return view('payments.show', compact('payment'));
    // }
    
    /**
     * Verify payment and send receipt with QR code
     */
    public function verifyPaymentWithReceipt(Request $request, $id)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
        ]);
        
        $payment = Payments::with(
            'bookingLog.details',    
            'bookingLog.summaries.facility',
            'bookingLog.summaries.breakfast',
            'bookingLog.summaries.bookingDetails',
            'bookingLog.guestDetails.guestType'
        )->findOrFail($id);
        
        $checkout_date = $payment->bookingLog->details->first()->checkout_date;
        $expire_date = Carbon::parse($checkout_date)->endOfDay()->toDateTimeString();
    
        // ✅ Generate a unique token
        do {
            $verificationToken = Str::random(64);
        } while (Payments::where('verification_token', $verificationToken)->exists());
    
        // ✅ Encrypt QR code payload
        $payload = [
            'id' => $payment->id,
            'expires_at' => $expire_date
        ];
        
        $encryptedQrData = Crypt::encrypt($payload);
    
        // ✅ Build QR Code with encrypted string
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($encryptedQrData)
            ->size(500) // 🔼 Increased size
            ->margin(5) // 🔽 Reduced margin
            ->foregroundColor(0, 0, 0) // ⬛ Black
            ->backgroundColor(255, 255, 255) // ⬜ White
            ->build();
        
    
        // ✅ Save QR Code Image
        $directory = public_path('imgs/qr_code/');
        $fileName = 'qr_payment_'.$payment->id.'_'.time().'.png';
        $filePath = $directory . $fileName;
    
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    
        $result->saveToFile($filePath);
    
        // ✅ Update payment record
        $payment->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'amount_paid' => $request->amount_paid,
            'verification_token' => $verificationToken,
            'qr_code_path' => 'imgs/qr_code/' . $fileName
        ]);
    
        // ✅ Send Email with QR Code (Optional)
        $qrCodeUrl = asset('imgs/qr_code/' . $fileName);
        $this->sendVerificationEmail($payment, $qrCodeUrl);
    
        return response()->json([
            'success' => true,
            'payment' => $payment,
            'message' => 'Payment verified and receipt sent with QR code',
            'qr_code_url' => $qrCodeUrl,
            'guest_details' => $payment->bookingLog->guestDetails
        ]);
    }
    
    
    /**
     * Send verification email with QR code
     */
    private function sendVerificationEmail($payment, $qrCodeUrl)
    {
        try {
            // Validate the payment has all required data
            if (!$payment->bookingLog || !$payment->bookingLog->user) {
                throw new \Exception('Booking or user information missing');
            }
    
            $customer_email = $payment->bookingLog->user->email;
            
            // Validate email format
            if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Invalid email format: ' . $customer_email);
            }
    
            $customMessage = "Thank you for your payment. Please present this QR code upon arrival at the resort to verify your reservation.";
    
            // Validate payment has required fields
            $requiredFields = ['reference_no', 'amount', 'verified_at'];
            foreach ($requiredFields as $field) {
                if (empty($payment->{$field})) {
                    throw new \Exception("Payment missing required field: $field");
                }
            }
    
            // Send email with error handling
            Mail::to($customer_email)->send(new PaymentVerifiedMail(
                $payment,
                $qrCodeUrl,
                $customMessage
            ));
    
            // Log successful email sending
            \Log::info("Verification email sent to {$customer_email} for payment {$payment->reference_no}");
    
        } catch (\Exception $e) {
            \Log::error("Failed to send verification email for payment {$payment->id}: " . $e->getMessage());
            throw $e; // Re-throw if you want calling method to handle it
        }
    }
    
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
