<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Breakfast;
use App\Models\Facility;


class MayaCheckoutController extends Controller
{
    public function index()
    {
        return view('customer_pages.maya.index');
    }

    public function createCheckoutSession($token)
    {
        Log::info('Maya Checkout - Token: ' . $token);

        $bookingData = Cache::get('booking_confirmation_' . $token);

        if (!$bookingData) {
            Log::error('Maya Checkout - No booking data found for token: ' . $token);
            return redirect()->route('bookings.customer-info')->with('error', 'Invalid or expired booking session.');
        }

        // Extract data from cached booking
        $user_firstname = $bookingData['firstname'] ?? 'Guest';
        $user_lastname = $bookingData['lastname'] ?? 'Guest';
        $user_phone = $bookingData['phone'] ?? '+639000000000';
        $user_email = $bookingData['email'] ?? 'guest@example.com';
        $total_price = $bookingData['total_price'] ?? 0;
        $amount_to_pay = $bookingData['amount_to_pay'] ?? $total_price; // Use the correct amount to pay
        $reservation_code = $bookingData['reservation_code'] ?? 'NO-CODE';
        
        $checkin = Carbon::parse($bookingData['checkin_date']);
        $checkout = Carbon::parse($bookingData['checkout_date']);
        $nights = $checkin->diffInDays($checkout);

        Log::debug('Booking Data:', [
            'breakfast_included' => $bookingData['breakfast_included'] ?? 'not set',
            'total_price' => $total_price,
            'amount_to_pay' => $amount_to_pay,
            'nights' => $nights,
            'facilities_count' => count($bookingData['facilities'] ?? [])
        ]);

        // Get breakfast price if included
        $breakfastPrice = 0;
        $isBreakfastIncluded = false;

        if (isset($bookingData['breakfast_included']) && $bookingData['breakfast_included']) {
            $isBreakfastIncluded = true;
            $breakfast = Breakfast::where('status', 'Active')->first();

            if ($breakfast) {
                $breakfastPrice = $breakfast->price;
                Log::debug('Breakfast found:', [
                    'price' => $breakfastPrice,
                    'breakfast_id' => $breakfast->id
                ]);
            } else {
                Log::warning('No active breakfast found in database');
            }
        }

        Log::debug('Breakfast status:', [
            'included' => $isBreakfastIncluded,
            'price_per_night' => $breakfastPrice,
            'total_breakfast_cost' => $breakfastPrice * $nights
        ]);

        // Calculate the ratio for prorating the amount to pay
        $paymentRatio = $amount_to_pay / $total_price;

        // Prepare items array for Maya
        $items = [];
        $calculatedTotal = 0;

        foreach ($bookingData['facilities'] as $facilityData) {
            $facility = Facility::find($facilityData['facility_id']);
            $facilityName = $facility->name ?? 'Unknown Facility';
            $facilityPrice = $facilityData['price'] ?? 0;

            // Calculate subtotal (price per night * nights)
            $facilitySubtotal = $facilityPrice * $nights;

            // Add breakfast if included
            $breakfastCost = 0;
            if ($isBreakfastIncluded) {
                $breakfastCost = $breakfastPrice * $nights;
                $facilitySubtotal += $breakfastCost;
            }

            // Calculate the prorated amount for this facility
            $facilityPaymentAmount = $facilitySubtotal * $paymentRatio;

            $calculatedTotal += $facilityPaymentAmount;

            $description = "Check-in: " . $checkin->format('M j, Y') .
                " | Check-out: " . $checkout->format('M j, Y') .
                " | Nights: " . $nights;

            if ($isBreakfastIncluded) {
                $description .= " | Breakfast: ₱" . number_format($breakfastCost, 2) . " (" . $nights . " mornings)";
            }

            // Add payment type info to description
            if ($amount_to_pay < $total_price) {
                $description .= " | 50% Deposit Payment";
            }

            $items[] = [
                'name' => $facilityName,
                'code' => 'FAC-' . ($facility->id ?? '000'),
                'description' => $description,
                'amount' => [
                    'value' => $facilityPaymentAmount,
                    'currency' => 'PHP',
                    'details' => [
                        "discount" => 0,
                        "serviceCharge" => 0,
                        "shippingFee" => 0,
                        "tax" => 0,
                        "subtotal" => $facilityPaymentAmount
                    ]
                ],
                'totalAmount' => [
                    'value' => $facilityPaymentAmount,
                    'currency' => 'PHP',
                    'details' => [
                        "discount" => 0,
                        "serviceCharge" => 0,
                        "shippingFee" => 0,
                        "tax" => 0,
                        "subtotal" => $facilityPaymentAmount
                    ]
                ]
            ];
        }
        // ✅ Get configuration
        $baseUrl = config('services.maya.base_url');
        $publicKey = config('services.maya.public_key');
        $secretKey = config('services.maya.secret_key');

        if (!$baseUrl || !$publicKey || !$secretKey) {
            return back()->with('error', 'Payment gateway configuration error. Please try again later.');
        }
        
        $rn = 'CLM-' . Str::upper(Str::random(8));

        $requestBody = [
            'totalAmount' => [
                'value' => $amount_to_pay, // Use the actual amount to pay
                'currency' => 'PHP',
                'details' => [
                    "discount" => 0,
                    "serviceCharge" => 0,
                    "shippingFee" => 0,
                    "tax" => 0,
                    "subtotal" => $amount_to_pay
                ]
            ],
            // 'buyer' => [
            //     'firstName' => $user_firstname,
            //     'lastName' => $user_lastname,
            //     'contact' => [
            //         'phone' => $user_phone,
            //         'email' => $user_email
            //     ],
            //     'billingAddress' => [
            //         'line1' => 'Not specified',
            //         'city' => 'Not specified',
            //         'state' => 'Not specified',
            //         'zipCode' => '0000',
            //         'countryCode' => 'PH'
            //     ]
            // ],
            'items' => $items,
            'redirectUrl' => [
                'success' => route('booking-awaiting'),
                'failure' => route('maya.checkout.failure', ['reason' => 'Failed', 'order' => $rn, 'token' => $token]),
                'cancel' => route('maya.checkout.failure', ['reason' => 'Cancelled', 'order' => $rn, 'token' => $token]),
            ],
            'requestReferenceNumber' => $rn,
        ];
        
        Log::debug('Maya Request Body:', $requestBody);

        try {
            $response = Http::withBasicAuth($publicKey, $secretKey)
                ->timeout(30)
                ->withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])
                ->post("{$baseUrl}/checkout/v1/checkouts", $requestBody);

