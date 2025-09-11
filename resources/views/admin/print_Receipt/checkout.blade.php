<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-out Receipt - {{ $payment->bookingLog->reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            body {
                font-size: 13px;
            }

            .header h1 {
                font-size: 18px;
            }

            .container {
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-border {
                border: 1px solid #e5e7eb !important;
            }

            .print-label {
                display: inline;
                font-weight: 500;
                color: #6b7280;
                margin-right: 0.5rem;
                min-width: 50%;
            }

            .print-value {
                display: inline;
                font-weight: 500;
                color: #1f2937;
                text-align: right;
                flex-grow: 1;
            }

            .print-row {
                display: flex;
                justify-content: space-between;
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .payment-row {
                display: flex;
                justify-content: space-between;
                width: 100%;
                margin-bottom: 0.25rem;
            }

            .payment-label {
                font-weight: 500;
                color: #6b7280;
            }

            .payment-value {
                font-weight: 500;
                color: #1f2937;
            }
        }

        .header-gradient {
            background: linear-gradient(135deg, #f63b3b 0%, #d81d1d 100%);
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        }

        .receipt-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .highlight-box {
            border-left: 4px solid #f63b3b;
        }

        .watermark {
            position: absolute;
            opacity: 0.05;
            font-size: 120px;
            font-weight: bold;
            color: #f63b3b;
            transform: rotate(-30deg);
            pointer-events: none;
            z-index: 0;
        }

        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            padding: 1.5rem;
            transform: translateY(-10px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-container {
            transform: translateY(0);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .modal-button {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-button-no {
            background-color: #e5e7eb;
            color: #374151;
        }

        .modal-button-no:hover {
            background-color: #d1d5db;
        }

        .modal-button-yes {
            background-color: #10B981;
            color: white;
        }

        .modal-button-yes:hover {
            background-color: #059669;
        }

        /* Loading spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #10B981;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans" style="font-family: 'Inter', sans-serif;">
    <div class="container mx-auto max-w-6xl p-4">
        <!-- Print Button -->
        <div class="no-print text-center mb-4 flex space-x-4 justify-center">
            <a href="{{ route('admin.bookings') }}"
                class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Back
            </a>
            <button onclick="handlePrint()" id="print-btn"
                class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="CurrentColor">
                    <path fill-rule="evenodd"
                        d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                        clip-rule="evenodd" />
                </svg>
                Print Receipt
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Left Side - Receipt -->
            <div class="md:w-2/3">
                <div class="rounded-xl border border-gray-200 overflow-hidden relative print-border">
                    <!-- Watermark -->
                    <div class="watermark hidden md:block top-1/4 left-1/4">RECEIPT</div>

                    <!-- Header -->
                    <div class="header-gradient text-white p-6 text-center relative">
                        <div class="absolute top-4 left-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 opacity-20" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold mb-1">CHECK-OUT RECEIPT</h1>
                        <p class="text-red-100 opacity-90 text-sm">Reservation Code: {{
                            $payment->bookingLog->code }}</p>
                        <p class="text-red-100 opacity-90 text-sm">
                            Check-out At:
                            {{ $payment->bookingLog->checked_out_at ?
                            \Carbon\Carbon::parse($payment->bookingLog->checked_out_at)->format('F j, Y
                            \a\t g:i A') : 'N/A' }}
                        </p>
                    </div>

                    <div class="p-6 relative z-10">
                        <!-- Guest Information -->
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="bg-red-200 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-800">Guest Information</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print:block">
                                <div class="p-3 rounded-lg print:bg-transparent print:p-0 print:border-0 print-row">
                                    <span
                                        class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Full
                                        name:</span>
                                    <span class="font-medium text-gray-800 print-value">{{
                                        $payment->bookingLog->user->firstname }} {{
                                        $payment->bookingLog->user->lastname }}</span>
                                </div>

                                <div class="p-3 rounded-lg print:bg-transparent print:p-0 print:border-0 print-row">
                                    <span
                                        class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Email:</span>
                                    <span class="font-medium text-gray-800 print-value">{{
                                        $payment->bookingLog->user->email }}</span>
                                </div>

                                <div class="p-3 rounded-lg print:bg-transparent print:p-0 print:border-0 print-row">
                                    <span
                                        class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Phone:</span>
                                    <span class="font-medium text-gray-800 print-value">{{
                                        $payment->bookingLog->user->phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Summary -->
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="bg-red-200 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h8a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 2a1 1 0 000 2h4a1 1 0 100-2H7zm0 4a1 1 0 000 2h4a1 1 0 100-2H7z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-800">Booking Summary</h2>
                            </div>

                            @php
                            // Calculate total amount from booking details
                            $totalAmount = $payment->bookingLog
                            ->details
                            ->sum('total_price');
                            @endphp

                            @foreach($payment->bookingLog->summaries as $summary)
                            @php
                            $facility = $summary->facility;
                            $bookingDetail = $summary->bookingDetails->first();

                            if ($bookingDetail) {
                            $breakfastPrice = $summary->breakfast ? $summary->breakfast_price : 0;
                            $nights =
                            $bookingDetail->checkin_date->diffInDays($bookingDetail->checkout_date);
                            $subtotal = ($summary->facility_price + $breakfastPrice) * $nights;
                            }

                            $guestsForFacility = $payment->bookingLog->guestDetails
                            ->where('facility_id', $summary->facility_id)
                            ->groupBy('guest_type_id')
                            ->map(function($items) {
                            return [
                            'type' => $items->first()->guestType->type ?? 'Unknown',
                            'quantity' => $items->sum('quantity')
                            ];
                            });

                            @endphp

                            @if($bookingDetail)
                            <div class="highlight-box p-4 rounded-lg mb-4 print:border-0 print:p-0">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print:block">
                                    <div class="print-row">
                                        <span
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Room
                                            Type:</span>
                                        <span class="font-medium text-gray-800 print-value">{{
                                            $facility->name }} (₱{{
                                            number_format($facility->price, 2) }}/night)</span>
                                    </div>
                                    <div class="print-row">
                                        <span
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Room
                                            Number:</span>
                                        <span class="font-medium text-gray-800 print-value">{{
                                            $facility->room_number ?? 'Not assigned' }}</span>
                                    </div>

                                    <div class="print-row">
                                        <span
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Check-in:</span>
                                        <span class="font-medium text-gray-800 print-value">
                                            {{ $bookingDetail->checkin_date->format('F j, Y g:i
                                            A') }}
                                        </span>
                                    </div>

                                    <div class="print-row">
                                        <span
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Check-out:</span>
                                        <span class="font-medium text-gray-800 print-value">
                                            {{ $bookingDetail->checkout_date->format('F j, Y g:i
                                            A') }}
                                        </span>
                                    </div>

                                    <div class="print-row">
                                        <span
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Duration:</span>
                                        <span class="font-medium text-gray-800 print-value">
                                            {{ $nights }} night(s)
                                        </span>
                                    </div>

                                    @if($summary->breakfast)
                                    <div class="print-row">
                                        <span
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Breakfast:</span>
                                        <span class="font-medium text-gray-800 print-value">₱{{
                                            number_format($summary->breakfast_price * $nights)
                                            }}/morning(s)</span>
                                    </div>
                                    @endif


                                    <div class="md:col-span-2 pt-2 print:block print-row">
                                        <span class="text-sm font-medium text-gray-500 print-label">Subtotal:</span>
                                        <span class="font-medium text-red-600 print-value">₱{{
                                            number_format($subtotal, 2) }}</span>
                                        <div class="divider my-2 print:hidden"></div>
                                    </div>
                                </div>
                                @if($guestsForFacility->count() > 0)
                                <table class="w-full border-collapse">
                                    @foreach($guestsForFacility as $guest)
                                    <tr>
                                        <td class="p-2 border-b border-gray-200 guest-type">{{
                                            $guest['type'] }}</td>
                                        <td class="p-2 border-b border-gray-200 text-right guest-quantity">
                                            {{ $guest['quantity'] }} guest(s)</td>
                                    </tr>
                                    @endforeach
                                    <tr class="guest-total">
                                        <td class="p-2 font-semibold">Total Guests</td>
                                        <td class="p-2 text-right font-semibold">{{
                                            $guestsForFacility->sum('quantity') }}</td>
                                    </tr>
                                </table>
                                @else
                                <p class="text-gray-500 italic">No guest details recorded</p>
                                @endif
                            </div>
                            @endif
                            @endforeach

                            <div
                                class="flex justify-between items-center mt-6 pt-4 border-t-2 border-gray-200 print-row">
                                <span class="text-lg font-bold text-gray-700 print-label">Total
                                    Amount:</span>
                                <span class="text-xl font-bold text-red-600 print-value">₱{{
                                    number_format($totalAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="footer bg-gray-50 p-6 text-center text-gray-500 text-sm">
                        <div class="divider mb-4"></div>
                        <p class="font-medium">Thank you for choosing our services!</p>
                        <p>We look forward to serving you.</p>
                        <p class="mt-2 text-xs">For any assistance, please contact our front desk.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Payment Status -->
            <div class="md:w-1/3">
                <div class="rounded-xl border border-gray-200 overflow-hidden sticky top-4 print-border">
                    <div class="header-gradient text-white p-4 text-center">
                        <h2 class="text-xl font-bold">Payment Status</h2>
                    </div>

                    <div class="p-6">
                        <!-- Payment Summary -->
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                        clip-rule="evenodd" />
                                </svg>

                                <h3 class="text-lg font-semibold text-gray-800">Payment Summary</h3>
                            </div>

                            <div class="space-y-3">
                                <div class="payment-row">
                                    <span class="payment-label">Total Amount:</span>
                                    <span class="payment-value">₱{{ number_format($totalAmount, 2)
                                        }}</span>
                                </div>

                                <!-- Calculate advance paid and checkin paid -->
                                @php
                                // Calculate payments
                                $advancePaid = $payment->amount;
                                $checkinPaid = $payment->checkin_paid;
                                $totalPaid = $advancePaid + $checkinPaid;
                                @endphp

                                <div class="payment-row">
                                    <span class="payment-label">Advance Paid:</span>
                                    <span class="payment-value text-green-600">₱{{
                                        number_format($advancePaid, 2) }}</span>
                                </div>

                                @if($checkinPaid > 0)
                                <div class="payment-row">
                                    <span class="payment-label">Paid at Check-in:</span>
                                    <span class="payment-value text-green-600">₱{{
                                        number_format($checkinPaid, 2) }}</span>
                                </div>
                                @endif

                                <div class="payment-row" style="font-weight: bold;">
                                    <span class="payment-label">Total Paid:</span>
                                    <span class="payment-value text-green-600">₱{{
                                        number_format($totalPaid, 2) }}</span>
                                </div>

                                <div
                                    class="payment-row pt-4 mt-4 border-t-2 border-gray-300 flex items-center justify-between bg-gray-50 rounded-xl p-4 shadow-sm">
                                    <span class="payment-label text-lg font-semibold text-gray-700 flex items-center">
                                        Balance Status
                                    </span>
                                    <span class="payment-value text-2xl font-extrabold text-green-600">
                                        FULLY PAID
                                    </span>
                                </div>

                            </div>
                        </div>

                        <!-- Status Display -->
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800">Payment Status</h3>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-green-800 font-medium">All payments have been
                                        settled. No balance remaining.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div>
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800">Advance Payment Details
                                </h3>
                            </div>

                            <div class="space-y-3">
                                <div class="payment-row">
                                    <span class="payment-label">Method:</span>
                                    <span class="payment-value">{{ ucfirst($payment->method) }}</span>
                                </div>
                                <div class="payment-row">
                                    <span class="payment-label">Amount:</span>
                                    <span class="payment-value">₱{{ ucfirst(number_format($payment->amount)) }}</span>
                                </div>
                                <div class="payment-row">
                                    <span class="payment-label">Payment Date:</span>
                                    <span class="payment-value">{{
                                        \Carbon\Carbon::parse($payment->payment_date)->format('F j,
                                        Y \a\t g:i A') }}</span>
                                </div>

                                @if($payment->reference_no)
                                <div class="payment-row">
                                    <span class="payment-label">Reference:</span>
                                    <span class="payment-value">{{ $payment->reference_no }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Print Confirmation Modal -->
    <div id="printConfirmModal" class="modal-overlay">
        <div class="modal-container">
            <h2 class="modal-title">Did the receipt print successfully?</h2>
            <p class="text-gray-600">Please confirm if the receipt was printed correctly.</p>
            <div class="modal-buttons">
                <button id="printNoBtn" class="modal-button modal-button-no">No, try again</button>
                <button id="printYesBtn" class="modal-button modal-button-yes">
                    <span id="yesText">Yes, successful</span>
                    <span id="loadingSpinner" class="spinner hidden"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Get elements
        const printConfirmModal = document.getElementById('printConfirmModal');
        const printNoBtn = document.getElementById('printNoBtn');
        const printYesBtn = document.getElementById('printYesBtn');
        const yesText = document.getElementById('yesText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        // Payment ID from your template
        const paymentId = "{{ $payment->id }}";
        
        // Function to show the print confirmation modal
        function showPrintConfirmModal() {
            printConfirmModal.classList.add('active');
        }
        
        // Function to hide the print confirmation modal
        function hidePrintConfirmModal() {
            printConfirmModal.classList.remove('active');
        }
        
        // Function to handle printing
        function handlePrint() {
            window.print();
            
            // Show confirmation modal after a short delay to allow print dialog to appear
            setTimeout(showPrintConfirmModal, 500);
        }
        
        // Function to update booking status
        async function updateBookingStatus() {
            try {
                // Show loading state
                yesText.classList.add('hidden');
                loadingSpinner.classList.remove('hidden');
                printYesBtn.disabled = true;
                
                // Send request to update booking status
                const response = await fetch(`/update/booking/checkout/status/${paymentId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'completed'
                    })
                });
                
                if (response.ok) {
                    // Success - show message and close modal after a delay
                    yesText.textContent = 'Success!';
                    yesText.classList.remove('hidden');
                    loadingSpinner.classList.add('hidden');
                    
                    setTimeout(() => {
                        hidePrintConfirmModal();
                        // Reset button text for next time
                        setTimeout(() => {
                            yesText.textContent = 'Yes, successful';
                            printYesBtn.disabled = false;
                        }, 300);
                    }, 1500);
                } else {
                    throw new Error('Failed to update status');
                }
            } catch (error) {
                console.error('Error updating booking status:', error);
                alert('Failed to update booking status. Please try again.');
                
                // Reset button state
                yesText.textContent = 'Yes, successful';
                yesText.classList.remove('hidden');
                loadingSpinner.classList.add('hidden');
                printYesBtn.disabled = false;
            }
        }
        
        // Event listeners
        printNoBtn.addEventListener('click', () => {
            hidePrintConfirmModal();
        });
        
        printYesBtn.addEventListener('click', updateBookingStatus);
        
        // Close modal when clicking outside
        printConfirmModal.addEventListener('click', (e) => {
            if (e.target === printConfirmModal) {
                hidePrintConfirmModal();
            }
        });
    </script>
</body>

</html>