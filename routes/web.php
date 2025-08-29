<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\CustomerBookingPageController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\PoolParkbookingController;
use App\Http\Controllers\AccommodationImgController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\EmailConfirmationController;

// admins
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InquirerController;
use App\Http\Controllers\BookingCalendarController;
use App\Http\Controllers\BookCottageController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\BookingLogController;
use App\Http\Controllers\GuestTypeController;
use App\Http\Controllers\DayTourController;
use App\Http\Controllers\BookingReplyController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

Route::get('/', [WelcomeController::class, 'index'])->name('index');
Route::get('/dashboard', [FacilitiesController::class, 'showData'])->name('dashboard');
route::get('/dashboard/cottages', [BookCottageController::class, 'index'])->name('customer_bookings.cottage');

Route::get('/customer/guest-types', [GuestTypeController::class, 'index'])->name('customer.guest-types');

Route::get('/dashboard/checkin_page/{id}', [CustomerBookingPageController::class, 'Data'])->name('facility.deal');

// =======================
// Awaiting verification page 
// =======================
Route::get('/booking-awaiting', [BookingsController::class, 'pendingVerification']);

Route::get('/WaitForConfirmation', [BookingController::class, 'WaitConfirmation'])
    ->name('booking.WaitConfirmation');

Route::get('/booking-submitted', [BookingController::class, 'bookingSubmitted'])
    ->name('booking.submitted');


Route::get('/Bookings', [BookingsController::class, 'index'])->name('customer_bookings');
Route::get('/dashboard/Bookings', [BookingsController::class, 'bookings_page'])->name('dashboard.bookings');
Route::get('/bookings/customer-info', [BookingsController::class, 'customerInfo'])->name('bookings.customer-info');

// =======================
// Email Confirmation
// =======================

// Send email
Route::post('/customer/send-email-otp', [EmailConfirmationController::class, 'sendOTP'])
->name('booking.send_otp');

// Confirm the email
Route::post('/verify/otp', [EmailConfirmationController::class, 'verifyOtp'])
    ->name('verify.otp');

// Redirect to email front-end
Route::get('/booking/pending', function (Request $request) {
    // Validate that both email and token are present
    $request->validate([
        'email' => 'required|email',
        'token' => 'required|string'
    ]);
    
    return view('customer_pages.booking.otp_confirmation', [
        'email' => $request->email,
        'token' => $request->token
    ]);
})->name('booking.pending');

Route::post('/resend-otp', [EmailConfirmationController::class, 'resendOtp'])
    ->name('resend.otp'); // 3 attempts every 10 minutes
// =======================


Route::get('/bookings/get-facilities', [BookingsController::class, 'getFacilities'])->name('bookings.get-facilities');
Route::get('/Book', [PoolParkbookingController::class, 'index'])->name('Pools_Park');
Route::get('/Images/{id}', [AccommodationImgController::class, 'index'])->name('my_modals');


Route::post('/book', [BookingController::class, 'store'])->name('bookings.store');// Ongoing
Route::post('/new/booking', [BookingController::class, 'stores'])->name('booking.stores');

Route::get('/verify_email', [VerifyEmailController::class, 'verify'])->name('verify.email');

Route::get('/events', function () {
    return view('customer_pages.static_events');
})->name('events');

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/Payment/Submitted', function () {
    return view('customer_pages.payment_submitted');
})->name('payments.submitted');

// This Condition is to verify email first before proceed
Route::middleware(['auth', 'verified'])->group(function () {});

// Amenities Modal Route
Route::get('/api/facilities/{facility}/amenities', [BookingsController::class, 'getAmenities'])
    ->name('facilities.amenities');


Route::get('/test-insert-user', function () {
    DB::table('users')->insert([
        'firstname' => 'Richter',
        'lastname'  => 'Mayandoc',
        'phone'     => '09169824195',
        'role'      => 'Admin',
        'email'     => 'rmayandoc0625@gmail.com',
        'password'  => Hash::make('?richter11'),
    ]);
    return 'User inserted!';
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/image', [ProfileImageController::class, 'update'])->name('profile.image.update');
});

Route::get('/env-test', function() {
    return [
        'PUSHER_APP_KEY' => env('PUSHER_APP_KEY'),
        'PUSHER_APP_SECRET' => env('PUSHER_APP_SECRET'),
        'PUSHER_APP_ID' => env('PUSHER_APP_ID'),
        'PUSHER_APP_CLUSTER' => env('PUSHER_APP_CLUSTER'),
        'loaded_file' => file_exists(base_path('.env')),
    ];
});

