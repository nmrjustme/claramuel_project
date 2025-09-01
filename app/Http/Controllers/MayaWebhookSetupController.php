<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MayaWebhookSetupController extends Controller
{
    /**
     * Register webhook with Maya
     */
    public function registerWebhook()
    {
        $baseUrl = config('services.maya.base_url');
        $secretKey = config('services.maya.secret_key');
        $webhookUrl = config('app.url') . '/webhook/maya';

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$baseUrl}/payments/v1/webhooks", [
                    'name' => 'AUTHORIZED',
                    'callbackUrl' => $webhookUrl
                ]);

            if ($response->successful()) {
                $webhookData = $response->json();
                Log::info('Maya Webhook Registered Successfully:', $webhookData);

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook registered successfully',
                    'data' => $webhookData
                ]);
            } else {
                Log::error('Maya Webhook Registration Failed:', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Webhook registration failed',
                    'details' => $response->json()
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Maya Webhook Registration Exception: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List registered webhooks
     */
    public function listWebhooks()
    {
        $baseUrl = config('services.maya.base_url');
        $secretKey = config('services.maya.secret_key');

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->withHeaders(['Accept' => 'application/json'])
                ->get("{$baseUrl}/payments/v1/webhooks");

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch webhooks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a webhook
     */
    public function deleteWebhook($webhookId)
    {
        $baseUrl = config('services.maya.base_url');
        $secretKey = config('services.maya.secret_key');

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->withHeaders(['Accept' => 'application/json'])
                ->delete("{$baseUrl}/payments/v1/webhooks/{$webhookId}");

            if ($response->successful()) {
                Log::info("Maya Webhook {$webhookId} deleted successfully");
                return response()->json(['success' => true]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete webhook: ' . $e->getMessage()
            ], 500);
        }
    }
}
