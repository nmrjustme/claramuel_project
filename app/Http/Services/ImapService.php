<?php

// app/Http/Services/EmailMonitorService.php
namespace App\Http\Services;

use Webklex\IMAP\Facades\Client;
use App\Models\EmailLog;
use App\Models\BookingReplies;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;

class ImapService
{
      protected $client;

      public function __construct()
      {
            $this->client = Client::account('default');
      }

      /**
       * Fetch emails from IMAP and store relevant ones
       *
       * @param int $bookingId The booking ID to associate replies with
       * @return void
       */
      public function fetchAndStoreEmails($bookingId)
      {
            try {
                  // Connect to the IMAP server
                  $this->client->connect();

                  // Get the inbox folder
                  $folder = $this->client->getFolder('INBOX');

                  // Get all unseen messages
                  $messages = $folder->query()->unseen()->get();

                  foreach ($messages as $message) {
                        // Extract relevant information
                        $fromEmail = $message->from[0]->mail;
                        $fromName = $message->from[0]->personal ?? $fromEmail;
                        $content = $message->getTextBody();

                        // Store in database
                        BookingReplies::create([
                              'booking_id' => $bookingId,
                              'from_email' => $fromEmail,
                              'from_name' => $fromName,
                              'message' => $content,
                              'is_read' => false
                        ]);

                        // Mark as read (optional)
                        $message->setFlag('SEEN');
                  }
            } catch (ConnectionFailedException $e) {
                  // Handle connection errors
                  \Log::error('IMAP Connection Failed: ' . $e->getMessage());
            }
      }
}