Route::middleware(['auth'])->group(function () {
    //========================
    // Sidebar Routes
    //========================
    Route::get('/admin_dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/bookings', [AdminController::class, 'getBookings'])->name('admin.bookings');
    Route::get('Inquiries', [AdminController::class, 'inquiries'])->name('admin.inquiries');
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('admin.payments');
    Route::get('/Calendar', [AdminController::class, 'calendar'])->name('admin.calendar');
    Route::get('/facilities', [FacilitiesController::class, 'AdminIndex'])->name('admin.facilities.index');
    
    // Get badge counts Unread or New
    Route::get('/unread-counts/all', [AdminController::class, 'getAllUnreadCounts']);
    
    //========================
    //========================
    
    //========================
    //Email monitoring
    //========================
    Route::get('/booking-replies', [BookingReplyController::class, 'index'])->name('admin.email'); // Web interface
    Route::get('/inbox', [BookingReplyController::class, 'fetchInbox']);
    Route::post('/email/mark-as-read', [BookingReplyController::class, 'markAsRead']);
    Route::get('/emails/{email_id}/attachment/{attachment_id}', [BookingReplyController::class, 'downloadAttachment'])
    ->name('emails.download.attachment');
    //========================
    
    
    Route::get('/admin/dashboard/stats', [AdminController::class, 'getStats']);
    Route::get('booking-logs', [BookingLogController::class, 'index'])->name('admin.booking-logs');
    
    // Booking log route
    Route::get('/get/inquiries/booking', [BookingController::class, 'getMyInquiries'])->name('my_inquiries_bookings');
    
    //========================
    // Bookings Page
    //========================
    Route::get('/get/mybooking', [BookingController::class, 'getMyBookings'])->name('my_bookings');
    Route::get('/get/admin/bookings', [BookingController::class, 'index']);
    Route::get('/get/admin/bookings/guest/details/', [BookingController::class, 'guestDetailsList'])->name('guest.details.list');
    //========================
    
    //========================
    // Dashboard Routes
    //========================
    // Next Check-in route
    Route::get('/get/bookings/next-checkin', [BookingController::class, 'nextCheckin']);
    // Get Occupied Facilities
    Route::get('/admin/dashboard/occupied-facilities', [AdminController::class, 'getOccupiedFacilities']);
    // Get Cottages
    Route::get('/get/admin/cottages', [DayTourController::class, 'getCottages']);
    // Register customer for daytour
    Route::get('/day-tour/register', [DayTourController::class, 'register']);
    // Host toogle
    Route::post('/host/status', [AdminController::class, 'updateActiveHost']);
    // Get Active Admins
    Route::get('/admin/active-admins', [AdminController::class, 'getActiveAdmins']);
    //========================
    
    
    Route::get('/get/show/bookings/{booking}', [BookingController::class, 'show']);

    Route::get('/admin/bookings/export/', [BookingController::class, 'export'])->name('admin.bookings.export');
    
    Route::get('/facility_management', [AdminController::class, 'facilities'])->name('admin.facilities');
    Route::get('/events_management', [AdminController::class, 'events'])->name('admin.events');
    Route::get('/users_management', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/overall_bookings', [AdminController::class, 'overall_bookings'])->name('admin.overall_bookings');
    
    // open modal booking details each customer
    Route::get('/booking-details/{id}', [InquirerController::class, 'getBookingDetails'])->name('admin.inquirer.show');
    
    Route::get('/admin/booking-details/{id}', [BookingController::class, 'bookingDetails'])->name('admin.booking.details');
    
    // open modal payment details each customer
    Route::get('/payment-details/{id}', [InquirerController::class, 'getPaymentDetails'])->name('admin.inquirerPayment.show');
    
    Route::post('/admin/inquiries/mark-all-as-read', [AdminController::class, 'markAllAsRead'])
        ->name('admin.inquiries.mark-all-as-read');
    
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
    // Calendar
    // =======================
    
    // calendar
    Route::get('/bookings/calendar', [BookingCalendarController::class, 'getCalendarData'])->name('admin.calendar.getDate');
    Route::get('/bookings/by-date', [BookingCalendarController::class, 'getBookingsByDate'])->name('admin.calendar.byDate');
    Route::get('/facilities/availability/', [BookingCalendarController::class, 'getUnavailableDatesFacility'])->name('admin.getUnavailableDates');

    // =======================
    // Payments
    // =======================
    Route::get('/payments/{id}/row', [AdminPaymentController::class, 'getPaymentRow'])->name('payments.row');
    Route::get('/admin/payments/{id}/details', [AdminPaymentController::class, 'getPaymentDetails'])->name('payments.details');
    Route::post('/admin/payments/{id}/verify', [AdminPaymentController::class, 'verifyPayment'])->name('payments.verify');
    Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'rejectPayment'])->name('payments.reject');
    Route::post('/payments/{id}/update-reference', [AdminPaymentController::class, 'updateReference'])->name('payments.update-reference');
    Route::get('/payments/search', [AdminPaymentController::class, 'search'])->name('payments.search');

    // Add these routes to your web.php
    
    Route::post('/bookings/{booking}/verify-with-receipt', [InquirerController::class, 'verifyBookingWithReceipt'])
        ->name('payments.verify-with-receipt');
    
    // Route::post('/payments/verify-qr', [PaymentsController::class, 'verifyQrCode'])
    //     ->name('payments.verify-qr');
    
    
    Route::post('/admin/payments/verify/{payment_id}', [PaymentsController::class, 'verifyScannedPayment'])
        ->name('admin.payments.verify');

    Route::get('/admin/payments/search', [AdminPaymentController::class, 'search'])->name('admin.payments.search');
    
    // =======================
    // Check-in Routes
    // =======================
    Route::get('/check-in/scanner', [CheckinController::class, 'showScanner'])->name('checkin.scanner');
    Route::post('/verify-qr-codes/checkin', [CheckinController::class, 'verifyQrCode']);
    Route::post('/check-in/process-qr-upload', [CheckInController::class, 'processUploadQrUpload']);
    Route::get('/check-in/success/{id}', [CheckinController::class, 'showPrinting']);
    Route::get('/qrScanner/customer-details/{paymentId}', [CheckinController::class, 'getCustomerDetails']);
    Route::get('/check-in/search-guests', [CheckinController::class, 'searchGuests']);
    Route::post('/update/booking/status/{id}', [CheckinController::class, 'updateStatus']);
    Route::get('/check-in/used', function (Request $request) {
        $qrPath = $request->query('path');
        return view('admin.qr_in_used', ['qrPath' => $qrPath]);
    });
    // =======================

    // =======================
    // Check-out Routes
    // =======================
    Route::get('/check-out/scanner', [CheckoutController::class, 'scannerPage'])->name('checkout.scanner');
    Route::post('/verify-qr-codes/checkout', [CheckoutController::class, 'verifyQrCode']);
    Route::get('/check-out/receipt/{id}', [CheckoutController::class, 'showPrinting']);
    Route::post('/check-out/process-qr-upload', [CheckoutController::class, 'processUploadQrUpload']);
    Route::get('/check-out/search-guests', [CheckoutController::class, 'searchGuests']);
    Route::post('/update/booking/checkout/status/{id}', [CheckoutController::class, 'updateStatus']);
    // =======================
    
    Route::post('/payments/{paymentId}/update-remaining-status', [PaymentsController::class, 'updateRemainingStatus']);

});

