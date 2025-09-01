<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Order; // Your order model

class MayaWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('Maya Webhook Received: ', $request->all());

        // 1. Verify the webhook signature (CRITICAL FOR SECURITY)
        if (!$this->verifySignature($request)) {
            Log::error('Maya Webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // 2. Process the webhook based on event type
        $webhookData = $request->json()->all();
        
        Log::info('Maya Webhook Processing: ', $webhookData);

        // 3. Handle different webhook events
        switch ($webhookData['type'] ?? '') {
            case 'PAYMENT_SUCCESS':
                return $this->handlePaymentSuccess($webhookData);
                
            case 'PAYMENT_FAILED':
                return $this->handlePaymentFailed($webhookData);
                
            case 'PAYMENT_EXPIRED':
                return $this->handlePaymentExpired($webhookData);
                
            case 'CHECKOUT_SUCCESS':
                return $this->handleCheckoutSuccess($webhookData);
                
            case 'CHECKOUT_FAILURE':
                return $this->handleCheckoutFailure($webhookData);
                
            default:
                Log::warning('Maya Webhook: Unknown event type', $webhookData);
                return response()->json(['status' => 'ignored'], 200);
        }
    }

    /**
     * Verify webhook signature
     */
    private function verifySignature(Request $request): bool
    {
        $secretKey = config('services.maya.secret_key');
        $receivedSignature = $request->header('x-paymaya-sig');
        $payload = $request->getContent();

        // Maya uses HMAC-SHA256 for signature verification
        $expectedSignature = hash_hmac('sha256', $payload, $secretKey);

        Log::debug('Signature Verification:', [
            'received' => $receivedSignature,
            'expected' => $expectedSignature,
            'match' => hash_equals($expectedSignature, $receivedSignature ?? '')
        ]);

        return hash_equals($expectedSignature, $receivedSignature ?? '');
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSuccess(array $data)
    {
        try {
            $rrn = $data['requestReferenceNumber'] ?? null;
            
            if (!$rrn) {
                Log::error('Maya Webhook: Missing reference number', $data);
                return response()->json(['error' => 'Missing reference'], 400);
            }

            // Find and update the order
            $order = Order::where('reference_number', $rrn)->first();
            
            if (!$order) {
                Log::error('Maya Webhook: Order not found', ['rrn' => $rrn]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Update order status
            $order->update([
                'status' => 'paid',
                'payment_status' => 'completed',
                'paid_at' => now(),
                'payment_reference' => $data['id'] ?? null,
                'payment_method' => $data['paymentScheme'] ?? 'maya',
                'webhook_data' => json_encode($data) // Store for reference
            ]);

            Log::info("Order {$rrn} marked as paid successfully");

            // Trigger any post-payment actions
            $this->afterPaymentSuccess($order, $data);

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Maya Webhook Payment Success Error: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed(array $data)
    {
        $rrn = $data['requestReferenceNumber'] ?? null;
        
        if ($rrn) {
            $order = Order::where('reference_number', $rrn)->first();
            
            if ($order) {
                $order->update([
                    'status' => 'payment_failed',
                    'payment_status' => 'failed',
                    'failure_reason' => $data['reason'] ?? 'Unknown',
                    'webhook_data' => json_encode($data)
                ]);
                
                Log::info("Order {$rrn} payment failed");
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Handle expired payment
     */
    private function handlePaymentExpired(array $data)
    {
        $rrn = $data['requestReferenceNumber'] ?? null;
        
        if ($rrn) {
            $order = Order::where('reference_number', $rrn)->first();
            
            if ($order) {
                $order->update([
                    'status' => 'expired',
                    'payment_status' => 'expired',
                    'webhook_data' => json_encode($data)
                ]);
                
                Log::info("Order {$rrn} payment expired");
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Handle checkout success
     */
    private function handleCheckoutSuccess(array $data)
    {
        // This might be similar to payment success
        return $this->handlePaymentSuccess($data);
    }

    /**
     * Handle checkout failure
     */
    private function handleCheckoutFailure(array $data)
    {
        // This might be similar to payment failed
        return $this->handlePaymentFailed($data);
    }

    /**
     * Post-payment success actions
     */
    private function afterPaymentSuccess(Order $order, array $data)
    {
        // Send confirmation email
        // $this->sendConfirmationEmail($order);
        
        // Update inventory
        // $this->updateInventory($order);
        
        // Trigger any other business logic
        Log::info("Post-payment actions completed for order: " . $order->id);
    }

    /**
     * Webhook verification endpoint (for Maya dashboard)
     */
    public function verifyWebhook(Request $request)
    {
        $challenge = $request->get('challenge');
        
        if ($challenge) {
            Log::info('Maya Webhook Verification Challenge: ' . $challenge);
            return response($challenge);
        }
        
        return response()->json(['error' => 'No challenge provided'], 400);
    }
}
