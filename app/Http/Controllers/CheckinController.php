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
            
             if ($this->qrInUsed($payload['id'])) {
                $qr_path = Payments::where('id', $payload['id'])->value('qr_code_path');
                return response()->json([
                    'success' => false,
                    'message' => 'QR code is already in use.',
                    'qr_path' => $qr_path
                ], 409);    
            }
            
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

            $this->updateCheckedin($payment);
            $this->updateQrStatus($payment, 'in_used');
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

    private function updateCheckedin($payment)
    {
            $payment->bookingLog->update([
                'checked_in_at' => Carbon::now()
            ]);
    }

    private function updateQrStatus($payment, $status)
    {
        $payment->update([
            'qr_status' => $status
        ]);
    }

    private function QrInUsed($id)
    {
        $payment = Payments::where('id', $id)
            ->select('qr_status')
            ->first();
        
        return $payment && $payment->qr_status === 'in_used';
    }
        

    public function processUploadQrUpload(Request $request)
    {
        \Log::debug('QR Upload Verification Request:', $request->all());

        try {
            // Validate JSON request
            if (!$request->isJson()) {
                \Log::error('Non-JSON request received for QR upload');
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be JSON format',
                    'received_content_type' => $request->header('Content-Type')
                ], 415);
            }
            
            $data = $request->json()->all();
            \Log::debug('Parsed QR upload data:', $data);

            if (empty($data['qr_data'])) {
                \Log::error('Missing qr_data field in upload');
                return response()->json([
                    'success' => false,
                    'message' => 'Missing qr_data field',
                    'received_data' => $data
                ], 400);
            }

            // Decrypt QR code payload
            try {
                $payload = Crypt::decrypt($data['qr_data']);
                \Log::debug('Decrypted QR upload payload:', $payload);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                \Log::error('Decryption failed for uploaded QR: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or tampered QR code.',
                ], 400);
            }
            
            // if ($this->qrInUsed($payload['id'])) {
            //     $qr_path = Payments::where('id', $payload['id'])->value('qr_code_path');
                
            //     return redirect()->route('qr-in-used', ['qr_path' => $qr_path]);
            // }
            
            if ($this->qrInUsed($payload['id'])) {
                $qr_path = Payments::where('id', $payload['id'])->value('qr_code_path');
                return response()->json([
                    'success' => false,
                    'message' => 'QR code is already in use.',
                    'qr_path' => $qr_path
                ], 409);    
            }
            
            // Validate payload structure
            if (empty($payload['id']) || empty($payload['expires_at'])) {
                \Log::error('Invalid payload structure in uploaded QR', ['payload' => $payload]);
                return response()->json([
                    'success' => false,
                    'message' => 'QR code missing required fields',
                    'payload_received' => $payload
                ], 400);
            }
            
            // Check for expiry
            if (Carbon::now()->greaterThan(Carbon::parse($payload['expires_at']))) {
                \Log::warning('Expired QR code uploaded', ['expired_at' => $payload['expires_at']]);
                return response()->json([
                    'success' => false,
                    'message' => 'QR code has expired.',
                    'expired_at' => $payload['expires_at']
                ], 403);
            }

            // Find payment by ID
            $payment = Payments::with(['bookingLog', 'bookingLog.user'])
                ->where('id', $payload['id'])
                ->first();

            $this->updateCheckedin($payment);
            $this->updateQrStatus($payment, 'in_used');
            if (!$payment) {
                \Log::error('Payment not found for uploaded QR', ['payment_id' => $payload['id']]);
                return response()->json([
                    'success' => false,
                    'message' => 'QR code not recognized',
                    'payment_id' => $payload['id']
                ], 404);
            }
            
            \Log::info('QR upload verified successfully', ['payment_id' => $payment->id]);
            return response()->json([
                'success' => true,
                'message' => 'QR code verified successfully',
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('QR upload processing error:', ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }
    
    public function getCustomerDetails($paymentId)
    {
        try {
            $payment = Payments::with(
                'bookingLog.user',
            )->findOrFail($paymentId);
            
            return response()->json([
                'success' => true,
                'customer' => [
                    'name' => $payment->bookingLog->user->firstname,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching customer details:', ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customer details: ' . $e->getMessage()
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
