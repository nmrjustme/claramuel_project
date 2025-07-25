<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payments::with(['bookingLog.user', 'bookingLog.details'])
            ->whereNotNull('reference_no')
            ->orderBy('id', 'desc');

            
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $payments = $query->paginate(15);
        
        return view('admin.payment.index', [
            'payments' => $payments,
            'status' => $request->status // This will be null if no status filter is applied
        ]);
    }

    public function stream()
    {
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() {
            while (true) {
                $newPayments = Payments::with(['bookingLog.user'])
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->get();

                if ($newPayments->isNotEmpty()) {
                    foreach ($newPayments as $payment) {
                        $payment->is_read = true;
                        $payment->save();

                        echo "data: " . json_encode([
                            'type' => 'new_payment',
                            'payment' => $payment
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }
                }

                $updatedPayments = Payments::with(['bookingLog.user'])
                    ->where('is_updated', true)
                    ->orderBy('updated_at', 'desc')
                    ->get();

                if ($updatedPayments->isNotEmpty()) {
                    foreach ($updatedPayments as $payment) {
                        $payment->is_updated = false;
                        $payment->save();

                        echo "data: " . json_encode([
                            'type' => 'payment_updated',
                            'payment' => $payment
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }
                }

                sleep(1);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }

    public function getPaymentRow($id)
    {
        $payment = Payments::with(['bookingLog.user'])->findOrFail($id);
        return view('admin.payment.payment_row', compact('payment'));
    }

    public function getPaymentDetails($id)
    {
        try {
            $payment = Payments::with('bookingLog.user')->find($id);
            
            if (!$payment->is_read) {
                $payment->is_read = true;
                $payment->save();
            }
    
            return response()->json([
                'success' => true,
                'html' => view('admin.payment.payment_details', compact('payment'))->render(),
                'payment' => $payment
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }
    }

    public function verifyPayment($id)
    {
        $payment = Payments::findOrFail($id);
        
        if ($payment->method === 'gcash' && empty($payment->gcash_reference)) {
            return response()->json([
                'success' => false,
                'message' => 'GCash reference number is required for verification'
            ], 422);
        }

        // Get the amount paid from the request
        $amountPaid = request('amount_paid', $payment->amount);
        $totalAmount = $payment->bookingLog->details->sum('total_price');

        // Validate amount paid doesn't exceed total amount
        if ($amountPaid > $totalAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Amount paid cannot exceed total amount'
            ], 422);
        }

        $payment->status = 'verified';
        $payment->amount_paid = $amountPaid;
        $payment->verified_by = auth()->id();
        $payment->verified_at = now();
        $payment->is_updated = true;
        $payment->save();
        
        // Update booking log payment status
        $paymentStatus = ($amountPaid >= $totalAmount) ? 'paid' : 'partially_paid';
        $payment->bookingLog->payment_status = $paymentStatus;
        $payment->bookingLog->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully',
            'payment' => $payment
        ]);
    }

    public function rejectPayment($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'gcash_reference_mismatch' => 'nullable|boolean'
        ]);

        $payment = Payments::findOrFail($id);
        
        $rejectionReason = $request->reason;
        if ($request->gcash_reference_mismatch) {
            $rejectionReason .= " (GCash reference mismatch)";
        }
        
        $payment->status = 'rejected';
        $payment->rejection_reason = $rejectionReason;
        $payment->is_updated = true;
        $payment->save();
        
        $payment->bookingLog->payment_status = 'rejected';
        $payment->bookingLog->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment rejected successfully',
            'payment' => $payment
        ]);
    }

    public function updateReference($id, Request $request)
    {
        $request->validate([
            'gcash_reference' => 'required|string|max:255'
        ]);

        $payment = Payments::findOrFail($id);
        $payment->gcash_reference = $request->gcash_reference;
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => 'GCash reference updated',
            'payment' => $payment
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->q;
        
        $payments = Payments::with(['bookingLog.user'])
            ->where(function($q) use ($query) {
                $q->where('reference_no', 'like', "%$query%")
                  ->orWhere('reference_no', 'like', "%$query%")
                  ->orWhere('gcash_number', 'like', "%$query%")
                  ->orWhereHas('bookingLog.user', function($q) use ($query) {
                      $q->where('firstname', 'like', "%$query%")
                        ->orWhere('lastname', 'like', "%$query%")
                        ->orWhere('email', 'like', "%$query%");
                  });
            })
            ->orderBy('id', 'desc')
            ->get();

        $html = '';
        foreach ($payments as $payment) {
            $html .= view('admin.payment.payment_row', compact('payment'))->render();
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $payments->count(),
            'total' => Payments::count()
        ]);
    }
}