            Log::debug('Maya API Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                Order::create([
                    'reference_number' => $rn,
                    'amount' => $amount_to_pay, // Store the actual amount being paid
                    'total_amount' => $total_price, // Store the full amount for reference
                    'status' => 'pending',
                    'token' => $token,
                    'payment_type' => $amount_to_pay < $total_price ? 'deposit' : 'full',
                ]);

                Log::info('Maya checkout created successfully', [
                    'reference_number' => $rn,
                    'checkout_id' => $responseData['checkoutId'] ?? null,
                    'redirect_url' => $responseData['redirectUrl'] ?? null,
                    'amount_paid' => $amount_to_pay,
                    'payment_type' => $amount_to_pay < $total_price ? 'deposit' : 'full'
                ]);

                return redirect()->away($responseData['redirectUrl']);
            } else {
                $statusCode = $response->status();
                $error = $response->json();

                Log::error('Maya API Error:', [
                    'status' => $statusCode,
                    'error' => $error,
                    'request_body' => $requestBody
                ]);

                $errorMessage = 'Failed to initialize checkout. ';
                if ($statusCode === 401) {
                    $errorMessage .= 'Authentication failed. Please check your API keys.';
                } elseif ($statusCode === 404) {
                    $errorMessage .= 'API endpoint not found.';
                } elseif ($statusCode === 422) {
                    $errorMessage .= 'Invalid request data. Please check your input.';
                } else {
                    $errorMessage .= 'Please try again.';
                }

                return back()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Maya Checkout Exception: ' . $e->getMessage());
            Log::error('Exception Trace: ' . $e->getTraceAsString());

            return back()->with('error', 'A network error occurred: ' . $e->getMessage());
        }
    }
    
    public function handleProcessing($token)
    {
        $order = Order::where('token', $token)->latest()->first();
        
        return view('customer_pages.maya.processing_payment', ['order' => $order, 'token' => $token]);
    }
    
    public function checkOrder($token)
    {
        $order = Order::where('token', $token)->latest()->first();

        if ($order) {
            return response()->json([
                'exists' => true,
                'status' => $order->status,
                'amount' => $order->amount,
                'reference' => $order->reference_number,
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function handleFailure($reason, $order, $token)
    {
        return view('customer_pages.maya.checkout_failure', [
            'reason' => $reason,
            'order' => $order,
            'token' => $token
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
