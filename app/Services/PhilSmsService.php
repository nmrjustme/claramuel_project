<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PhilSmsService
{
      protected $apiUrl = "https://app.philsms.com/api/v3/sms/send";
      protected $token;
      protected $sender;

      public function __construct()
      {
            $this->token = config('services.philsms.token');
            $this->sender = config('services.philsms.sender');
      }

      public function send(string $recipient, string $message): array
      {
            $response = Http::withHeaders([
                  'Authorization' => 'Bearer ' . $this->token,
                  'Accept'        => 'application/json',
                  'Content-Type'  => 'application/json',
            ])->post($this->apiUrl, [
                  'recipient' => $recipient,
                  'sender_id' => $this->sender,
                  'type'      => 'plain',
                  'message'   => $message,
            ]);

            return $response->json();
      }
}
