<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Receipt - {{ $payment->bookingLog->reference }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                extend: {
                    colors: {
                        primary: {
                        light: '#fee2e2',  // lighter red
                        DEFAULT: '#ef4444',  // standard red (was #B22222)
                        dark: '#dc2626',  // darker red 
                        },
                        secondary: {
                            DEFAULT: '#10B981',  // Emerald green for accents
                        }
                    }
                }
            }
        }
    </script>
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
            .qr-code {
                width: 80px !important;
                height: 80px !important;
            }
            .container {
                padding: 0 !important;
            }
            .shadow-md, .shadow-lg {
                box-shadow: none !important;
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
            }
            .print-value {
                display: inline;
                font-weight: 500;
                color: #1f2937;
            }
            .print-row {
                margin-bottom: 0.5rem;
            }
        }
        .receipt-container {
            background: linear-gradient(to bottom, #f8fafc, #ffffff);
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
            background-color: #f8fafc;
            border-left: 4px solid #f63b3b;
        }
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease-out;
        }
        .toast-success {
            background-color: #10B981;
        }
        .toast-error {
            background-color: #EF4444;
        }
        .toast-info {
            background-color: #3B82F6;
        }
        .fade-out {
            animation: fadeOut 0.5s ease-out;
            opacity: 0;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
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
    </style>
</head>
<body class="bg-gray-200 font-sans">
    <div class="container mx-auto max-w-6xl p-4">
        <!-- Print Button -->
        <div class="no-print text-center mb-4">
            <button onclick="window.print()" class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        
        <!-- Main Content -->
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Left Side - Receipt -->
            <div class="md:w-2/3">
                <div class="receipt-container bg-white rounded-xl shadow-lg overflow-hidden relative print-border">
                    <!-- Watermark -->
                    <div class="watermark hidden md:block top-1/4 left-1/4">RECEIPT</div>
                    
                    <!-- Header -->
                    <div class="header-gradient text-white p-6 text-center relative">
                        <div class="absolute top-4 left-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 opacity-20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold mb-1">CHECK-IN RECEIPT</h1>
                        <p class="text-primary-100 opacity-90 text-sm">Booking Reference: {{ $payment->bookingLog->reference }}</p>
                        <p class="text-primary-100 opacity-90 text-sm">
                            Check-in Date:
                            {{ $payment->bookingLog->checked_in_at ? \Carbon\Carbon::parse($payment->bookingLog->checked_in_at)->format('F j, Y \a\t g:i A') : 'N/A' }}
                        </p>
                    
                    
                    </div>
                    
                    <div class="p-6 relative z-10">
                        <!-- Guest Information -->
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="bg-primary-100 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-800">Guest Information</h2>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print:block">
                                <div class="bg-gray-50 p-3 rounded-lg print:bg-transparent print:p-0 print:border-0 print-row">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Full name:</span>
                                    <span class="font-medium text-gray-800 print-value">{{ $payment->bookingLog->user->firstname }} {{ $payment->bookingLog->user->lastname }}</span>
                                </div>
                                
                                <div class="bg-gray-50 p-3 rounded-lg print:bg-transparent print:p-0 print:border-0 print-row">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Email:</span>
                                    <span class="font-medium text-gray-800 print-value">{{ $payment->bookingLog->user->email }}</span>
                                </div>
                                
                                <div class="bg-gray-50 p-3 rounded-lg print:bg-transparent print:p-0 print:border-0 print-row">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Phone:</span>
                                    <span class="font-medium text-gray-800 print-value">{{ $payment->bookingLog->user->phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Summary -->
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div class="bg-primary-100 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-800">Booking Summary</h2>
                            </div>
                            
                            @php 
                                $totalAmount = 0;
                            @endphp
                            
                            @foreach($payment->bookingLog->summaries as $summary)
                                @php
                                    $facility = $summary->facility;
                                    $bookingDetail = $summary->bookingDetails->first();
                                    
                                    if ($bookingDetail) {
                                        $breakfastPrice = $summary->breakfast ? $summary->breakfast->price : 0;
                                        $nights = $bookingDetail->checkin_date->diffInDays($bookingDetail->checkout_date);
                                        $subtotal = ($facility->price + $breakfastPrice) * $nights;
                                        $totalAmount += $subtotal;
                                    }
                                @endphp
                                
                                @if($bookingDetail)
                                    <div class="highlight-box p-4 rounded-lg mb-4 print:border-0 print:p-0">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print:block">
                                            <div class="print-row">
                                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Room Type:</span>
                                                <span class="font-medium text-gray-800 print-value">{{ $facility->name }} (₱{{ number_format($facility->price, 2) }}/night)</span>
                                            </div>
                                            
                                            <div class="print-row">
                                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Room Number:</span>
                                                <span class="font-medium text-gray-800 print-value">{{ $facility->room_number ?? 'Not assigned' }}</span>
                                            </div>
                                            
                                            <div class="print-row">
                                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Check-in:</span>
                                                <span class="font-medium text-gray-800 print-value">
                                                    {{ $bookingDetail->checkin_date->format('F j, Y g:i A') }}
                                                </span>
                                            </div>
                                            
                                            <div class="print-row">
                                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Check-out:</span>
                                                <span class="font-medium text-gray-800 print-value">
                                                    {{ $bookingDetail->checkout_date->format('F j, Y g:i A') }}
                                                </span>
                                            </div>
                                            
                                            <div class="print-row">
                                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Duration:</span>
                                                <span class="font-medium text-gray-800 print-value">
                                                    {{ $nights }} night(s)
                                                </span>
                                            </div>

                                            @if($summary->breakfast)
                                            <div class="print-row">
                                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Breakfast:</span>
                                                <span class="font-medium text-gray-800 print-value">₱{{ number_format($summary->breakfast->price * $nights) }}/morning(s)</span>
                                            </div>
                                            @endif

                                            <div class="md:col-span-2 pt-2 print:block print-row">
                                                <span class="text-sm font-medium text-gray-500 print-label">Subtotal:</span>
                                                <span class="font-medium text-primary-600 print-value">₱{{ number_format($subtotal, 2) }}</span>
                                                <div class="divider my-2 print:hidden"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            
                            <div class="flex justify-between items-center mt-6 pt-4 border-t-2 border-gray-200 print-row">
                                <span class="text-lg font-bold text-gray-700 print-label">Total Amount:</span>
                                <span class="text-xl font-bold text-primary-600 print-value">₱{{ number_format($totalAmount, 2) }}</span>
                            </div>
                        </div>

                        <!-- QR Code -->
                        @if(isset($qrCodeUrl))
                        <div class="text-center bg-white p-4 rounded-xl border border-gray-200 mt-8">
                            <div class="flex flex-col items-center">
                                <img src="{{ $qrCodeUrl }}" alt="Verification QR Code" class="w-32 h-32 mb-3 border-4 border-primary-100 rounded-lg">
                                <p class="text-sm text-gray-600 max-w-xs">Present this QR code when claiming your reservation</p>
                            </div>
                        </div>
                        @endif
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
                <div class="bg-white rounded-xl shadow-md overflow-hidden sticky top-4 print-border">
                    <div class="header-gradient text-white p-4 text-center">
                        <h2 class="text-xl font-bold">Payment Status</h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Payment Summary -->
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800">Payment Summary</h3>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between items-center print-row">
                                    <span class="text-gray-600 print-label">Total Amount:</span>
                                    <span class="font-medium print-value">₱{{ number_format($totalAmount, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center print-row">
                                    <span class="text-gray-600 print-label">Advance Paid:</span>
                                    <span class="font-medium text-green-600 print-value">₱{{ number_format($payment->amount_paid, 2) }}</span>
                                </div>

                                @if($payment->checkin_paid > 0)
                                <div class="flex justify-between items-center print-row">
                                    <span class="text-gray-600 print-label">Paid at Check-in:</span>
                                    <span class="font-medium text-green-600 print-value">₱{{ number_format($payment->checkin_paid, 2) }}</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200 print-row">
                                    <span class="text-gray-700 font-semibold print-label">Remaining Amount:</span>
                                    <span class="font-bold text-primary-600 print-value">₱{{ number_format(($totalAmount - $payment->amount_paid - $payment->checkin_paid), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Control -->
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800">Update Status</h3>
                            </div>
                            
                            <div class="flex items-center justify-between mb-3 print-row">
                                <span class="text-gray-600 print-label">Remaining Status:</span>
                                <span id="status-display" class="status-badge 
                                    {{ $payment->remaining_balance_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->remaining_balance_status)) }}
                                </span>
                            </div>
                            
                        <div class="no-print">
                            <label for="status-select" class="block text-sm font-medium text-gray-700 mb-1">Change Status</label>
                            <div class="flex flex-col gap-4">
                                <div class="flex gap-2">
                                    <select id="status-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 py-2 px-3 border text-sm">
                                        <option value="pending" {{ $payment->remaining_balance_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="fully_paid" {{ $payment->remaining_balance_status === 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                                    </select>
                                    <button id="save-btn" class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-md transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                        Save
                                    </button>
                                </div>
                                
                                <div id="amount-container" class="hidden">
                                    <label for="paid-amount" class="block text-sm font-medium text-gray-700 mb-1">Paid Amount</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">₱</span>
                                        </div>
                                        <input type="number" id="paid-amount" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md py-2" placeholder="0.00" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        
                        <!-- Payment Details -->
                        <div>
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-800">Advance Payment Details</h3>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="bg-gray-50 p-3 rounded-lg print:bg-transparent print:p-0 print-row">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Method:</span>
                                    <span class="font-medium print-value">{{ ucfirst($payment->method) }}</span>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg print:bg-transparent print:p-0 print-row">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">GCASH NUMBER:</span>
                                    <span class="font-medium print-value">{{ ucfirst($payment->gcash_number) }}</span>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg print:bg-transparent print:p-0 print-row">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Payment Date:</span>
                                    <span class="font-medium print-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y \a\t g:i A') }}</span>
                                </div>
                                
                                @if($payment->reference_no)
                                <div class="bg-gray-50 p-3 rounded-lg print:bg-transparent print:p-0 print-row">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider print-label">Reference No:</span>
                                    <span class="font-medium print-value">{{ $payment->reference_no }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status-select');
            const amountContainer = document.getElementById('amount-container');
            
            statusSelect.addEventListener('change', function() {
                if (this.value === 'fully_paid') {
                    amountContainer.classList.remove('hidden');
                } else {
                    amountContainer.classList.add('hidden');
                }
            });
            // Initialize visibility based on current selection
            if (statusSelect.value === 'fully_paid') {
                amountContainer.classList.remove('hidden');
            }

            const saveBtn = document.getElementById('save-btn');
            const originalStatus = statusSelect.value;
            
            // Set original status for tracking changes
            statusSelect.dataset.originalStatus = originalStatus;
            saveBtn.disabled = true;
            
            // Enable save only when status changes
            statusSelect.addEventListener('change', function () {
                saveBtn.disabled = (this.value === this.dataset.originalStatus);
            });
            
            saveBtn.addEventListener('click', function () {
                const newStatus = statusSelect.value;
                const paymentId = {{ $payment->id }};
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                let paidAmount = 0;
                
                // Validation: require paid amount when status is "fully_paid"
                if (newStatus === 'fully_paid') {
                    const paidAmountInput = document.getElementById('paid-amount');
                    paidAmount = parseFloat(paidAmountInput.value.trim());
                    if (!paidAmount || isNaN(paidAmount) || parseFloat(paidAmount) <= 0) {
                        showToast('Please enter a valid paid amount.', 'error');
                        paidAmountInput.focus();
                        return;
                    }
                    
                    // Validate that paid amount matches remaining balance
                    const remainingBalance = parseFloat({{ $totalAmount - $payment->amount_paid - $payment->checkin_paid }});
                    if (paidAmount < remainingBalance) {
                        showToast(`Amount paid (₱${paidAmount.toFixed(2)}) is less than remaining balance (₱${remainingBalance.toFixed(2)})`, 'error');
                        return;
                    }
                }
                
                // Button UI feedback
                const originalText = saveBtn.textContent;
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';

                fetch(`/payments/${paymentId}/update-remaining-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        remaining_status: newStatus,
                        amount_paid: paidAmount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusSelect.dataset.originalStatus = newStatus;
                        saveBtn.disabled = true;

                        // Update UI
                        const statusDisplay = document.getElementById('status-display');
                        if (statusDisplay) {
                            const displayText = newStatus.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
                            statusDisplay.textContent = displayText;
                            
                            // Update status color
                            statusDisplay.className = 'status-badge ' + 
                                (newStatus === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                        }
                        
                        showToast('Status updated successfully!', 'success');
                        
                        // Reload page after 2 seconds to reflect changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showToast(data.message || 'Failed to update status.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Request failed:', error);
                    showToast('Error updating status. Please try again.', 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.textContent = originalText;
                });
            });
            
            // Toast function
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.add('fade-out');
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>