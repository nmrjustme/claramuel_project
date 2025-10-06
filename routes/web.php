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
use App\Http\Controllers\Day_tour_Controller;
use App\Http\Controllers\BookingReplyController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\MayaCheckoutController;
use App\Http\Controllers\MayaWebhookController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MayaWebhookSetupController;
use App\Http\Controllers\RoomMonitoringController;
use App\Http\Controllers\AdminBookingsController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminlistUser;
use App\Http\Controllers\RoomBookReportController;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Accounting;
use App\Models\FacilityBookingLog;
use App\Services\InvoiceService; // adjust namespace if different

Route::get('/test-invoice/{id}', function ($id) {
    $booking = FacilityBookingLog::findOrFail($id);

    $invoiceService = new InvoiceService();
    $pdf = $invoiceService->generateInvoice($booking);

    // Instead of streaming the PDF, test a redirect after generating it
    return redirect('/redirect-success')->with('message', 'Invoice generated successfully!');
});

Route::get('/redirect-success', function () {
    return "✅ Redirected successfully! " . session('message');
});

Route::get('/', [WelcomeController::class, 'index'])->name('index');
Route::get('/dashboard', [FacilitiesController::class, 'showData'])->name('dashboard');
route::get('/dashboard/cottages', [BookCottageController::class, 'index'])->name('customer_bookings.cottage');

Route::get('/customer/guest-types', [GuestTypeController::class, 'index'])->name('customer.guest-types');

Route::get('/dashboard/checkin_page/{id}', [CustomerBookingPageController::class, 'Data'])->name('facility.deal');


// =======================
// Maya Payment Routes
// =======================
// Checkout page
Route::get('/maya-checkout', [MayaCheckoutController::class, 'index'])->name('maya.checkout');
Route::post('/maya/create-session', [MayaCheckoutController::class, 'createCheckoutSession'])->name('maya.create.session');
// Route::get('/maya/success/', [MayaCheckoutController::class, 'handleSuccess'])->name('maya.checkout.success');
Route::get('/maya/payment-processing/{token}', [MayaCheckoutController::class, 'handleProcessing'])->name('maya.checkout.processing');
Route::get('/maya/check-order/{token}', [MayaCheckoutController::class, 'checkOrder'])->name('maya.checkOrder');
// Handle Failure, cancel, or expired
Route::get('/maya/failure/{reason}/{order}/{token}', [MayaCheckoutController::class, 'handleFailure'])->name('maya.checkout.failure');

Route::get('/maya/cancel', [MayaCheckoutController::class, 'handleCancel'])->name('maya.checkout.cancel');
Route::post('/maya/webhook', [MayaCheckoutController::class, 'handleWebhook'])->name('maya.webhook');
Route::get('/maya/status/{referenceNumber}', [MayaCheckoutController::class, 'checkPaymentStatus'])->name('maya.check.status');
// Route::get('/maya/success/paid', [MayaCheckoutController::class, 'handleSuccessPaid'])->name('maya.success.checkout');
// =======================
Route::post('/maya/payment/webhook', [MayaWebhookSetupController::class, 'handle'])->name('maya.webhook.succes');


// =======================
// Awaiting verification page 
// =======================
Route::get('/booking-awaiting', [BookingsController::class, 'pendingVerification'])->name('booking-awaiting');


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


Route::post('/book', [BookingController::class, 'store'])->name('bookings.store'); // Ongoing
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
Route::middleware(['auth', 'verified'])->group(function () { });

// Amenities Modal Route
Route::get('/api/facilities/{facility}/amenities', [BookingsController::class, 'getAmenities'])
    ->name('facilities.amenities');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/image', [ProfileImageController::class, 'update'])->name('profile.image.update');
});

Route::get('/env-test', function () {
    return [
        'PUSHER_APP_KEY' => env('PUSHER_APP_KEY'),
        'PUSHER_APP_SECRET' => env('PUSHER_APP_SECRET'),
        'PUSHER_APP_ID' => env('PUSHER_APP_ID'),
        'PUSHER_APP_CLUSTER' => env('PUSHER_APP_CLUSTER'),
        'loaded_file' => file_exists(base_path('.env')),
    ];
});

Route::get('/daytour/check-availability', [Day_tour_Controller::class, 'checkAvailability'])->name('daytour.checkAvailability');
Route::get('/cottages/{date}', [Day_tour_Controller::class, 'getCottages'])->name('cottages.availability');




