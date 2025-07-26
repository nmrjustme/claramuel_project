<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\CustomerBookingPageController;
use App\Http\Controllers\MyBookingsController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\PoolParkbookingController;
use App\Http\Controllers\AccommodationImgController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\Redirect;
use App\Http\Controllers\AdminsController;
// admins
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InquirerController;
use App\Http\Controllers\BookingMonitorController;
use App\Http\Controllers\FacilityDiscountController;
use App\Http\Controllers\BookingCalendarController;
use App\Http\Controllers\BookCottageController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\BookingLogController;
    
use App\Events\NewBookingRequest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use App\Models\FacilityBookingLog;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;


Route::get('/', [WelcomeController::class, 'index'])->name('index');
Route::get('/dashboard/', [FacilitiesController::class, 'showData'])->name('dashboard');
route::get('/dashboard/cottages', [BookCottageController::class, 'index'])->name('customer_bookings.cottage');

Route::get('/dashboard/checkin_page/{id}', [CustomerBookingPageController::class, 'Data'])->name('facility.deal');
Route::get('/dashboard/MyBookings/', [MyBookingsController::class, 'index'])->name('MyBookings');

Route::get('/Bookings', [BookingsController::class, 'index'])->name('customer_bookings');

Route::get('/bookings/get-facilities', [BookingsController::class, 'getFacilities'])->name('bookings.get-facilities');
Route::get('/Book', [PoolParkbookingController::class, 'index'])->name('Pools_Park');
Route::get('/Images/{id}', [AccommodationImgController::class, 'index'])->name('my_modals');
Route::post('/book', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/verify_email/', [VerifyEmailController::class, 'verify'])->name('verify.email');

Route::get('/events/', function (){
    return view('customer_pages.static_events');
})->name('events');

Route::get('/login', function (){
    return view('auth.login'); 
});

Route::get('/Payment/Submitted', function(){
   return view('customer_pages.payment_submitted'); 
})->name('payments.submitted');

// This Condition is to verify email first before proceed
Route::middleware(['auth', 'verified'])->group(function () {});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/profile/image', [ProfileImageController::class, 'update'])->name('profile.image.update');
});

