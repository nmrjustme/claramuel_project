<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\FacilityBookingLog;
use Illuminate\Support\Facades\Crypt;

class CheckinController extends Controller
{
    public function showScanner()
    {
        return view('admin.qr_scanner.index');
    }
        
    public function verifyQrCode(Request $request)
    {
        \Log::debug('Incoming request headers:', $request->headers->all());
    
        try {
            // Ensure JSON format
            if (!$request->isJson()) {
                \Log::error('Non-JSON request received');
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be JSON format',
                    'received_content_type' => $request->header('Content-Type')
                ], 415);
            }
    
            $data = $request->json()->all();
            \Log::debug('Parsed request data:', $data);
    
            if (empty($data['qr_data'])) {
                \Log::error('Missing qr_data field');
                return response()->json([
                    'success' => false,
                    'message' => 'Missing qr_data field',
                    'received_data' => $data
                ], 400);
            }
    
            // ✅ Decrypt QR code payload
            try {
                $payload = Crypt::decrypt($data['qr_data']);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                \Log::error('Decryption failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or tampered QR code.',
                ], 400);
            }
    
            \Log::debug('Decrypted payload:', $payload);
    
            // Validate payload structure
            if (empty($payload['id']) || empty($payload['expires_at'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code missing required fields',
                    'payload_received' => $payload
                ], 400);
            }
    
            // Check for expiry
            if (Carbon::now()->greaterThan(Carbon::parse($payload['expires_at']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code has expired.',
                    'expired_at' => $payload['expires_at']
                ], 403);
            }
    
            // ✅ Find payment by ID
            $payment = Payments::with(['bookingLog', 'bookingLog.user'])
                ->where('id', $payload['id'])
                ->first();
    
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code not recognized',
                    'payment_id' => $payload['id']
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'QR code verified successfully',
                'payment_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Server error:', ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
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