Route::prefix('admin-management')->name('admin.')->group(function () {
    Route::get('/', [AdminlistUser::class, 'index'])->name('list.management');
    Route::get('/list', [AdminlistUser::class, 'data'])->name('list.data');
    Route::post('/create', [AdminlistUser::class, 'create'])->name('list.create');
    Route::put('/update/{id}', [AdminlistUser::class, 'update'])->name('list.update');
    Route::delete('/delete/{id}', [AdminlistUser::class, 'delete'])->name('list.delete');
    Route::post('/reset-password/{id}', [AdminlistUser::class, 'resetPassword'])->name('reset-password');
});

Route::middleware(['auth'])->group(function () {
    //========================
    // Room Reports
    //========================
    // Route::get('/earnings-chart', [RoomBookReportController::class, 'index'])->name('earnings.chart');
    // Route::get('/api/earnings-data', [RoomBookReportController::class, 'earningsByRoomCategory'])->name('api.earnings.data');
    // Route::get('/api/categories', [RoomBookReportController::class, 'getCategories'])->name('api.categories');
    // Route::get('/api/months', [RoomBookReportController::class, 'getAvailableMonths'])->name('api.months');
    // Route::get('/api/years', [RoomBookReportController::class, 'getAvailableYears'])->name('api.years');

    Route::get('/analytics', [RoomBookReportController::class, 'index'])->name('earnings.chart');
    Route::get('/analytics/data', [RoomBookReportController::class, 'getEarningsData'])->name('earnings.data');
    Route::get('/analytics/category-earnings', [RoomBookReportController::class, 'getCategoryEarnings'])->name('earnings.category');
    Route::get('/analytics/comparison', [RoomBookReportController::class, 'getComparisonData'])->name('earnings.comparison');
    Route::get('/analytics/export', [RoomBookReportController::class, 'exportEarningsData'])->name('earnings.export');
    Route::get('/analytics/years', [RoomBookReportController::class, 'getAvailableYears'])->name('earnings.years');
    //========================

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

    Route::prefix('admin/facilities')->name('admin.facilities.')->group(function () {
        Route::get('{id}/details', [Day_tour_Controller::class, 'details'])->name('details');
        Route::post('{id}/mark-available', [Day_tour_Controller::class, 'markAvailable'])->name('markAvailable');
    });


    // Day Tour Routes Group
    Route::prefix('admin/daytour')->name('admin.daytour.')->group(function () {

        // Main Day Tour Registration
        Route::get('/', [Day_tour_Controller::class, 'index'])->name('index');
        Route::get('/create', [Day_tour_Controller::class, 'create'])->name('create');
        Route::post('/store', [Day_tour_Controller::class, 'store'])->name('store');

        // Facility Availability Check
        Route::get('/facility-availability', [Day_tour_Controller::class, 'facilityAvailability'])->name('facility-availability');
        Route::get('/check-availability', [Day_tour_Controller::class, 'checkAvailability'])->name('check-availability');

        // Logs Management
        Route::get('/logs', [Day_tour_Controller::class, 'logs'])->name('logs');
        Route::get('/logs/{id}', [Day_tour_Controller::class, 'show'])->name('logs.show');
        Route::get('/logs/{id}/edit', [Day_tour_Controller::class, 'edit'])->name('logs.edit');
        Route::put('/logs/{id}', [Day_tour_Controller::class, 'update'])->name('logs.update');
        Route::get('/logs/{id}/print', [Day_tour_Controller::class, 'print'])->name('logs.print');

        // Cottage & Villa Monitoring
        Route::get('/facility-monitoring', [Day_tour_Controller::class, 'monitorFacilities'])->name('facility_monitoring');
        Route::get('/admin/daytour/facility-monitoring', [Day_tour_Controller::class, 'monitorFacilities'])->name('admin.daytour.facility_monitoring');
        Route::post('daytour/{id}/checkin', [Day_tour_Controller::class, 'checkin'])->name('checkin');
        Route::post('daytour/{id}/checkout', [Day_tour_Controller::class, 'checkout'])->name('checkout');

        // Facility Calendar
        Route::get('/facility-calendar', [Day_tour_Controller::class, 'facilityCalendar'])->name('facility-calendar');
    });

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
    // Route::get('/get/inquiries/booking', [BookingController::class, 'getMyInquiries'])->name('my_inquiries_bookings');

    Route::get('/get/inquiries/confirmed', [BookingController::class, 'getConfirmedInquiries']);
    Route::get('/get/inquiries/pending', [BookingController::class, 'getPendingInquiries']);

    //========================
    // Bookings Page
    //========================
    Route::get('/get/mybooking', [BookingController::class, 'getMyBookings'])->name('my_bookings');
    Route::get('/get/admin/bookings', [BookingController::class, 'index']);
    Route::get('/get/admin/bookings/guest/details/', [BookingController::class, 'guestDetailsList'])->name('guest.details.list');


    Route::get('/get/show/bookings/checkin/{id}', [BookingController::class, 'paymentDetails']);
    Route::post('/bookings/{id}/process-payment', [BookingController::class, 'processMyPayment']);
    Route::post('/bookings/{id}/checkin', [BookingController::class, 'checkin']);

    Route::post('/admin/guest-addons', [BookingController::class, 'storeGuestAddons']);
    Route::delete('/delete/guest-addons/{addonId}', [BookingController::class, 'deleteAddon']);

    // In routes/web.php
    Route::get('/user/verification-status', function () {
        return response()->json([
            'verified' => !is_null(Auth::user()->email_verified_at),
            'email' => Auth::user()->email,
        ]);
    })->middleware('auth');


    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancelBooking']);
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

    //Get Rooms avaibilities monitoring
    Route::get('/room/monitoring', [RoomMonitoringController::class, 'index']);
    Route::get('/room/monitoring/data', [RoomMonitoringController::class, 'data'])->name('monitor.room.data');

    Route::get('admin/bookings/management', [AdminBookingsController::class, 'index']);


    Route::get('/rooms/get', function () {
        return view('admin.monitoring.index');
    });

    // Revenue monitoring
    Route::get('/dashboard/revenue', [AccountingController::class, 'index']);
    Route::get('/income-chart', [AccountingController::class, 'showIncomeChart']);
    Route::get('/api/monthly-income', [AccountingController::class, 'monthlyIncomeApi'])->name('income.chart.data');
    Route::get('/dashboard/revenue', [AccountingController::class, 'index'])->name('admin.accounting.index');
    Route::get('/dashboard/reports/export', [AccountingController::class, 'export'])->name('admin.reports.export');
    Route::get('/admin/api/monthly-income', [AccountingController::class, 'monthlyIncomeApi'])->name('admin.api.monthly-income');
    Route::get('/admin/api/top-performers', [AccountingController::class, 'topPerformersApi'])->name('admin.api.top-performers');

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
    Route::get('/next/check-in/list', [CheckinController::class, 'index'])->name('incoming.list');
    Route::get('/next/check-in/list/data', [CheckinController::class, 'dataList'])->name('checkins.data');

    Route::get('/check-in/scanner', [CheckinController::class, 'showScanner'])->name('checkin.scanner');
    Route::post('/verify-qr-codes/checkin', [CheckinController::class, 'verifyQrCode']);
    Route::post('/check-in/process-qr-upload', [CheckInController::class, 'processUploadQrUpload']);
    Route::get('/check-in/success/{id}', [CheckinController::class, 'showPrinting'])->name('show.print-checkout');
    Route::get('/qrScanner/customer-details/{paymentId}', [CheckinController::class, 'getCustomerDetails']);
    Route::get('/check-in/search-guests', [CheckinController::class, 'searchGuests']);
    Route::post('/update/booking/status/{id}', [CheckinController::class, 'updateStatus']);
    Route::get('/check-in/used', function (Request $request) {
        $qrPath = $request->query('path');
        return view('admin.qr_in_used', ['qrPath' => $qrPath]);
    });

    Route::post('/decode-qr-booking', [CheckinController::class, 'decodeQrBooking']);
    // =======================

    // =======================
    // Check-out Routes
    // =======================
    Route::get('/check-out/scanner', [CheckoutController::class, 'scannerPage'])->name('checkout.scanner');
    Route::post('/verify-qr-codes/checkout', [CheckoutController::class, 'verifyQrCode']);
    Route::get('/check-out/receipt/{id}', [CheckoutController::class, 'showPrinting'])->name('checkout.receipt');
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
// Route::get('/payment/create/{token}', [PaymentsController::class, 'payments'])->name('customer.booking.payments');
Route::get('/payment/create/{token}', [MayaCheckoutController::class, 'createCheckoutSession'])->name('customer.booking.payments');
// =======================

Route::get('/NotVerified', [PaymentsController::class, 'notVerified'])->name('not.verified');
Route::get('/payment/order/status/{token}', [PaymentsController::class, 'checkOrderStatus'])
    ->name('payment.status');


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