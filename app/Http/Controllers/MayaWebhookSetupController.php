<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Breakfast;
use App\Models\Facility;
use App\Models\FacilitySummary;
use App\Models\FacilityBookingDetails;
use App\Models\BookingGuestDetails;
use App\Events\BookingNew;
use App\Models\FacilityBookingLog;
use App\Models\Payments;

class MayaWebhookSetupController extends Controller
{
    
    public function handle(Request $request)
    {
        // Log full payload for debugging
        Log::info('Maya Webhook Received:', $request->all());

        // Get event type
        $event = $request->status;

        switch ($event) {
            case 'PAYMENT_SUCCESS':
                $this->updateOrder('paid', $request);
                break;
            case 'PAYMENT_FAILED':
                $this->updateOrder('failed', $request);
                break;
            case 'PAYMENT_EXPIRED':
                $this->updateOrder('expired', $request);
                break;
            case 'PAYMENT_CANCELLED':
                $this->updateOrder('cancelled', $request);
                break;
            default:
                Log::info("Maya Webhook: Event {$event} ignored");
                return response()->json(['message' => "Event {$event} ignored"], 200);
        }
    }
    
    public function updateOrder($status, $request)
    {
        $orderId = $request->input('requestReferenceNumber');
        
        // Find order by reference number and mark as paid
        $order = Order::where('reference_number', $orderId)->first();
        $token = Order::where('reference_number', $orderId)->value('token');
        
        $order->status = $status;
        $order->save();

        if ($status === 'paid') {
            $this->storeBookingInDatabase($token, $orderId);
        }
        
        Log::info("Order {$orderId} marked as " . strtoupper($status));
        return response()->json(['message' => "Order {$orderId} updated to {$status}"], 200);
    }

    private function storeBookingInDatabase($token, $orderId)
    {
        $bookingData = Cache::get('booking_confirmation_' . $token);
        // Start database transaction
        DB::beginTransaction();

        try {
            // Parse dates explicitly with timezone
            $timezone = config('app.timezone', 'Asia/Manila');
            $checkinDate = Carbon::parse($bookingData['checkin_date'], $timezone)
                ->setTimezone('Asia/Manila')
                ->startOfDay();
            $checkoutDate = Carbon::parse($bookingData['checkout_date'], $timezone)
                ->setTimezone('Asia/Manila')
                ->startOfDay();

            // Verify dates after parsing
            if ($checkinDate >= $checkoutDate) {
                throw new \Exception('Check-out date must be after check-in date');
            }

            // Validate guest counts match facility pax limits
            foreach ($bookingData['facilities'] as $facilityData) {
                $facilityId = $facilityData['facility_id'];
                $facility = Facility::findOrFail($facilityId);

                $totalGuests = 0;
                if (isset($bookingData['guest_types'][$facilityId])) {
                    $totalGuests = array_sum($bookingData['guest_types'][$facilityId]);
                }

                if ($totalGuests > $facility->pax) {
                    throw new \Exception("Facility {$facility->name} exceeds maximum guest limit of {$facility->pax}");
                }
            }

            // Create user (always insert new, no update)
            $user = User::create([
                'email' => $bookingData['email'],
                'firstname' => $bookingData['firstname'],
                'lastname' => $bookingData['lastname'],
                'phone' => $bookingData['phone'],
                'role' => 'customer',
            ]);

            // Create booking log with confirmation token
            $bookingLog = FacilityBookingLog::create([
                'user_id' => $user->id,
                'booking_date' => now(),
                'code' => $bookingData['reservation_code']
            ]);

            Log::info('Booking confirmed via email', [
                'booking_id' => $bookingLog->id,
                'user_id' => $user->id,
                'email' => $bookingData['email']
            ]);

            // Get active breakfast price if included
            $breakfastId = null;
            $breakfastPricePerFacilityPerDay = 0;

            if ($bookingData['breakfast_included']) {
                $breakfast = Breakfast::where('status', 'Active')->first();
                if ($breakfast) {
                    $breakfastId = $breakfast->id;
                    $breakfastPricePerFacilityPerDay = $breakfast->price;
                }
            }

            // Process each facility
            foreach ($bookingData['facilities'] as $facilityData) {
                $facility = Facility::findOrFail($facilityData['facility_id']);

                // Create facility summary
                $facilitySummary = FacilitySummary::create([
                    'facility_id' => $facility->id,
                    'facility_price' => $facility->price,
                    'breakfast_id' => $breakfastId,
                    'breakfast_price' => $breakfastPricePerFacilityPerDay,
                    'facility_booking_log_id' => $bookingLog->id,
                ]);

                // Calculate breakfast cost for this facility (per facility per night)
                $breakfastCost = 0;
                if ($bookingData['breakfast_included']) {
                    $breakfastCost = $breakfastPricePerFacilityPerDay * $facilityData['nights'];
                }

                // Calculate total price for this facility (room + breakfast)
                $facilityTotalPrice = $facilityData['total_price'] + $breakfastCost;

                // Create booking details
                FacilityBookingDetails::create([
                    'facility_summary_id' => $facilitySummary->id,
                    'facility_booking_log_id' => $bookingLog->id,
                    'checkin_date' => $checkinDate->format('Y-m-d'),
                    'checkout_date' => $checkoutDate->format('Y-m-d'),
                    'total_price' => $facilityTotalPrice,
                    'breakfast_cost' => $breakfastCost,
                ]);
                
                $total_price = $bookingData['total_price'] ?? 0;
                $half_of_total_price = ($total_price * 0.5);
                // Create payment record
                Payments::create([
                    'facility_log_id' => $bookingLog->id,
                    'status' => 'paid',
                    'amount' => $half_of_total_price,
                    'reference_no' => $orderId,
                    'payment_date' => now(),
                    'amount_paid' => $half_of_total_price,
                ]);
                
                // Process guest types for this facility
                if (isset($bookingData['guest_types'][$facility->id])) {
                    foreach ($bookingData['guest_types'][$facility->id] as $guestTypeId => $quantity) {
                        if ($quantity > 0) {
                            BookingGuestDetails::create([
                                'guest_type_id' => $guestTypeId,
                                'facility_booking_log_id' => $bookingLog->id,
                                'facility_id' => $facility->id,
                                'quantity' => $quantity
                            ]);
                        }
                    }
                }
            }
            
            // Commit transaction
            DB::commit();

            $bookingLog->load(['user']);

            event(new BookingNew($bookingLog)); // Event listener for new booking list
            
            // Sending active admin email
            // if (User::where('role', 'Admin')->where('is_active', true)->exists()) {
            //     $this->sendEmailAdmin($bookingLog); 
            // }

            Log::info('booking recorded successfully');

            Cache::forget('booking_confirmation_' . $token);
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Email confirmation booking failed:', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'booking_data' => $bookingData
            ]);

            throw $e; // Re-throw to handle in the controller
        }
    }
}