Route::middleware(['auth'])->group(function () {


    Route::get('/test-broadcast', function () {
        // Get the most recent booking for testing
        $booking = FacilityBookingLog::with('user')->latest()->first();
        
        if (!$booking) {
            return 'No bookings found to broadcast. ';
        }

        // Fire the event
        event(new NewBookingRequest($booking));
        
        return 'New booking event broadcasted!' . $booking->id;
    });
    
    
    Route::get('/admin_dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/stats', [AdminController::class, 'getStats']);
    Route::get('/admin/dashboard/occupied-facilities', [AdminController::class, 'getOccupiedFacilities']);
    
    Route::get('booking-logs', [BookingLogController::class, 'index'])->name('admin.booking-logs');

    //Bookings
    Route::get('/admin/bookings', [AdminController::class, 'getBookings'])->name('admin.bookings');
    Route::get('/get/admin/bookings', [BookingController::class, 'index']);
    Route::get('/get/bookings/next-checkin', [BookingController::class, 'nextCheckin']);
    Route::get('/get/show/bookings/{booking}', [BookingController::class, 'show']);
    Route::get('/admin/bookings/export/', [BookingController::class, 'export'])->name('admin.bookings.export');
    
    Route::get('/facility_management', [AdminController::class, 'facilities'])->name('admin.facilities');
    Route::get('/events_management', [AdminController::class, 'events'])->name('admin.events');
    Route::get('/users_management', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/overall_bookings', [AdminController::class, 'overall_bookings'])->name('admin.overall_bookings');

    // open modal booking details each customer
    Route::get('/booking-details/{id}', [InquirerController::class, 'getBookingDetails'])->name('admin.inquirer.show');
    // open modal payment details each customer
    Route::get('/payment-details/{id}', [InquirerController::class, 'getPaymentDetails'])->name('admin.inquirerPayment.show');
    Route::post('/payment-details/{id}/verify', [InquirerController::class, 'verifyPayment'])->name('admin.inquirerPayment.verify');
    
    Route::post('/admin/inquiries/mark-all-as-read', [AdminController::class, 'markAllAsRead'])
    ->name('admin.inquiries.mark-all-as-read');
    
    // =======================
    // bookings monitoring
    // =======================
    Route::get('Inquiries', [AdminController::class, 'inquiries'])->name('admin.inquiries');
    
    
    // =======================
    // ajax fetching data in recent Inquirers at admin index
    // =======================
    Route::get('/api/inquiries', [InquirerController::class, 'getInquiries'])->name('api.inquiries');
    Route::post('/api/inquiries/mark-read/{id}', [InquirerController::class, 'markAsRead'])->name('api.inquiries.mark-read');
    Route::post('/api/inquiries/mark-all-read', [InquirerController::class, 'markAllAsRead'])->name('api.inquiries.mark-all-read');
    
    // =======================
    // Dynamic modal for customer booking request to edit their old data
    // =======================
    
    // soon
    
    
    // =======================
    // Facility Crud
    // =======================
    Route::get('/facilities', [FacilitiesController::class, 'AdminIndex'])->name('admin.facilities.index');
    Route::post('/facilities/store', [FacilitiesController::class, 'AdminStore'])->name('admin.facilities.store');
    
    Route::get('/admin/facilities/{id}/edit', [FacilitiesController::class, 'edit']);
    Route::put('/admin/facilities/update/{id}', [FacilitiesController::class, 'UpdateFacility']);
    Route::delete('/facilities/delete/{id}', [FacilitiesController::class, 'DeleteFacility']);
    
    // Cottages
    Route::get('/admin/cottages', [FacilitiesController::class, 'getCottage'])->name('admin.cottages.index');
    Route::get('/admin/cottages/{id}/edit', [FacilitiesController::class, 'editCottage'])->name('admin.cottages.edit');
    Route::post('/admin/cottages/store', [FacilitiesController::class, 'storeCottage'])->name('admin.cottages.store');
    Route::put('/admin/cottages/{id}', [FacilitiesController::class, 'updateCottage'])->name('admin.cottages.update');
    Route::delete('/admin/cottages/{id}', [FacilitiesController::class, 'destroyCottage'])->name('admin.cottages.destroy');
    Route::delete('/admin/cottage-images/{id}', [FacilitiesController::class, 'deleteCottageImage']);
    
    // Discounts
    Route::get('/facilities/discounted/{id}', [FacilitiesController::class, 'getDiscounts']);
    Route::post('/facilities/{id}/discounts', [FacilitiesController::class, 'addDiscount']);
    Route::match(['post', 'put'], '/facilities/discounts/{id}', [FacilitiesController::class, 'updateDiscount']);
    Route::delete('/facilities/discounts/delete/{id}', [FacilitiesController::class, 'deleteDiscount'])->where('id', '[0-9]+');
    
    // Facility Images Routes
    Route::get('/facilities/{id}/images', [FacilitiesController::class, 'getImages']);
    Route::delete('/admin/facilities/images/{image}', [FacilitiesController::class, 'deleteImage']);
    
    // =======================
    // =======================

    Route::get('/booking-updates', function () {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        echo "retry: 10000\n\n";

        config(['session.driver' => 'array']);
        set_time_limit(0);
        ob_implicit_flush(true);

        $lastEventId = (int)(request()->header('Last-Event-ID') ?? FacilityBookingLog::max('id') ?? 0);
        $lastUpdateTime = request()->input('last_update') ?? now()->subDay()->toDateTimeString();

        // Add connection timeout and limit
        $maxExecutionTime = 300; // 5 minutes
        $startTime = time();

        try {
            while (true) {
                // Check if connection is still alive
                if (connection_aborted() || (time() - $startTime) > $maxExecutionTime) {
                    break;
                }
                
                // Get new bookings with limit
                $newBookings = FacilityBookingLog::with(['user', 'details', 'payments'])
                    ->where('id', '>', $lastEventId)
                    ->orderBy('id', 'asc')
                    ->limit(100) // Add limit to prevent huge data transfers
                    ->get();

                // Get updated bookings with limit and time constraint
                $updatedBookings = FacilityBookingLog::with(['user', 'details', 'payments'])
                    ->where('updated_at', '>', $lastUpdateTime)
                    ->where('id', '<=', $lastEventId)
                    ->limit(100) // Add limit
                    ->get();

                if ($newBookings->isNotEmpty() || $updatedBookings->isNotEmpty()) {
                    $currentEventId = $newBookings->last()->id ?? $lastEventId;
                    $currentUpdateTime = now()->toDateTimeString();

                    $data = [
                        'bookings' => [
                            'new' => $newBookings->toArray(),
                            'updated' => $updatedBookings->toArray(),
                        ],
                        'lastUpdate' => $currentUpdateTime,
                        'playSound' => $newBookings->isNotEmpty() // Only play sound for new bookings
                    ];
                    
                    echo "id: {$currentEventId}\n";
                    echo "event: update\n";
                    echo 'data: ' . json_encode($data) . "\n\n";

                    $lastEventId = $currentEventId;
                    $lastUpdateTime = $currentUpdateTime;
                }

                @ob_flush();
                flush();

                // Clear memory
                unset($newBookings, $updatedBookings, $data);
                
                // Sleep but check for connection abort more frequently
                for ($i = 0; $i < 5; $i++) {
                    if (connection_aborted()) break 2;
                    sleep(1);
                }
            }
        } catch (\Throwable $e) {
            Log::error('SSE error', ['error' => $e->getMessage()]);
            echo "event: error\n";
            echo 'data: ' . json_encode(['error' => $e->getMessage()]) . "\n\n";
            @ob_flush();
            flush();
        }

        exit;
    });
    

    // calendar
    Route::get('/Calendar', [AdminController::class, 'calendar'])->name('admin.calendar');
    Route::get('/bookings/calendar', [BookingCalendarController::class, 'getCalendarData'])->name('admin.calendar.getDate');
    Route::get('/bookings/by-date', [BookingCalendarController::class, 'getBookingsByDate'])->name('admin.calendar.byDate');
    
    // =======================
    // Payments
    // =======================
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('admin.payments');
    Route::get('/payments/stream', [AdminPaymentController::class, 'stream'])->name('admin.payments.stream');
    Route::get('/payments/{id}/row', [AdminPaymentController::class, 'getPaymentRow'])->name('payments.row');
    Route::get('/admin/payments/{id}/details', [AdminPaymentController::class, 'getPaymentDetails'])->name('payments.details');
    Route::post('/payments/{id}/verify', [AdminPaymentController::class, 'verifyPayment'])->name('payments.verify');
    Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'rejectPayment'])->name('payments.reject');
    Route::post('/payments/{id}/update-reference', [AdminPaymentController::class, 'updateReference'])->name('payments.update-reference');
    Route::get('/payments/search', [AdminPaymentController::class, 'search'])->name('payments.search');
    
    // Add these routes to your web.php
    
    Route::post('/payments/{payment}/verify-with-receipt', [PaymentsController::class, 'verifyPaymentWithReceipt'])
        ->name('payments.verify-with-receipt');
    
    Route::post('/payments/verify-qr', [PaymentsController::class, 'verifyQrCode'])
        ->name('payments.verify-qr');
    
    Route::post('/admin/payments/verify/{payment_id}', [PaymentsController::class, 'verifyScannedPayment'])
    ->name('admin.payments.verify');
    
    Route::get('check-in/scanner', function(){
        
    });
    
    Route::get('/check-in/scanner', [CheckinController::class, 'showScanner'])->name('checkin.scanner');
    Route::post('/verify-qr-codes', [CheckinController::class, 'verifyQrCode']);
    Route::get('/check-in/success/{id}', [CheckinController::class, 'showPrinting']);
    
    Route::post('/payments/{paymentId}/update-remaining-status', [PaymentsController::class, 'updateRemainingStatus']);
    
});

