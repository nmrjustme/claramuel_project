<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\FacilityBookingLog;

// Encryption
use Illuminate\Support\Facades\Crypt;

class CheckinController extends Controller
{
    public function showScanner()
    {
        return view('admin.qr_scanner.index');
    }

    public function verifyQrCode(Request $request)
    {
        try {
            // ✅ Read JSON data properly (since frontend sends raw JSON)
            $qrData = $request->json('qr_data');
    
            // ❌ If empty or not a string
            if (empty($qrData) || !is_string($qrData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code data format.'
                ], 400);
            }
    
            // ✅ Decode the JSON inside qr_data (the payload itself)
            $payload = json_decode($qrData, true);
    
            if (!$payload || !isset($payload['token'], $payload['expires_at'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format or missing fields.'
                ], 400);
            }
    
            // ✅ Find the payment by token
            $payment = Payments::with(['bookingLog', 'bookingLog.user'])
                ->where('verification_token', $payload['token'])
                ->first();
    
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code is not recognized.'
                ], 404);
            }
    
            // ❌ Expired QR code
            if (\Carbon\Carbon::parse($payload['expires_at'])->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code has expired.'
                ], 400);
            }
    
            // ❌ Already checked in
            if ($payment->bookingLog->checked_in_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'This QR code has already been used.'
                ], 409);
            }
    
            // ✅ Check-in the guest
            $payment->bookingLog->update([
                'checked_in_at' => now(),
                'checked_in_by' => auth()->id()
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Verification successful',
                'payment_id' => $payment->id,
                'reference_no' => $payment->reference_no,
                'guest_name' => $payment->bookingLog->user->name ?? 'Guest',
                'amount' => $payment->amount,
                'checked_in_at' => now()->format('Y-m-d H:i:s')
            ]);
    
        } catch (\Exception $e) {
            \Log::error("QR verification error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }


    
    public function showPrinting($id)
    {
        $payment = Payments::with(
            'bookingLog.details',    
            'bookingLog.summaries.facility',
            'bookingLog.summaries.breakfast',
            'bookingLog.summaries.bookingDetails'
        )->findOrFail($id);
    
        return view('admin.printCheckinPage.index', ['payment' => $payment]);
    }


}
