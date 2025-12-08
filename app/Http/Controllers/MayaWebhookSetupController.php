<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\Models\FacilityDiscount;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationReceived;
use App\Mail\PaymentFailed;
use Illuminate\Support\Facades\Log;
use App\Services\InvoiceService;
use App\Mail\AdminNotification;
use App\Models\RoomHold;

class MayaWebhookSetupController extends Controller
{

    public function handle(Request $request)
    {
        Log::info('Maya Webhook Received:', $request->all());

        // Some payloads use "paymentStatus", some only "status"
        $event = $request->input('paymentStatus') ?? $request->input('status');

        switch ($event) {
            case 'PAYMENT_SUCCESS':
            case 'CHECKOUT_SUCCESS':
                return $this->updateOrder('paid', $request);

            case 'PAYMENT_FAILED':
            case 'CHECKOUT_FAILURE':
                return $this->updateOrder('failed', $request);

            case 'PAYMENT_EXPIRED':
                return $this->updateOrder('expired', $request);

            case 'PAYMENT_CANCELLED':
            case 'CHECKOUT_DROPOUT':
            case 'CHECKOUT_CANCELLED':
                return $this->updateOrder('cancelled', $request);

            default:
                return response()->json(['message' => "Event {$event} ignored"], 200);
        }
    }

    public function updateOrder($status, $request)
    {
        // Always prefer requestReferenceNumber to find your order
        $orderId = $request->input('requestReferenceNumber')
            ?? $request->input('receiptNumber')
            ?? $request->input('transactionReferenceNumber');

        // Payment Method 
        $paymentScheme = $request->input('fundSource.details.scheme');


        $order = Order::where('reference_number', $orderId)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->status = $status;
        $order->payment_scheme = $paymentScheme;

        $amount = $order->amount;
        $order->save();

        if ($status == 'paid') {
            $existingBooking = FacilityBookingLog::where('token', $order->token)->first();

            if ($existingBooking) {
                // Delete the order token associated with this booking
                Order::where('token', $order->token)->delete();

                throw new \Exception('Reservation already exists. Order token has been cleaned up.');
            }

            $this->storeBookingInDatabase($order->token, $orderId, $amount, $paymentScheme);

        } else if ($status == 'expired' || $status == 'failed' || $status == 'cancelled') {
            $bookingData = Cache::get('booking_confirmation_' . $order->token);

            if ($bookingData) {
                Mail::to($bookingData['email'])->send(new PaymentFailed($bookingData['firstname']));
                Cache::forget('booking_confirmation_' . $order->token);
            }

        }
        return response()->json(['message' => "Order {$orderId} updated to {$status}"], 200);
    }


    private function storeBookingInDatabase($token, $orderId, $amount, $paymentScheme)
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
                'token' => $token,
                'code' => $bookingData['reservation_code']
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
                // Calculate facility price with discount logic
                $facilityPrice = $this->calculateDiscountedFacilityPrice($facility);

                // Create facility summary
                $facilitySummary = FacilitySummary::create([
                    'facility_id' => $facility->id,
                    'facility_price' => $facilityPrice, // Store discounted price
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

            // Create payment record
            Payments::create([
                'facility_log_id' => $bookingLog->id,
                'status' => 'paid',
                'amount' => $amount,
                'method' => $paymentScheme,
                'reference_no' => $orderId,
                'payment_date' => now(),
            ]);


            // This remove the room holds, means permanently booked
            $this->releaseHolds($bookingData);


            // Commit transaction
            DB::commit();

            $bookingLog->load([
                'payments',
                'details',
                'summaries.facility',
                'summaries.breakfast',
                'summaries.bookingDetails',
                'guestDetails.guestType',
                'user',
                'guestAddons'
            ]);

            // Generate PDF invoice
            $invoiceService = new InvoiceService();
            $pdf = $invoiceService->generateInvoice($bookingLog);

            event(new BookingNew($bookingLog)); // Event listener for new booking list
            $this->sendEmailAdmin($bookingLog);

            // Send email with PDF attachment
            Mail::to($bookingData['email'])->send(new ReservationReceived(
                $bookingLog,
                $pdf
            ));

            Cache::forget('booking_confirmation_' . $token);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Re-throw to handle in the controller
        }
    }

    private function releaseHolds($bookingData)
    {
        try {
            foreach ($bookingData['facilities'] as $facilityData) {
                RoomHold::where('facility_id', $facilityData['facility_id'])
                    ->where('date_from', $bookingData['checkin_date']) // Matches the string format saved in hold
                    ->where('date_to', $bookingData['checkout_date'])
                    ->delete();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clean up room holds via webhook: ' . $e->getMessage());
        }
    }

    /**
     * Calculate discounted facility price based on active discounts
     */
    private function calculateDiscountedFacilityPrice(Facility $facility)
    {
        $today = now();
        $basePrice = $facility->price;

        // Check for active discounts for this facility
        $activeDiscount = FacilityDiscount::where('facility_id', $facility->id)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if (!$activeDiscount) {
            return $basePrice; // No active discount, return base price
        }

        // Apply discount based on discount type
        switch ($activeDiscount->discount_type) {
            case 'percent':
                $discountAmount = $basePrice * ($activeDiscount->discount_value / 100);
                $discountedPrice = $basePrice - $discountAmount;
                break;

            case 'fixed':
                $discountedPrice = $basePrice - $activeDiscount->discount_value;
                break;

            default:
                $discountedPrice = $basePrice; // Unknown discount type, return base price
        }

        // Ensure price doesn't go below zero
        return max($discountedPrice, 0);
    }

    public function sendEmailAdmin(FacilityBookingLog $booking)
    {
        // Get all admin users who are active
        $admins = User::where('role', 'Admin')
            ->whereNotNull('email')
            ->get();

        if ($admins->isEmpty()) {
            Log::warning("No active admin with email found");
            return;
        }

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(
                    new AdminNotification($booking)
                );

                Log::info("Booking email sent to admin", [
                    'booking_id' => $booking->id,
                    'email' => $admin->email
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send admin email", [
                    'booking_id' => $booking->id,
                    'email' => $admin->email,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
