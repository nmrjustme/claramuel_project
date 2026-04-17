<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Log;

class BookingReplyController extends Controller
{
    public function index() 
    {
        return view('admin.booking_replies.index');
    }
    
    private function account() 
    {
        return Client::account('default');
    }

    /**
     * Fetch inbox messages with attachment details
     */
    public function fetchInbox(Request $request)
    {
        try {
            $limit = $request->input('limit', 20);
            
            $client = $this->account()->connect();
            $folder = $client->getFolder('INBOX');
            $messages = $folder->query()->all()->limit($limit)->get();

            // Sort messages by newest first
            $messages = $messages->sortByDesc(function ($message) {
                $dateAttr = $message->getDate();
                $dateObj  = $dateAttr instanceof \Webklex\PHPIMAP\Attribute ? $dateAttr->first() : $dateAttr;
                return $dateObj ? $dateObj->getTimestamp() : 0;
            });
            
            $emails = [];
            foreach ($messages as $message) {
                try {
                    $from     = $message->getFrom();
                    $fromObj  = $from ? $from->first() : null;
                    
                    $dateAttr = $message->getDate();
                    $dateObj  = $dateAttr instanceof \Webklex\PHPIMAP\Attribute ? $dateAttr->first() : $dateAttr;
                    $date     = $dateObj ? $dateObj->format('Y-m-d H:i:s') : null;
                    
                    $body = $message->getTextBody();
                    if (empty($body)) {
                        $htmlBody = $message->getHTMLBody();
                        if ($htmlBody) {
                            $body = strip_tags($htmlBody);
                            $body = html_entity_decode($body);
                            $body = preg_replace('/\s+/', ' ', $body);
                        }
                    }
                    
                    $flags  = $message->getFlags();
                    $isRead = $flags->contains('Seen');

                    // Handle attachments
                    $attachments = [];
                    if ($message->hasAttachments()) {
                        foreach ($message->getAttachments() as $attachment) {
                            try {
                                $attachments[] = [
                                    'id'           => $attachment->id,
                                    'name'         => $attachment->name ?? 'unnamed',
                                    'size'         => $attachment->size ?? 0,
                                    'mime'         => $attachment->content_type ?? 'application/octet-stream',
                                    'download_url' => route('emails.download.attachment', [
                                        'email_id'      => $message->getUid(),
                                        'attachment_id' => $attachment->id,
                                    ]),
                                ];
                            } catch (\Exception $e) {
                                Log::warning('Attachment error: '.$e->getMessage());
                            }
                        }
                    }
                    
                    $emails[] = [
                        'id'             => $message->getUid(),
                        'from'           => $fromObj ? $fromObj->mail : 'unknown',
                        'from_name'      => $fromObj ? $fromObj->personal : '',
                        'subject'        => $message->getSubject() ?? '(No Subject)',
                        'date'           => $date,
                        'body'           => $body ?: '(No body content)',
                        'is_read'        => $isRead,
                        'has_attachments'=> $message->hasAttachments(),
                        'attachments'    => $attachments,
                    ];
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            return response()->json([
                'success'      => true,
                'emails'       => $emails,
                'unread_count' => count(array_filter($emails, fn($email) => !$email['is_read'])),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email server connection failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Mark email as read
     */
    public function markAsRead(Request $request)
    {
        try {
            $emailId = $request->input('email_id');
            if (!$emailId) {
                return response()->json(['success' => false, 'message' => 'Email ID required'], 400);
            }
            
            $client = $this->account()->connect();
            $folder = $client->getFolder('INBOX');
            $message = $folder->messages()->getMessageByUid($emailId);
            
            if ($message) {
                $message->setFlag('Seen');
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'Email not found'], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark email as read',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download an attachment
     */
    public function downloadAttachment($email_id, $attachment_id)
    {
        try {
            $client  = $this->account()->connect();
            $folder  = $client->getFolder('INBOX');
            $message = $folder->messages()->getMessageByUid($email_id);

            if (!$message) {
                return abort(404, 'Email not found');
            }

            foreach ($message->getAttachments() as $attachment) {
                if ($attachment->id == $attachment_id) {
                    $filename = $attachment->name ?? 'attachment.dat';
                    return response($attachment->content, 200, [
                        'Content-Type'        => $attachment->content_type,
                        'Content-Disposition' => "attachment; filename=\"$filename\"",
                    ]);
                }
            }

            return abort(404, 'Attachment not found');
        } catch (\Exception $e) {;
            return abort(500, 'Failed to download attachment');
        }
    }
}
