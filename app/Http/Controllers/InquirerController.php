<?php

namespace App\Http\Controllers;

use App\Models\FacilityBookingLog;
use App\Models\Payments;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationEmail;
use App\Mail\BookingRejectionEmail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class InquirerController extends Controller
{
    // ... existing methods ...
    
    /**
     * Confirm a booking and optionally send confirmation email
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmBooking(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'custom_message' => 'nullable|string|max:500'
            ]);
    
            $booking = FacilityBookingLog::with(['user', 'details'])->findOrFail($id);
            $referenceNumber = '#CLM' . date('Y') . '-' . $booking->id;
            // Check if booking is already confirmed
            if ($booking->status === 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is already confirmed.'
                ], 400);
            }
    
            $updateData = [
                'status' => 'confirmed',
                'confirmed_at' => Carbon::now(),
                'confirmation_token' => Str::random(60),
                'reference' => $referenceNumber,
                'code' => $this->reservationCode()
            ];
    
            // Start a database transaction
            DB::beginTransaction();
    
            try {
                // Update booking status
                $booking->update($updateData);
            
                $total_amount = $booking->details->sum('total_price');
        
                // Create payment record
                Payments::create([
                    'facility_log_id' => $booking->id,
                    'status' => 'Pending',
                    'amount' => (0.5 * $total_amount)
                ]);
    
                // Commit the transaction
                DB::commit();
    
                // Send confirmation email
                $this->sendConfirmationEmail($booking, $validated['custom_message'] ?? null);
    
                Log::info("Booking confirmed, payment created, and email sent", [
                    'booking_id' => $id, 
                    'user_id' => auth()->id()
                ]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Booking has been confirmed, payment record created, and confirmation email sent.',
                    'data' => $booking->fresh()
                ]);
    
            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollBack();
                throw $e;
            }
    
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Booking confirmation failed: " . $e->getMessage(), [
                'booking_id' => $id,
                'exception' => $e
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reservationCode()
    {
        $now = Carbon::now();
        
        return strtoupper(
            'CM' // Prefix
            . $now->format('y')  // Year (e.g. 25 for 2025)
            . $now->format('m')  // Month (e.g. 08)
            . $now->format('d')  // Day (e.g. 19)
            . $now->format('H')  // Hour (24h format)
            . $now->format('i')  // Minute
            . 'AA'               // Predefined separator
            . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT)// Six random digits
        );
    }
    
    /**
     * Send booking confirmation email
     *
     * @param FacilityBookingLog $booking
     * @param string|null $customMessage
     * @throws \Exception
     */
    protected function sendConfirmationEmail(FacilityBookingLog $booking, ?string $customMessage = null): void
    {
        if (!$booking->user) {
            throw new \Exception('No user associated with this booking');
        }
        
        if (empty($booking->user->email)) {
            throw new \Exception('User does not have an email address');
        }
        
        $verificationUrl = route('booking.verify', [
            'token' => $booking->confirmation_token
        ]);

        try {
            Mail::to($booking->user->email)->send(
                new BookingConfirmationEmail($booking, $verificationUrl, $customMessage)
            );
            
            Log::info("Confirmation email sent", [
                'booking_id' => $booking->id,
                'email' => $booking->user->email
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send confirmation email", [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verify booking using confirmation token
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyBooking(Request $request, $token)
    {
        try {
            $booking = FacilityBookingLog::where('confirmation_token', $token)
                ->whereNotNull('confirmation_token')
                ->firstOrFail();
            
            $booking->update([
                'confirmation_token' => null,
                'verified_at' => Carbon::now(),
            ]);

            Log::info("Booking verified via email", ['booking_id' => $booking->id]);

            return redirect()->route('booking.redirect', [
                'booking' => $booking->id
            ]);

        } catch (\Exception $e) {
            Log::warning("Invalid booking verification attempt", ['token' => $token]);
            
            return redirect()->route('invalid_link');
        }
    }
    
    
    public function rejectBooking(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'custom_message' => 'nullable|string|max:500'
            ]);
    
            $booking = FacilityBookingLog::with(['user', 'details'])->findOrFail($id);
    
            // Check if booking is already rejected
            if ($booking->status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is already rejected.'
                ], 400);
            }
    
            // Check if booking is already confirmed
            if ($booking->status === 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reject a confirmed booking.'
                ], 400);
            }
    
            $updateData = [
                'status' => 'rejected',
                'rejected_at' => Carbon::now(),
                'rejection_reason' => $validated['custom_message'] ?? null
            ];
    
            // Start a database transaction
            DB::beginTransaction();
    
            try {
                // Update booking status
                $booking->update($updateData);
            
                // Commit the transaction
                DB::commit();
    
                // Send rejection email
                $this->sendRejectionEmail($booking, $validated['custom_message'] ?? null);
    
                Log::info("Booking rejected and email sent", [
                    'booking_id' => $id, 
                    'user_id' => auth()->id()
                ]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Booking has been rejected and notification email sent.',
                    'data' => $booking->fresh()
                ]);
    
            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollBack();
                throw $e;
            }
    
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Booking rejection failed: " . $e->getMessage(), [
                'booking_id' => $id,
                'exception' => $e
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject booking: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send booking rejection email
     *
     * @param FacilityBookingLog $booking
     * @param string|null $customMessage
     * @throws \Exception
     */
    protected function sendRejectionEmail(FacilityBookingLog $booking, ?string $customMessage = null): void
    {
        if (!$booking->user) {
            throw new \Exception('No user associated with this booking');
        }
    
        if (empty($booking->user->email)) {
            throw new \Exception('User does not have an email address');
        }
    
        try {
            Mail::to($booking->user->email)->send(
                new BookingRejectionEmail($booking, $customMessage)
            );
    
            Log::info("Rejection email sent", [
                'booking_id' => $booking->id,
                'email' => $booking->user->email
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send rejection email", [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    // =======================
    // Trying ajax entry in Inquirer at admin index
    // =======================
    public function getInquiries(Request $request)
    {
        $search = $request->input('search', '');
        $requestStatus = $request->input('request_status', '');
        $paymentStatus = $request->input('payment_status', '');
        $perPage = $request->input('per_page', 20);
    
        $query = FacilityBookingLog::with([
                'user', 
                'summaries.facility', 
                'summaries.breakfast',
                'details',
                'payments',
            ])
            ->when($search, function($query) use ($search) {
                // Check if search is numeric (likely an ID search)
                if (is_numeric($search)) {
                    $query->where('id', $search);
                } else {
                    $query->whereHas('user', function($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhere('reference', 'like', "%{$search}%");
                }
            })
            ->when($requestStatus, function($query) use ($requestStatus) {
                $query->where('status', $requestStatus);
            })
            ->when($paymentStatus, function($query) use ($paymentStatus) {
                $query->whereHas('payments', function($q) use ($paymentStatus) {
                    $q->where('status', $paymentStatus);
                });
            })
            ->orderBy('created_at', 'desc');
    
        $bookings = $query->paginate($perPage);
        $newCount = FacilityBookingLog::where('is_read', false)->count();
        
        // Count pending payments (assuming this means bookings with pending payment status)
        $pendingPayments = FacilityBookingLog::whereHas('payments', function($q) {
            $q->where('status', 'pending');
        })->count();
        
        $total_PaymentUnderVerification = Payments::where('status', 'under_verification')->count();
        
        $summary = [
            'total' => FacilityBookingLog::count(),
            'payment_under_verification' => $total_PaymentUnderVerification,
            'pending_requests' => FacilityBookingLog::where('status', 'pending_confirmation')->count(),
            'cancellations' => FacilityBookingLog::where('status', 'cancelled')->count(),
            'confirmed_requests' => FacilityBookingLog::where('status', 'confirmed')->count(),
            'rejected_request' => FacilityBookingLog::where('status', 'rejected')->count(),
        ];
        
        
        return response()->json([
            'success' => true,
            'inquiries' => $bookings->items(),
            'summary' => $summary,
            'pagination' => [
                'total' => $bookings->total(),
                'per_page' => $bookings->perPage(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'from' => $bookings->firstItem(),
                'to' => $bookings->lastItem()
            ],
            'newCount' => $newCount
        ]);
    }
    
    
    public function markAsRead($id)
    {
        $inquiry = FacilityBookingLog::findOrFail($id);
        $inquiry->update(['is_read' => true]);
        
        $newCount = FacilityBookingLog::where('is_read', false)->count();
        
        return response()->json([
            'success' => true,
            'newCount' => $newCount
        ]);
    }


    public function markAllAsRead()
    {
        FacilityBookingLog::where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'newCount' => 0
        ]);
    }
    
    public function getBookingDetails($id)
    {
        $inquiry = FacilityBookingLog::with([
                'user:id,firstname,lastname,phone,email',
                'summaries.facility:id,name,room_number,bed_number,price,category',
                'summaries.breakfast:id,price',
                'summaries.bookingDetails',
                'guestDetails.guestType'
            ])->findOrFail($id);
        
        $formattedData = $this->formatInquiryData($inquiry);
        
        // Temporary logging - remove after debugging
        \Log::debug('Booking Details Response:', [
            'id' => $id,
            'data_structure' => $formattedData,
            'has_user' => isset($formattedData['user']),
            'has_facilities' => isset($formattedData['facilities']),
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $formattedData
        ]);
    }
    
    protected function formatInquiryData($inquiry)
    {
        $totalPrice = $inquiry->summaries->sum(function($summary) {
            $detail = $summary->bookingDetails->first();
            return $detail ? $detail->total_price : 0;
        });
        
        // Calculate nights for the first facility (assuming all have same dates)
        $firstDetail = $inquiry->summaries->first()->bookingDetails->first() ?? null;
        $nights = 0;
        if ($firstDetail && $firstDetail->checkin_date && $firstDetail->checkout_date) {
            $checkin = \Carbon\Carbon::parse($firstDetail->checkin_date);
            $checkout = \Carbon\Carbon::parse($firstDetail->checkout_date);
            $nights = $checkin->diffInDays($checkout);
            
        }
        $arrivingTime = $inquiry->bookingDetails->first()->arriving_time ?? null;

        return [
            'id' => $inquiry->id,
            'reference' => $inquiry->reference,
            'status' => $inquiry->status,
            'total_price' => $totalPrice,
            'nights' => $nights,
            'arriving_time' => $arrivingTime,
            'user' => [
                'firstname' => $inquiry->user->firstname ?? null,
                'lastname' => $inquiry->user->lastname ?? null,
                'email' => $inquiry->user->email ?? null,
                'phone' => $inquiry->user->phone ?? null
            ],
            'facilities' => $inquiry->summaries->map(function($summary) use ($inquiry) {
                $detail = $summary->bookingDetails->first() ?? new \stdClass();
                
                // Get ALL guest details for this facility
                $guestDetails = $inquiry->guestDetails
                    ->where('facility_id', $summary->facility_id)
                    ->map(function($detail) {
                        return [
                            'type' => $detail->guestType->type ?? 'Unknown',
                            'quantity' => $detail->quantity
                        ];
                    })->values()->all(); // Ensure we get a clean array
                
                return [
                    'facility_id' => $summary->facility_id,
                    'check_in' => $detail->checkin_date ?? null,
                    'check_out' => $detail->checkout_date ?? null,
                    'total_price' => $detail->total_price ?? 0,
                    'facility_name' => $summary->facility->name ?? 'N/A',
                    'facility_category' => $summary->facility->category ?? 'N/A',
                    'breakfast' => $summary->breakfast ? 'Included' : 'None',
                    'breakfast_price' => $summary->breakfast->price ?? 0,
                    'guest_details' => $guestDetails, // Now guaranteed to be an array
                    'room_info' => [
                        'room_number' => $summary->facility->room_number ?? 'N/A',
                        'room_type' => $summary->facility->category ?? 'N/A',
                        'price_per_night' => $summary->facility->price ?? 0,
                        'bed_number' => $summary->facility->bed_number ?? 'N/A'
                    ]
                ];
            })->values()->all() // Ensure facilities is a clean array
        ];
    }
    
    public function getPaymentDetails($id)
    {
        try {
            $inquiry = FacilityBookingLog::with(['payments'])->findOrFail($id);
            
            if (!$inquiry->payments) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payment found for this inquiry'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $this->formatInquiryPayment($inquiry)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment details'
            ], 500);
        }
    }
    
    public function verifyPayment($id)
    {
        try {
            $inquiry = FacilityBookingLog::with(['payments'])->findOrFail($id);
            
            if (!$inquiry->payments) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payment found to verify'
                ], 404);
            }
            
            // Update payment status
            $inquiry->payments->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment successfully verified',
                'data' => $this->formatInquiryPayment($inquiry)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment'
            ], 500);
        }
    }
    
    protected function formatInquiryPayment($inquiry)
    {
        $payment = $inquiry->payments;
        
        return [
            'id' => $inquiry->id,
            'amount' => $payment->amount,
            'amount_lacking' => (0.5 * $payment->amount),
            'gcash_number' => $payment->gcash_number,
            'reference_number' => $payment->reference_no,
            'receipt_image' => $payment->receipt_path,
            'is_verified' => $payment->status === 'verified',
            'payment_method' => $payment->payment_method
        ];
    }
    
    // public function streamUpdates(Request $request)
    // {
    //     header('Content-Type: text/event-stream');
    //     header('Cache-Control: no-cache');
    //     header('Connection: keep-alive');
    //     header('X-Accel-Buffering: no'); // Disable buffering for Nginx
    
    //     $lastEventId = $request->header('Last-Event-ID');
    //     $token = $request->input('token');
    
    //     // Verify CSRF token if needed
    //     if (!hash_equals($request->input('token'), csrf_token())) {
    //         return response('Unauthorized', 401);
    //     }
    
    //     $response = new StreamedResponse(function() use ($lastEventId) {
    //         $lastCheck = now()->subSeconds(5);
            
    //         while (true) {
    //             try {
    //                 // Get new bookings since last check
    //                 $newBookings = FacilityBookingLog::where('created_at', '>', $lastCheck)
    //                     ->where('is_read', false)
    //                     ->where('status', 'pending_confirmation')
    //                     ->get();
                    
    //                 // Get updated bookings
    //                 $updatedBookings = FacilityBookingLog::where('updated_at', '>', $lastCheck)
    //                     ->where('created_at', '<', $lastCheck)
    //                     ->get()
    //                     ->map(function($booking) {
    //                         $booking->status_updated = $booking->wasChanged('status');
    //                         return $booking;
    //                     });
    
    //                 $lastCheck = now();
                    
    //                 if ($newBookings->isNotEmpty() || $updatedBookings->isNotEmpty()) {
    //                     $data = [
    //                         'type' => 'booking_update',
    //                         'bookings' => [
    //                             'new' => $newBookings,
    //                             'updated' => $updatedBookings
    //                         ],
    //                         'time' => now()->toDateTimeString()
    //                     ];
                        
    //                     echo "data: " . json_encode($data) . "\n\n";
    //                     ob_flush();
    //                     flush();
    //                 }
                    
    //                 // Break if client disconnected
    //                 if (connection_aborted()) {
    //                     break;
    //                 }
                    
    //                 sleep(5); // Check every 5 seconds
    //             } catch (\Exception $e) {
    //                 // Log error and retry
    //                 Log::error('SSE Error: ' . $e->getMessage());
    //                 sleep(1);
    //             }
    //         }
    //     });
    
    //     return $response;
    // }
    
    // public function updateGuestInfo($id, Request $request)
    // {
    //     try {
    //         $booking = Booking::findOrFail($id);
    //         $user = $booking->user;
            
    //         $validated = $request->validate([
    //             'firstname' => 'required|string|max:255',
    //             'lastname' => 'required|string|max:255',
    //             'email' => 'required|email|max:255',
    //             'phone' => 'required|string|max:20',
    //         ]);
            
    //         $user->update($validated);
            
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Guest information updated successfully'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    // public function updateBreakfast($id, Request $request)
    // {
    //     try {
    //         $booking = Booking::findOrFail($id);
            
    //         $validated = $request->validate([
    //             'breakfast' => 'required|string|in:None,Yes,No',
    //         ]);
            
    //         // Update breakfast option for all facilities in this booking
    //         $booking->facilities()->update(['breakfast' => $validated['breakfast']]);
            
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Breakfast option updated successfully'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function updateFacility($bookingId, $facilityId, Request $request)
    // {
    //     try {
    //         $booking = Booking::findOrFail($bookingId);
    //         $facility = $booking->facilities()->findOrFail($facilityId);
            
    //         $validated = $request->validate([
    //             'facility_name' => 'sometimes|string|max:255',
    //             'room_number' => 'sometimes|string|max:50',
    //             'check_in' => 'sometimes|date',
    //             'check_out' => 'sometimes|date|after:check_in',
    //             'pax' => 'sometimes|integer|min:1',
    //             'bed_number' => 'sometimes|string|max:50',
    //             'price_per_night' => 'sometimes|numeric|min:0',
    //             'room_type' => 'sometimes|string|max:100'
    //         ]);
            
    //         // Update facility information
    //         $facility->update([
    //             'facility_name' => $validated['facility_name'] ?? $facility->facility_name,
    //             'pax' => $validated['pax'] ?? $facility->pax,
    //             'bed_number' => $validated['bed_number'] ?? $facility->bed_number,
    //             'check_in' => $validated['check_in'] ?? $facility->check_in,
    //             'check_out' => $validated['check_out'] ?? $facility->check_out,
    //         ]);
            
    //         // Update room info if it exists
    //         if ($facility->room_info) {
    //             $roomInfo = $facility->room_info;
    //             $roomInfo['room_number'] = $validated['room_number'] ?? $roomInfo['room_number'];
    //             $roomInfo['price_per_night'] = $validated['price_per_night'] ?? $roomInfo['price_per_night'];
    //             $roomInfo['room_type'] = $validated['room_type'] ?? $roomInfo['room_type'];
    //             $facility->room_info = $roomInfo;
    //             $facility->save();
    //         }
            
    //         // Recalculate total price
    //         $this->recalculateBookingTotal($booking);
            
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Facility updated successfully'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function removeFacility($bookingId, $facilityId)
    // {
    //     try {
    //         $booking = Booking::findOrFail($bookingId);
    //         $facility = $booking->facilities()->findOrFail($facilityId);
            
    //         $facility->delete();
            
    //         // Recalculate total price
    //         $this->recalculateBookingTotal($booking);
            
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Facility removed successfully'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function addRoomToBooking($bookingId, Request $request)
    // {
    //     try {
    //         $booking = Booking::findOrFail($bookingId);
    //         $validated = $request->validate([
    //             'room_id' => 'required|exists:rooms,id'
    //         ]);
            
    //         $room = Room::findOrFail($validated['room_id']);
            
    //         // Check if room is already in this booking
    //         if ($booking->facilities()->where('room_id', $room->id)->exists()) {
    //             throw new \Exception('This room is already part of the booking');
    //         }
            
    //         // Check if room is available for booking dates
    //         $firstFacility = $booking->facilities()->first();
    //         $isAvailable = Room::where('id', $room->id)
    //             ->whereDoesntHave('facilities', function($query) use ($firstFacility) {
    //                 $query->where(function($q) use ($firstFacility) {
    //                     $q->whereBetween('check_in', [$firstFacility->check_in, $firstFacility->check_out])
    //                       ->orWhereBetween('check_out', [$firstFacility->check_in, $firstFacility->check_out])
    //                       ->orWhere(function($q) use ($firstFacility) {
    //                           $q->where('check_in', '<=', $firstFacility->check_in)
    //                             ->where('check_out', '>=', $firstFacility->check_out);
    //                       });
    //                 });
    //             })
    //             ->exists();
            
    //         if (!$isAvailable) {
    //             throw new \Exception('This room is not available for the selected dates');
    //         }
            
    //         // Calculate number of nights
    //         $checkIn = new \DateTime($firstFacility->check_in);
    //         $checkOut = new \DateTime($firstFacility->check_out);
    //         $nights = $checkIn->diff($checkOut)->days;
            
    //         // Create new facility for this room
    //         $facility = new Facility([
    //             'booking_id' => $booking->id,
    //             'room_id' => $room->id,s
    //             'facility_name' => $room->name,
    //             'category' => $room->category,
    //             'pax' => $room->pax,
    //             'bed_number' => $room->bed_number,
    //             'check_in' => $firstFacility->check_in,
    //             'check_out' => $firstFacility->check_out,
    //             'price_per_night' => $room->price_per_night,
    //             'total_price' => $room->price_per_night * $nights,
    //             'room_type' => $room->type,
    //             'status' => 'confirmed'
    //         ]);
            
    //         $facility->save();
            
    //         // Recalculate total price for the booking
    //         $this->recalculateBookingTotal($booking);
            
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Room added to booking successfully'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // private function recalculateBookingTotal($booking)
    // {
    //     $totalPrice = $booking->facilities()->sum('total_price');
    //     $booking->total_price = $totalPrice;
    //     $booking->save();
    // }
    
}