// =======================
// storing customer payment proof
// =======================
Route::post('/submit/booking/{token}', [PaymentsController::class, 'submitBooking'])->name('payments.booking.submit');

Route::get('/booked_date', [BookingsController::class, 'getUnavailableDates']);

Route::post('/confirm-booking/{id}', [InquirerController::class, 'confirmBooking'])->name('booking.confirm');
Route::post('/reject-booking/{id}', [InquirerController::class, 'rejectBooking'])->name('booking.reject');

// =======================
// Payments Redirection
// =======================
Route::get('/payment/create/{token}', [PaymentsController::class, 'payments'])->name('customer.booking.payments');
// =======================

Route::get('/NotVerified', [PaymentsController::class, 'notVerified'])->name('not.verified');


Route::get('/link-expired', function () {
    return view('customer_pages.invalid_link');
})->name('invalid_link');

Route::get('/admin/inquiries/check-new', [InquirerController::class, 'checkNewInquiries'])
    ->name('admin.inquiries.check-new');

Route::get('/booking/redirect/{token}', function ($token) {
    return view('booking-redirect', ['token' => $token]);
})->name('booking.redirect');


// Route::get('/booking/verify/{token}', [BookingController::class, 'verify'])->name('booking.verify');

// Route::post('/booking/{booking}/resend-confirmation', [BookingController::class, 'resendConfirmation'])
//     ->name('booking.resendConfirmation');

// In routes/web.php

//Route::get('admin/dashboard', [AdminsController::class, 'index'])->name('admin.index');

require __DIR__ . '/auth.php';