// =======================
// storing customer payment proof
// =======================
Route::post('/payment/update/{booking}', [PaymentsController::class, 'updateCustomerPayment'])->name('payments.update');

Route::get('/booked_date', [BookingsController::class, 'getUnavailableDates']);

Route::post('/confirm-booking/{id}', [InquirerController::class, 'confirmBooking'])->name('booking.confirm');
Route::post('/reject-booking/{id}', [InquirerController::class, 'rejectBooking'])->name('booking.reject');

Route::get('/booking/verify/{token}', [InquirerController::class, 'verifyBooking'])->name('booking.verify');
Route::get('/payment/create/{booking}', [PaymentsController::class, 'payments'])->name('payments');
Route::get('/NotVerified/', [PaymentsController::class, 'notVerified'])->name('not.verified');
Route::get('/link-expired', function() {
    return view('customer_pages.invalid_link');
})->name('invalid_link');
Route::get('/admin/inquiries/check-new', [InquirerController::class, 'checkNewInquiries'])
     ->name('admin.inquiries.check-new');
     
Route::get('/booking/completed/{booking}', [BookingsController::class, 'booking_completed'])->name('booking.completed');


Route::get('/WaitForConfirmation', [BookingController::class, 'WaitConfirmation'])
     ->name('booking.WaitConfirmation');
     
Route::get('/booking/redirect/{booking}', function($booking) {
    return view('booking-redirect', ['booking' => $booking]);
})->name('booking.redirect');
     
// Route::get('/booking/verify/{token}', [BookingController::class, 'verify'])->name('booking.verify');

// Route::post('/booking/{booking}/resend-confirmation', [BookingController::class, 'resendConfirmation'])
//     ->name('booking.resendConfirmation');

// In routes/web.php



//Route::get('admin/dashboard', [AdminsController::class, 'index'])->name('admin.index');



require __DIR__.'/auth.php';
