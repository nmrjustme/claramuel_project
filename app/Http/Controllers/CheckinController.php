<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\FacilityBookingLog;
use Vinkla\Hashids\Facades\Hashids;

class CheckinController extends Controller
{
    public function showScanner()
    {
        return view('admin.qr_scanner.checkin');
    }

    public function verifyQrCode(Request $request)
    {
        try {
            if (!$request->isJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be JSON format',
                    'received_content_type' => $request->header('Content-Type')
                ], 415);
            }

            $data = $request->json()->all();

            if (empty($data['qr_data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing qr_data field',
                    'received_data' => $data
                ], 400);
            }

            // ✅ Extract raw QR payload
            $qrRaw = $data['qr_data'];
            if (is_string($qrRaw)) {
                $qrRaw = trim($qrRaw, '"');
            }

            // ✅ Split into [hashid, timestamp]
            $parts = explode('|', $qrRaw);
            if (count($parts) !== 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR format'
                ], 400);
            }

            [$hashId, $expireTs] = $parts;

            // ✅ Decode booking ID
            $bookingId = Hashids::decode($hashId)[0] ?? null;
            $expireDate = Carbon::createFromTimestamp((int)$expireTs);

            if (!$bookingId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code'
                ], 400);
            }

            // ✅ Check expiry
            if (Carbon::now()->greaterThan($expireDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code has expired',
                    'expired_at' => $expireDate->toDateTimeString()
                ], 403);
            }

            // ✅ Find booking/payment
            $payment = Payments::with(['bookingLog', 'bookingLog.user'])
                ->where('facility_log_id', $bookingId)
                ->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code not recognized',
                    'booking_id' => $bookingId
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'QR code verified successfully',
                'booking_id' => $bookingId,
                'payment_id' => $payment->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Server error verifying QR: ' . $e->getMessage());
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
            'checked_in_at' => Carbon::now(),
            'checked_in_by' => auth()->id(),
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
        \Log::debug('QR Upload Verification Request', ['request' => $request->all()]);

        try {
            // ✅ Validate JSON request
            if (!$request->isJson()) {
                \Log::error('Non-JSON request received for QR upload', [
                    'content_type' => $request->header('Content-Type')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Request must be JSON format',
                    'received_content_type' => $request->header('Content-Type')
                ], 415);
            }
            
            $data = $request->json()->all();
            \Log::debug('Parsed QR upload data', ['data' => $data]);

            if (empty($data['qr_data'])) {
                \Log::error('Missing qr_data field in upload', ['received_data' => $data]);
                return response()->json([
                    'success' => false,
                    'message' => 'Missing qr_data field',
                    'received_data' => $data
                ], 400);
            }

            // ✅ Extract raw QR payload (string format: hashid|timestamp)
            $qrRaw = $data['qr_data'];
            if (is_string($qrRaw)) {
                $qrRaw = trim($qrRaw, '"'); // remove quotes if present
            }

            $parts = explode('|', $qrRaw);
            if (count($parts) !== 2) {
                \Log::error('Invalid QR format in upload', ['qr_data' => $qrRaw]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format'
                ], 400);
            }
            
            [$hashId, $expireTs] = $parts;
            
            // ✅ Decode booking id
            $bookingId = Hashids::decode($hashId)[0] ?? null;
            $expireDate = Carbon::createFromTimestamp((int)$expireTs);

            if (!$bookingId) {
                \Log::error('Invalid booking ID decoded from QR', ['hashId' => $hashId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code'
                ], 400);
            }

            // ✅ Check expiry
            if (Carbon::now()->greaterThan($expireDate)) {
                \Log::warning('Expired QR code uploaded', ['expired_at' => $expireDate->toDateTimeString()]);
                return response()->json([
                    'success' => false,
                    'message' => 'QR code has expired',
                    'expired_at' => $expireDate->toDateTimeString()
                ], 403);
            }

            // ✅ Check if QR is already used
            if ($this->qrInUsed($bookingId)) {
                $qr_path = Payments::where('facility_log_id', $bookingId)->value('qr_code_path');
                return response()->json([
                    'success' => false,
                    'message' => 'QR code is already in use.',
                    'qr_path' => $qr_path
                ], 409);
            }

            // ✅ Find related payment
            $payment = Payments::with(['bookingLog', 'bookingLog.user'])
                ->where('facility_log_id', $bookingId)
                ->first();

            if (!$payment) {
                \Log::error('Payment not found for uploaded QR', ['booking_id' => $bookingId]);
                return response()->json([
                    'success' => false,
                    'message' => 'QR code not recognized',
                    'booking_id' => $bookingId
                ], 404);
            }

            \Log::info('QR upload verified successfully', ['payment_id' => $payment->id]);
            return response()->json([
                'success' => true,
                'message' => 'QR code verified successfully',
                'booking_id' => $bookingId,
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('QR upload processing error', ['exception' => $e->getMessage()]);
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
            'bookingLog.summaries.bookingDetails',
            'bookingLog.guestDetails.guestType'
        )->findOrFail($id);
        
        $this->updateCheckedin($payment);
        $this->updateQrStatus($payment, 'in_used');
        
        return view('admin.print_Receipt.checkin', ['payment' => $payment]);
    }
    
    public function searchGuests(Request $request)
    {
        // Get search parameters
        $firstName = $request->query('firstname', '');
        $lastName = $request->query('lastname', '');
        $reservationCode = $request->query('reservationCode', '');
        
        // Return empty if no criteria provided
        if (empty($firstName) && empty($lastName) && empty($reservationCode)) {
            return response()->json([]);
        }
        
        $bookings = FacilityBookingLog::with(['user', 'payments'])
            ->where(function($query) use ($firstName, $lastName, $reservationCode) {
                // Search by reservation code
                if (!empty($reservationCode)) {
                    $query->where('code', 'like', "%{$reservationCode}%")
                        ->orWhere('reference', 'like', "%{$reservationCode}%");
                }
                
                // Search by user details
                if (!empty($firstName) || !empty($lastName)) {
                    $query->orWhereHas('user', function($q) use ($firstName, $lastName) {
                        if (!empty($firstName)) {
                            $q->where('firstname', 'like', "%{$firstName}%");
                        }
                        if (!empty($lastName)) {
                            $q->where('lastname', 'like', "%{$lastName}%");
                        }
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        // Format results
        $results = $bookings->map(function($booking) {
            $paymentId = $booking->payments->first()?->id ?? null;
            
            return [
                'id' => $booking->id,
                'name' => $booking->user ? "{$booking->user->firstname} {$booking->user->lastname}" : 'Unknown',
                'code' => $booking->code,
                'email' => $booking->user->email ?? '',
                'phone' => $booking->user->phone ?? '',
                'payment_id' => $paymentId,
                'type' => 'booking',
                'status' => $booking->status,
                'checked_in_at' => $booking->checked_in_at,
                'booking_date' => $booking->created_at->format('M j, Y'),
                'checkin_date' => $booking->checkin_date ?? null,
                'checkout_date' => $booking->checkout_date ?? null,
            ];
        });
        
        return response()->json($results);
    }

    public function updateStatus($id)
    {
        $payment = Payments::with('bookingLog')->findorFail($id);
        
        $payment->bookingLog->update([
            'status' => "checked_in"
        ]);
    }

}
