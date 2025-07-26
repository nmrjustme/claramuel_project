<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Receipt - {{ $payment->bookingLog->reference }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#fecaca',
                            DEFAULT: '#ef4444',
                            dark: '#b91c1c',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .status-select, .save-btn {
                display: none;
            }
        }
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 4px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        }
        .toast-success {
            background-color: #4CAF50;
        }
        .toast-error {
            background-color: #f44336;
        }
        .toast-info {
            background-color: #2196F3;
        }
        .fade-out {
            animation: fadeOut 0.5s ease-out;
            opacity: 0;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto max-w-6xl p-4">
        <!-- Print Button -->
        <div class="no-print text-center mb-6">
            <button onclick="window.print()" class="bg-green-500 hover:bg-green-400 text-white font-bold py-2 px-4 rounded transition duration-200">
                Print Receipt
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Left Side - Receipt -->
            <div class="md:w-2/3 bg-white rounded-lg shadow-md overflow-hidden">
                <div class="header bg-primary-500 text-dark p-6 text-center">
                    <h1 class="text-2xl font-bold mb-2">Check-in Receipt</h1>
                    <p class="text-primary-100">Booking Reference: {{ $payment->bookingLog->reference }}</p>
                    <p class="text-primary-100">Check-in Date: {{ now()->format('F j, Y \a\t g:i A') }}</p>
                </div>
                
                <div class="p-6">
                    <!-- Guest Information -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold border-b border-gray-200 pb-2 mb-4 text-primary-600">Guest Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Full Name</p>
                                <p class="font-medium">{{ $payment->bookingLog->user->firstname }} {{ $payment->bookingLog->user->lastname }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="font-medium">{{ $payment->bookingLog->user->email }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm font-medium text-gray-500">Phone</p>
                                <p class="font-medium">{{ $payment->bookingLog->user->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Summary -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold border-b border-gray-200 pb-2 mb-4 text-primary-600">Booking Summary</h2>
                        
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
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Room Type</p>
                                            <p class="font-medium">{{ $facility->name }} (₱{{ number_format($facility->price, 2) }}/night)</p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Room Number</p>
                                            <p class="font-medium">{{ $facility->room_number ?? 'Not assigned' }}</p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Check-in</p>
                                            <p class="font-medium">
                                                {{ $bookingDetail->checkin_date->format('F j, Y g:i A') }}
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Check-out</p>
                                            <p class="font-medium">
                                                {{ $bookingDetail->checkout_date->format('F j, Y g:i A') }}
                                            </p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Duration</p>
                                            <p class="font-medium">
                                                {{ $nights }} night(s)
                                            </p>
                                        </div>

                                        @if($summary->breakfast)
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Breakfast</p>
                                            <p class="font-medium">₱{{ number_format($summary->breakfast->price * $nights) }}/morning(s)</p>
                                        </div>
                                        @endif

                                        <div class="md:col-span-2">
                                            <p class="text-sm font-medium text-gray-500">Subtotal</p>
                                            <p class="font-medium">₱{{ number_format($subtotal, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        
                        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                            <p class="text-lg font-semibold">Total Amount:</p>
                            <p class="text-xl font-bold text-primary-600">₱{{ number_format($totalAmount, 2) }}</p>
                        </div>
                    </div>

                    <!-- QR Code -->
                    @if(isset($qrCodeUrl))
                    <div class="text-center bg-white p-4 rounded-lg border border-gray-200 mt-8">
                        <img src="{{ $qrCodeUrl }}" alt="Verification QR Code" class="w-32 h-32 mx-auto mb-2">
                        <p class="text-sm text-gray-600">Present this QR code when claiming your reservation</p>
                    </div>
                    @endif
                </div>
                
                <div class="footer bg-gray-50 p-6 text-center text-gray-500 text-sm">
                    <p>Thank you for choosing our services!</p>
                    <p>We look forward to serving you.</p>
                    <p>For any assistance, please contact our front desk.</p>
                </div>
            </div>

            <!-- Right Side - Payment Status -->
            <div class="md:w-1/3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-4">
                    <div class="bg-primary-500 text-dark p-4 text-center">
                        <h2 class="text-xl font-bold">Payment Status</h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Payment Summary -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold border-b border-gray-200 pb-2 mb-4 text-primary-600">Payment Summary</h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Amount:</span>
                                    <span class="font-medium">₱{{ number_format($totalAmount, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Amount Paid:</span>
                                    <span class="font-medium">₱{{ number_format($payment->amount_paid, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between pt-3 border-t border-gray-200">
                                    <span class="text-gray-600 font-semibold">Remaining Amount:</span>
                                    <span class="font-bold text-primary-600">₱{{ number_format(($totalAmount - $payment->amount_paid), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Control -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold border-b border-gray-200 pb-2 mb-4 text-primary-600">Update Status</h3>
                            
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-600">Current Status:</span>
                                <span id="status-display" class="px-2 py-1 rounded text-sm font-medium 
                                    {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                </span>
                            </div>
                            
                            <div class="no-print">
                                <label for="status-select" class="block text-sm font-medium text-gray-700 mb-1">Change Status</label>
                                <div class="flex gap-2">
                                    <select id="status-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 py-2 px-3 border">
                                        <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="fully_paid" {{ $payment->status === 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                                    </select>
                                    <button id="save-btn" class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-md transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Details -->
                        <div>
                            <h3 class="text-lg font-semibold border-b border-gray-200 pb-2 mb-4 text-primary-600">Payment Details</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Method</p>
                                    <p class="font-medium">{{ ucfirst($payment->method) }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500">Payment Date</p>
                                    <p class="font-medium">{{ $payment->verified_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                                
                                @if($payment->reference_no)
                                <div>
                                    <p class="text-sm text-gray-500">Reference No</p>
                                    <p class="font-medium">{{ $payment->reference_no }}</p>
                                </div>
                                @endif
                                
                                <div>
                                    <p class="text-sm text-gray-500">Verification Status</p>
                                    <p class="font-medium">Verified</p>
                                </div>
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
                        remaining_status: newStatus
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
                            statusDisplay.className = 'px-2 py-1 rounded text-sm font-medium ' + 
                                (newStatus === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                        }
                        
                        showToast('Status updated successfully!', 'success');
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