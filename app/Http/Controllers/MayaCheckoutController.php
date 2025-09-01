<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class MayaCheckoutController extends Controller
{
    public function index()
    {
        return view('customer_pages.maya.index');
    }

    public function createCheckoutSession(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'totalAmount' => 'required|numeric|min:1'
        ]);

        // Get configuration from services.php
        $baseUrl = config('services.maya.base_url');
        $publicKey = config('services.maya.public_key');

        // Debug: Check if keys are loaded correctly
        Log::debug('Maya Config - Base URL: ' . $baseUrl);
        Log::debug('Maya Config - Public Key: ' . (empty($publicKey) ? 'EMPTY' : 'SET'));

        // Generate a unique reference number
        $rn = 'ORDER-' . Str::upper(Str::random(8));

        // Build the request payload for Maya
        $requestBody = [
            'totalAmount' => [
                'value' => $validated['totalAmount'],
                'currency' => 'PHP',
            ],
            'buyer' => [
                'firstName' => 'Test',
                'lastName' => 'User',
                'contact' => [
                    'phone' => '+639171234567',
                    'email' => 'test@example.com'
                ]
            ],
            'redirectUrl' => [
                'success' => route('maya.checkout.success'),
                'failure' => route('maya.checkout.failure'),
                'cancel' => route('maya.checkout.cancel'),
            ],
            'requestReferenceNumber' => $rn,
            'webhookUrl' => route('maya.webhook'), // Add webhook URL to payload
        ];

        Log::debug('Maya Request Body: ', $requestBody);

        // Make the API call to Maya
        try {
            $response = Http::withBasicAuth($publicKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$baseUrl}/checkout/v1/checkouts", $requestBody);

            // Log the full response for debugging
            Log::debug('Maya API Response: ', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            // Check if the API call was successful
            if ($response->successful()) {
                $responseData = $response->json();

                // Create order with pending status
                Order::create([
                    'reference_number' => $rn,
                    'amount' => $validated['totalAmount'],
                    'status' => 'pending',
                    'payment_gateway' => 'maya'
                ]);

                // Store the reference number in session for redirect pages
                session(['maya_rrn' => $rn]);

                // Redirect the user to Maya's payment page
                return redirect()->away($responseData['redirectUrl']);
            } else {
                // Handle API error with more details
                $error = $response->json();
                $statusCode = $response->status();

                Log::error('Maya API Error: ', [
                    'status' => $statusCode,
                    'error' => $error,
                    'request_body' => $requestBody
                ]);

                $errorMessage = 'Failed to initialize checkout. ';

                // Add specific error messages based on status code
                if ($statusCode === 401) {
                    $errorMessage .= 'Authentication failed. Please check your API keys.';
                } elseif ($statusCode === 404) {
                    $errorMessage .= 'API endpoint not found.';
                } else {
                    $errorMessage .= 'Please try again.';
                }

                return back()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            // Handle network errors
            Log::error('Maya Checkout Exception: ' . $e->getMessage());
            Log::error('Exception Trace: ' . $e->getTraceAsString());

            return back()->with('error', 'A network error occurred: ' . $e->getMessage());
        }
    }

    public function handleSuccess(Request $request)
    {
        // Display success message but don't update order status here
        // The actual payment confirmation will come from webhook
        $referenceNumber = session('maya_rrn');

        return view('customer_pages.maya.checkout_success', [
            'referenceNumber' => $referenceNumber,
            'message' => 'Payment processing completed. Please wait for confirmation.'
        ]);
    }

    public function handleFailure(Request $request)
    {
        $referenceNumber = session('maya_rrn');

        return view('customer_pages.maya.checkout_failure')->with([
            'error' => 'Payment failed. Please try again.',
            'referenceNumber' => $referenceNumber
        ]);
    }

    public function handleCancel(Request $request)
    {
        $referenceNumber = session('maya_rrn');

        return view('customer_pages.maya.checkout_cancel')->with([
            'message' => 'Payment was cancelled.',
            'referenceNumber' => $referenceNumber
        ]);
    }

    public function handleWebhook(Request $request)
    {
        Log::debug('Your Now in webHook');
        // Start webhook debugging
        $webhookId = 'WEBHOOK-' . Str::random(8);
        $startTime = microtime(true);

        Log::info("[$webhookId] Maya Webhook Received", [
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'raw_content' => $request->getContent(),
            'json_data' => $request->all()
        ]);

        // Verify webhook signature
        $signatureResult = $this->verifyWebhookSignature($request);
        if (!$signatureResult['valid']) {
            Log::error("[$webhookId] Webhook Signature Verification Failed", [
                'reason' => $signatureResult['reason'],
                'received_signature' => $request->header('x-payment-signature'),
                'expected_signature' => $signatureResult['expected'] ?? null,
                'payload_length' => strlen($request->getContent())
            ]);
            return response()->json(['error' => 'Invalid signature', 'webhook_id' => $webhookId], 401);
        }

        Log::debug("[$webhookId] Webhook signature verified successfully");

        $webhookData = $request->json()->all();
        $referenceNumber = $webhookData['requestReferenceNumber'] ?? null;

        if (!$referenceNumber) {
            Log::error("[$webhookId] No reference number in webhook data", [
                'webhook_data' => $webhookData,
                'available_keys' => array_keys($webhookData)
            ]);
            return response()->json([
                'error' => 'Invalid webhook data',
                'webhook_id' => $webhookId
            ], 400);
        }

        Log::info("[$webhookId] Processing webhook for reference: $referenceNumber", [
            'event_type' => $webhookData['eventType'] ?? 'unknown',
            'payment_id' => $webhookData['id'] ?? null
        ]);

        // Handle different webhook events
        $eventType = $webhookData['eventType'] ?? 'UNKNOWN_EVENT';
        $handlingResult = null;

        switch ($eventType) {
            case 'PAYMENT_SUCCESS':
                $handlingResult = $this->handlePaymentSuccess($webhookData, $webhookId);
                break;

            case 'PAYMENT_FAILED':
                $handlingResult = $this->handlePaymentFailed($webhookData, $webhookId);
                break;

            case 'PAYMENT_EXPIRED':
                $handlingResult = $this->handlePaymentExpired($webhookData, $webhookId);
                break;

            case 'PAYMENT_CANCELLED':
                $handlingResult = $this->handlePaymentCancelled($webhookData, $webhookId);
                break;

            default:
                Log::warning("[$webhookId] Unhandled webhook event type", [
                    'event_type' => $eventType,
                    'full_data' => $webhookData
                ]);
                $handlingResult = ['status' => 'unhandled', 'message' => 'Event type not handled'];
                break;
        }

        // Log webhook processing completion
        $processingTime = round((microtime(true) - $startTime) * 1000, 2);
        Log::info("[$webhookId] Webhook processing completed", [
            'processing_time_ms' => $processingTime,
            'reference_number' => $referenceNumber,
            'event_type' => $eventType,
            'result' => $handlingResult
        ]);

        return response()->json([
            'status' => 'success',
            'webhook_id' => $webhookId,
            'processing_time_ms' => $processingTime,
            'handled_event' => $eventType
        ]);
    }

    /**
     * Verify webhook signature with detailed debugging
     */
    private function verifyWebhookSignature(Request $request)
    {
        $secretKey = config('services.maya.secret_key');

        // Debug: Check if secret key is configured
        if (empty($secretKey)) {
            Log::error('Maya secret key not configured in services.php');
            return [
                'valid' => false,
                'reason' => 'Secret key not configured'
            ];
        }

        $receivedSignature = $request->header('x-payment-signature');

        // Check if signature header exists
        if (empty($receivedSignature)) {
            return [
                'valid' => false,
                'reason' => 'Missing signature header'
            ];
        }

        // Get the raw payload
        $payload = $request->getContent();

        // Check if payload is empty
        if (empty($payload)) {
            return [
                'valid' => false,
                'reason' => 'Empty payload'
            ];
        }

        // Generate expected signature
        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secretKey, true));

        // Compare signatures securely
        $isValid = hash_equals($expectedSignature, $receivedSignature);

        return [
            'valid' => $isValid,
            'expected' => $expectedSignature,
            'received' => $receivedSignature,
            'reason' => $isValid ? 'Signature valid' : 'Signature mismatch'
        ];
    }

    /**
     * Handle successful payment with debugging
     */
    private function handlePaymentSuccess(array $data, string $webhookId)
    {
        $paymentId = $data['id'] ?? null;
        $referenceNumber = $data['requestReferenceNumber'] ?? null;
        $amount = $data['amount']['value'] ?? null;
        $currency = $data['amount']['currency'] ?? null;

        Log::info("[$webhookId] Processing successful payment", [
            'payment_id' => $paymentId,
            'reference_number' => $referenceNumber,
            'amount' => $amount,
            'currency' => $currency
        ]);

        try {
            // Find the order
            $order = Order::where('reference_number', $referenceNumber)->first();

            if (!$order) {
                Log::error("[$webhookId] Order not found for successful payment", [
                    'reference_number' => $referenceNumber,
                    'payment_id' => $paymentId
                ]);
                return ['status' => 'error', 'message' => 'Order not found'];
            }

            Log::debug("[$webhookId] Found order for update", [
                'order_id' => $order->id,
                'current_status' => $order->status
            ]);

            // Update the order
            $updateData = [
                'status' => 'paid',
                'payment_id' => $paymentId,
                'paid_at' => now(),
                'payment_data' => json_encode($data),
                'currency' => $currency
            ];

            $order->update($updateData);

            Log::info("[$webhookId] Order updated successfully", [
                'reference_number' => $referenceNumber,
                'new_status' => 'paid',
                'payment_id' => $paymentId
            ]);

            // Additional success actions can be added here
            // Example: Send email, update inventory, etc.

            return ['status' => 'success', 'message' => 'Payment processed successfully'];
        } catch (\Exception $e) {
            Log::error("[$webhookId] Error processing successful payment", [
                'reference_number' => $referenceNumber,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle failed payment with debugging
     */
    private function handlePaymentFailed(array $data, string $webhookId)
    {
        $referenceNumber = $data['requestReferenceNumber'] ?? null;
        $errorReason = $data['reason'] ?? 'Unknown error';
        $paymentId = $data['id'] ?? null;

        Log::warning("[$webhookId] Processing failed payment", [
            'reference_number' => $referenceNumber,
            'payment_id' => $paymentId,
            'reason' => $errorReason
        ]);

        try {
            $order = Order::where('reference_number', $referenceNumber)->first();

            if (!$order) {
                Log::error("[$webhookId] Order not found for failed payment", [
                    'reference_number' => $referenceNumber
                ]);
                return ['status' => 'error', 'message' => 'Order not found'];
            }

            $order->update([
                'status' => 'failed',
                'failure_reason' => $errorReason,
                'payment_data' => json_encode($data)
            ]);

            Log::info("[$webhookId] Order marked as failed", [
                'reference_number' => $referenceNumber,
                'failure_reason' => $errorReason
            ]);

            return ['status' => 'success', 'message' => 'Payment failure processed'];
        } catch (\Exception $e) {
            Log::error("[$webhookId] Error processing failed payment", [
                'reference_number' => $referenceNumber,
                'error_message' => $e->getMessage()
            ]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle expired payment with debugging
     */
    private function handlePaymentExpired(array $data, string $webhookId)
    {
        $referenceNumber = $data['requestReferenceNumber'] ?? null;
        $paymentId = $data['id'] ?? null;

        Log::info("[$webhookId] Processing expired payment", [
            'reference_number' => $referenceNumber,
            'payment_id' => $paymentId
        ]);

        try {
            $order = Order::where('reference_number', $referenceNumber)->first();

            if ($order) {
                $order->update([
                    'status' => 'expired',
                    'payment_data' => json_encode($data)
                ]);

                Log::info("[$webhookId] Order marked as expired", [
                    'reference_number' => $referenceNumber
                ]);

                return ['status' => 'success', 'message' => 'Payment expiration processed'];
            }

            Log::warning("[$webhookId] Order not found for expired payment", [
                'reference_number' => $referenceNumber
            ]);

            return ['status' => 'warning', 'message' => 'Order not found'];
        } catch (\Exception $e) {
            Log::error("[$webhookId] Error processing expired payment", [
                'reference_number' => $referenceNumber,
                'error_message' => $e->getMessage()
            ]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle cancelled payment with debugging
     */
    private function handlePaymentCancelled(array $data, string $webhookId)
    {
        $referenceNumber = $data['requestReferenceNumber'] ?? null;
        $paymentId = $data['id'] ?? null;

        Log::info("[$webhookId] Processing cancelled payment", [
            'reference_number' => $referenceNumber,
            'payment_id' => $paymentId
        ]);

        try {
            $order = Order::where('reference_number', $referenceNumber)->first();

            if ($order) {
                $order->update([
                    'status' => 'cancelled',
                    'payment_data' => json_encode($data)
                ]);

                Log::info("[$webhookId] Order marked as cancelled", [
                    'reference_number' => $referenceNumber
                ]);

                return ['status' => 'success', 'message' => 'Payment cancellation processed'];
            }

            Log::warning("[$webhookId] Order not found for cancelled payment", [
                'reference_number' => $referenceNumber
            ]);

            return ['status' => 'warning', 'message' => 'Order not found'];
        } catch (\Exception $e) {
            Log::error("[$webhookId] Error processing cancelled payment", [
                'reference_number' => $referenceNumber,
                'error_message' => $e->getMessage()
            ]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check payment status (optional - for manual verification)
     */
    public function checkPaymentStatus($referenceNumber)
    {
        try {
            $order = Order::where('reference_number', $referenceNumber)->first();

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            return response()->json([
                'reference_number' => $order->reference_number,
                'status' => $order->status,
                'amount' => $order->amount,
                'paid_at' => $order->paid_at,
                'failure_reason' => $order->failure_reason
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
