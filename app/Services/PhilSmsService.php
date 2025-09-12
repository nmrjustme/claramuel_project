<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsService
{
      protected $apiUrl = "https://app.philsms.com/api/v3/sms/send";
      protected $token;
      protected $sender;


      public function __construct()
      {
            $this->token = config('services.philsms.token');
            $this->sender = config('services.philsms.sender');

            // Log the configuration for debugging
            Log::info('PhilSMS Config:', [
                  'token' => $this->token ? 'Set' : 'Missing',
                  'sender' => $this->sender,
                  'token_length' => strlen($this->token ?? '')
            ]);
      }

      public function send(string $recipient, string $message): array
      {
            try {
                  Log::info('Sending SMS via PhilSMS:', [
                        'recipient' => $recipient,
                        'sender_id' => $this->sender,
                        'message_length' => strlen($message)
                  ]);

                  $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                  ])->timeout(30) // Add timeout
                        ->post($this->apiUrl, [
                              'recipient' => $recipient,
                              'sender_id' => $this->sender,
                              'type'      => 'plain',
                              'message'   => $message,
                        ]);

                  $responseData = $response->json();
                  $statusCode = $response->status();

                  Log::info('PhilSMS API Response:', [
                        'status_code' => $statusCode,
                        'response' => $responseData
                  ]);

                  if ($response->successful()) {
                        return [
                              'success' => true,
                              'status' => 'success',
                              'data' => $responseData
                        ];
                  }

                  return [
                        'success' => false,
                        'status' => 'error',
                        'error' => $responseData['message'] ?? 'Unknown API error',
                        'code' => $statusCode,
                        'response' => $responseData
                  ];
            } catch (\Exception $e) {
                  Log::error('PhilSMS Exception: ' . $e->getMessage());

                  return [
                        'success' => false,
                        'status' => 'exception',
                        'error' => $e->getMessage()
                  ];
            }
      }
}
