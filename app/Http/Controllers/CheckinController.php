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
            $data = $request->validate([
                'qr_data' => 'required|string'
            ]);
    
            // ✅ Decrypt payload
            $payload = Crypt::decrypt($data['qr_data']);
    
            // Check expiration
            if (Carbon::parse($payload['expires_at'])->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code has expired.'
                ], 400);
            }
    
            // Lookup by token
            $payment = Payments::with(['bookingLog', 'bookingLog.user'])
                ->where('verification_token', $payload['token'])
                ->first();
    
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired QR token.'
                ], 404);
            }
    
            if ($payment->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not yet verified.'
                ], 400);
            }
    
            // ✅ Optional: prevent re-scan
            if ($payment->checked_in_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'This QR code has already been used.'
                ], 409);
            }
    
            // Mark as checked in
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
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code format.'
            ], 400);
        } catch (\Exception $e) {
            Log::error("QR verification error: " . $e->getMessage());
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
