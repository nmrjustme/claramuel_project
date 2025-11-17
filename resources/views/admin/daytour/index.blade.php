@extends('layouts.admin')
@section('title', 'Day Tour Registration')
@php
    $active = 'day_tour';
@endphp

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<div class="p-6 bg-white rounded-xl border border-gray-100 shadow-sm h-full">
    <div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Day Tour Registration</h2>
            <p class="text-sm text-gray-500">Register for a day tour by filling out the form below.</p>
        </div>

        <!-- Button to go to Day Tour Logs -->
        <a href="{{ route('admin.daytour.logs') }}" 
           class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg shadow transition duration-200">
            <i class="fas fa-list-alt mr-2"></i>
            View Day Tour Logs
        </a>
    </div>

    @if(session('success'))
    <div id="flash-message" class="mt-4 p-4 rounded-lg bg-green-100 text-green-800 font-medium shadow-md transition transform duration-500">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div id="flash-message" class="mt-4 p-4 rounded-lg bg-red-100 text-red-800 font-medium shadow-md transition transform duration-500">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div id="flash-message" class="mt-4 p-4 rounded-lg bg-red-100 text-red-800 font-medium shadow-md transition transform duration-500">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <!-- Registration Form -->

    <form method="POST" action="{{ route('admin.daytour.store') }}" class="space-y-8" id="dayTourForm">
        @csrf
        
        <!-- Guest Information Section -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
            <div class="flex items-center space-x-3 mb-6">
                
                <div class="p-3 bg-red-100 rounded-xl shadow-sm">
                    <i class="fas fa-user-circle text-red-500 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Guest Information</h3>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                    <label for="customerFirstName" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-user text-red-400 mr-2 text-sm"></i>
                        First Name
                    </label>
                    <input type="text" id="customerFirstName" name="first_name" placeholder="First Name" required
                        class="w-full px-4 py-3 pl-11 rounded-xl border-2 border-gray-100 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all duration-200 shadow-sm">
                </div>
                
                <!-- Last Name -->
                <div>
                    <label for="customerLastName" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-user text-red-400 mr-2 text-sm"></i>
                        Last Name
                    </label>
                    <input type="text" id="customerLastName" name="last_name" placeholder="Last Name" required
                        class="w-full px-4 py-3 pl-11 rounded-xl border-2 border-gray-100 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all duration-200 shadow-sm">
                </div>
                
                <!-- Contact Number -->
                <div>
                    <label for="contactNumber" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-phone text-blue-400 mr-2 text-sm"></i>
                        Contact Number
                    </label>
                    <input type="tel" id="contactNumber" name="phone" placeholder="Contact Number" required
                        class="w-full px-4 py-3 pl-11 rounded-xl border-2 border-gray-100 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition-all duration-200 shadow-sm">
                </div>
                
                <!-- Tour Date with Custom Calendar -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-calendar-day text-red-400 mr-2 text-sm"></i>
                        Tour Date
                    </label>
                    <div class="relative group">
                        <input type="text" id="walkinDateInput" readonly placeholder="Select date"
                            class="w-full px-4 py-3 pl-11 rounded-xl border-2 border-gray-100 focus:border-red-400 focus:ring-2 focus:ring-red-100 cursor-pointer bg-white shadow-sm transition-all duration-200"
                            onclick="toggleDatePicker()">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        </div>
                    </div>
                    <input type="hidden" id="date_tour" name="date_tour" value="{{ now()->format('Y-m-d') }}">
                    <p class="text-xs text-gray-500 mt-2 flex items-center">
                        <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                        Available for today and future dates only
                    </p>

                    <!-- Custom Date Picker -->
                    <div id="datePicker" class="hidden absolute z-50 bg-white rounded-2xl border border-gray-200 shadow-xl p-6 mt-2 w-full max-w-md">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-lg font-semibold text-gray-800" id="currentMonth"></h4>
                            <div class="flex space-x-2">
                                <button type="button" data-action="prev-month" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors shadow-sm">
                                    <i class="fas fa-chevron-left text-gray-600 text-sm"></i>
                                </button>
                                <button type="button" data-action="next-month" class="p-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors shadow-sm">
                                    <i class="fas fa-chevron-right text-gray-600 text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-2 mb-3">
                            <div class="text-center text-sm font-semibold text-red-500 py-2">Sun</div>
                            <div class="text-center text-sm font-semibold text-gray-600 py-2">Mon</div>
                            <div class="text-center text-sm font-semibold text-gray-600 py-2">Tue</div>
                            <div class="text-center text-sm font-semibold text-gray-600 py-2">Wed</div>
                            <div class="text-center text-sm font-semibold text-gray-600 py-2">Thu</div>
                            <div class="text-center text-sm font-semibold text-gray-600 py-2">Fri</div>
                            <div class="text-center text-sm font-semibold text-blue-500 py-2">Sat</div>
                        </div>

                        <div id="calendarGrid" class="grid grid-cols-7 gap-2">
                            <!-- Calendar days will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
                
                <!-- Email -->
                <div>
                    <label for="customerEmail" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-envelope text-green-400 mr-2 text-sm"></i>
                        Email (Optional)
                    </label>
                    <input type="email" id="customerEmail" name="email" placeholder="example@email.com"
                        class="w-full px-4 py-3 pl-11 rounded-xl border-2 border-gray-100 focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all duration-200 shadow-sm">
                </div>
                
                <!-- Status -->
                <div>
                    <label for="reservation_status" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-tag text-purple-400 mr-2 text-sm"></i>
                        Status
                    </label>
                    <select id="reservation_status" name="reservation_status" required
                        class="w-full px-4 py-3 pl-11 rounded-xl border-2 border-gray-100 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all duration-200 shadow-sm appearance-none">
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Service Selection Section -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-3 bg-blue-100 rounded-xl shadow-sm">
                    <i class="fas fa-bell text-blue-500 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Service Selection</h3>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-4">Service Type</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Pool Option -->
                    <div class="service-option" data-value="pool">
                        <input type="radio" id="servicePool" name="service_type" value="pool" class="hidden" checked>
                        <label for="servicePool" class="flex flex-col items-center p-6 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-blue-300 hover:shadow-lg transition-all duration-200 service-label">
                            <div class="p-3 bg-blue-100 rounded-xl mb-3">
                                <i class="fas fa-swimming-pool text-2xl text-blue-500"></i>
                            </div>
                            <span class="font-semibold text-gray-800">Pool Access</span>
                        </label>
                    </div>
                    
                    <!-- Park Option -->
                    <div class="service-option" data-value="themed_park">
                        <input type="radio" id="servicePark" name="service_type" value="themed_park" class="hidden">
                        <label for="servicePark" class="flex flex-col items-center p-6 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-green-300 hover:shadow-lg transition-all duration-200 service-label">
                            <div class="p-3 bg-green-100 rounded-xl mb-3">
                                <i class="fas fa-tree text-2xl text-green-500"></i>
                            </div>
                            <span class="font-semibold text-gray-800">Themed Park</span>
                        </label>
                    </div>
                    
                    <!-- Both Option -->
                    <div class="service-option" data-value="both">
                        <input type="radio" id="serviceBoth" name="service_type" value="both" class="hidden">
                        <label for="serviceBoth" class="flex flex-col items-center p-6 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-purple-600 hover:shadow-lg transition-all duration-200 service-label">
                            <div class="p-3 bg-purple-100 rounded-xl mb-3">
                                <i class="fas fa-ticket text-2xl text-purple-500"></i>
                                <i class="fas fa-swimming-pool text-2xl text-purple-500"></i>
                            </div>
                            <span class="font-semibold text-gray-800">Both</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Pool Access Fields -->
            <div id="poolAccessFields" class="mt-8 p-6 bg-blue-50 rounded-2xl border border-blue-100">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-swimming-pool text-blue-500 mr-2"></i>
                    Pool Access Guests
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($guestTypes->where('location', 'Pool') as $type)
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ $type->type }} 
                                <span class="text-blue-500">₱{{ number_format($type->rate, 2) }}</span>
                            </label>
                            <input type="number" name="pool_{{ strtolower($type->type) }}" value="0" min="0"
                                class="w-full px-3 py-2 border-2 border-gray-100 rounded-lg focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all duration-200 guest-counter" >
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Themed Park Fields -->
            <div id="themedParkFields" class="mt-8 p-6 bg-green-50 rounded-2xl border border-green-100 hidden">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-tree text-green-500 mr-2"></i>
                    Themed Park Guests
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($guestTypes->where('location', 'Park') as $type)
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ $type->type }} 
                                <span class="text-green-500">₱{{ number_format($type->rate, 2) }}</span>
                            </label>
                            <input type="number" name="park_{{ strtolower($type->type) }}" value="0" min="0" 
                                class="w-full px-3 py-2 border-2 border-gray-100 rounded-lg focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all duration-200 guest-counter">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

<!-- Accommodations Section -->
<div id="accommodationSection" class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="p-3 bg-amber-100 rounded-xl shadow-sm">
                <i class="fas fa-home text-amber-500 text-xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800">Accommodations</h3>
        </div>
        <!-- Validation Indicator -->
        <div id="accommodationValidation" class="hidden flex items-center space-x-2 px-3 py-2 bg-red-50 border border-red-200 rounded-lg">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span class="text-sm text-red-700 font-medium">Please select at least one accommodation</span>
        </div>
    </div>

    <!-- Cottages -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-umbrella-beach text-amber-500 mr-2"></i>
            Cottages
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($cottages as $cottage)
                <div class="accommodation-item flex items-center justify-between p-5 bg-gray-50 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800">{{ $cottage->name }}</span>
                        <div class="price-display" data-id="{{ $cottage->id }}">
                            @if($cottage->has_discount)
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-400 line-through">₱{{ number_format($cottage->original_price, 2) }}</span>
                                    <span class="text-sm text-green-600 font-semibold">₱{{ number_format($cottage->price, 2) }}</span>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                        Save ₱{{ number_format($cottage->discount_amount, 2) }}
                                    </span>
                                </div>
                            @else
                                <span class="text-sm text-gray-600">₱{{ number_format($cottage->price, 2) }}</span>
                            @endif
                        </div>
                        <div class="availability-text text-xs text-gray-500" data-id="{{ $cottage->id }}">
                            Availability: Loading...
                        </div>
                    </div>
                    <input type="number" name="accommodations[{{ $cottage->id }}]" min="0" value="0"
                        data-original-price="{{ $cottage->original_price }}"
                        data-price="{{ $cottage->price }}"
                        data-has-discount="{{ $cottage->has_discount ? 'true' : 'false' }}"
                        class="accommodation-input w-20 px-3 py-2 text-center rounded-lg border-2 border-gray-100 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all duration-200">
                </div>
            @endforeach
        </div>
    </div>

    <!-- Villas -->
    <div>
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-crown text-purple-500 mr-2"></i>
            Private Villas
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($villas as $villa)
                <div class="accommodation-item flex items-center justify-between p-5 bg-gray-50 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-800">{{ $villa->name }}</span>
                        <div class="price-display" data-id="{{ $villa->id }}">
                            @if($villa->has_discount)
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-400 line-through">₱{{ number_format($villa->original_price, 2) }}</span>
                                    <span class="text-sm text-green-600 font-semibold">₱{{ number_format($villa->price, 2) }}</span>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                        Save ₱{{ number_format($villa->discount_amount, 2) }}
                                    </span>
                                </div>
                            @else
                                <span class="text-sm text-gray-600">₱{{ number_format($villa->price, 2) }}</span>
                            @endif
                        </div>
                        <div class="availability-text text-xs text-gray-500" data-id="{{ $villa->id }}">
                            Availability: Loading...
                        </div>
                    </div>
                    <input type="number" name="accommodations[{{ $villa->id }}]" min="0" value="0"
                        data-original-price="{{ $villa->original_price }}"
                        data-price="{{ $villa->price }}"
                        data-has-discount="{{ $villa->has_discount ? 'true' : 'false' }}"
                        class="accommodation-input w-20 px-3 py-2 text-center rounded-lg border-2 border-gray-100 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all duration-200">
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Manual Discount Section -->
<div class="bg-yellow-50 rounded-2xl border border-yellow-100 shadow-lg p-6">
    <div class="flex items-center space-x-3 mb-6">
        <div class="p-3 bg-yellow-100 rounded-xl shadow-sm">
            <i class="fas fa-tag text-yellow-500 text-xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800">Manual Discount</h3>
    </div>
    
    <div class="space-y-4">
        <div class="flex items-center space-x-4">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" id="applyManualDiscount" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                <span class="text-sm font-medium text-gray-700">Apply manual discount</span>
            </label>
        </div>

        <div id="manualDiscountFields" class="hidden space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="discountType" class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label>
                    <select id="discountType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-red-400 focus:ring-2 focus:ring-red-100">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (₱)</option>
                    </select>
                </div>
                
                <div>
                    <label for="discountValue" class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label>
                    <input type="number" id="discountValue" min="0" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-red-400 focus:ring-2 focus:ring-red-100"
                           placeholder="0.00">
                </div>
            </div>
            
            <div>
                <label for="discountReason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Discount</label>
                <input type="text" id="discountReason" name="manual_discount_reason"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-red-400 focus:ring-2 focus:ring-red-100"
                       placeholder="e.g., Special promotion, Staff discount, etc.">
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-yellow-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Manual Discount Applied:</span>
                    <span id="manualDiscountAmount" class="text-lg font-bold text-red-600">₱0.00</span>
                </div>
                <div id="discountBreakdown" class="text-xs text-gray-500 mt-1"></div>
            </div>
        </div>
    </div>
</div>

        <!-- Total Cost Section -->
        <div class="bg-red-50 rounded-2xl border border-red-100 shadow-lg p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-3 bg-red-100 rounded-xl shadow-sm">
                    <i class="fas fa-receipt text-red-500 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Payment Summary</h3>
            </div>
            
            <div class="p-5 bg-white rounded-xl border border-red-200 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-lg font-bold text-gray-800">Total Amount:</p>
                    <p class="text-2xl font-bold text-red-500">₱<span id="totalAmount">0.00</span></p>
                </div>
                <div id="costBreakdown" class="text-sm text-gray-600 space-y-2 mt-4"></div>
            </div>
        </div>

        <!-- Hidden fields for manual discount -->
        <input type="hidden" id="manualDiscountType" name="manual_discount_type" value="">
        <input type="hidden" id="manualDiscountValue" name="manual_discount_value" value="0">
        <input type="hidden" id="manualDiscountAmountField" name="manual_discount_amount" value="0">

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center transform hover:scale-105">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                Complete Registration
            </button>
        </div>
    </form>
</div>

<style>
/* Calendar Styles */
#datePicker {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.calendar-day {
    transition: all 0.2s ease;
}

.calendar-day:hover:not(.disabled) {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.unavailable-date {
    background-color: #fecaca !important;
    color: #dc2626 !important;
    cursor: not-allowed !important;
    opacity: 0.7 !important;
}

.available-date {
    background-color: #d1fae5 !important;
    color: #059669 !important;
    cursor: pointer !important;
}

.selected-date {
    background-color: #3b82f6 !important;
    color: white !important;
    font-weight: bold !important;
}

/* Other styles */
.service-label {
    transition: all 0.2s ease;
}

.service-label:hover {
    transform: translateY(-2px);
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}

.animate-fade { 
    animation: fade 2s ease forwards; 
}

@keyframes fade { 
    from { opacity: 1; } 
    to { opacity: 0; } 
}
</style>

<script>
// Guest rates
const guestRates = {
    pool: {
        adult: {{ $guestTypes->where('location', 'Pool')->where('type', 'Adult')->first()->rate ?? 0 }},
        kids: {{ $guestTypes->where('location', 'Pool')->where('type', 'Kids')->first()->rate ?? 0 }},
        seniors: {{ $guestTypes->where('location', 'Pool')->where('type', 'Seniors')->first()->rate ?? 0 }}
    },
    park: {
        adult: {{ $guestTypes->where('location', 'Park')->where('type', 'Adult')->first()->rate ?? 0 }},
        kids: {{ $guestTypes->where('location', 'Park')->where('type', 'Kids')->first()->rate ?? 0 }},
        seniors: {{ $guestTypes->where('location', 'Park')->where('type', 'Seniors')->first()->rate ?? 0 }}
    }
};

// Manual discount state
let manualDiscount = {
    enabled: false,
    type: 'percentage',
    value: 0,
    amount: 0,
    reason: ''
};

/* =====================
   Calendar functionality
   ===================== */
let currentDate = new Date();
let selectedDate = new Date();

function formatDateDisplay(date) {
    return date.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', year:'numeric' });
}
function formatDateForInput(date) {
    return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
}

function toggleDatePicker() {
    const picker = document.getElementById('datePicker');
    picker.classList.toggle('hidden');
    if (!picker.classList.contains('hidden')) {
        currentDate = new Date();
        renderCalendar();
    }
}
function changeMonth(direction) {
    currentDate.setMonth(currentDate.getMonth() + direction);
    renderCalendar();
}
function renderCalendar() {
    const calendarGrid = document.getElementById('calendarGrid');
    const currentMonthElement = document.getElementById('currentMonth');
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    currentMonthElement.textContent = `${monthNames[month]} ${year}`;
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    calendarGrid.innerHTML = '';

    for (let i=0;i<firstDay;i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'text-center text-sm text-gray-400 py-2';
        calendarGrid.appendChild(emptyCell);
    }

    const today = new Date(); today.setHours(0,0,0,0);
    for (let d=1; d<=daysInMonth; d++){
        const date = new Date(year, month, d);
        const cell = document.createElement('div');
        cell.className = 'text-center text-sm py-2 rounded-lg transition-all duration-200 calendar-day';
        const isSelected = selectedDate && d===selectedDate.getDate() && month===selectedDate.getMonth() && year===selectedDate.getFullYear();
        if (date < today) {
            cell.classList.add('unavailable-date');
        } else if (isSelected) {
            cell.classList.add('selected-date');
        } else {
            cell.classList.add('available-date');
            cell.onclick = function() { selectDate(date); };
        }
        cell.textContent = d;
        calendarGrid.appendChild(cell);
    }
}
function selectDate(date) {
    const today = new Date(); today.setHours(0,0,0,0);
    if (date < today) return;

    selectedDate = date;
    document.getElementById('walkinDateInput').value = formatDateDisplay(date);
    document.getElementById('date_tour').value = formatDateForInput(date);

    updateAvailabilityAndPrices();
    renderCalendar();
    setTimeout(() => document.getElementById('datePicker').classList.add('hidden'), 300);
}
document.addEventListener('click', e => {
    const picker = document.getElementById('datePicker');
    if (!picker.contains(e.target) && e.target !== document.getElementById('walkinDateInput')) {
        picker.classList.add('hidden');
    }
});

/* =====================
   Manual Discount Functions
   ===================== */
function toggleManualDiscount() {
    const discountCheckbox = document.getElementById('applyManualDiscount');
    const discountFields = document.getElementById('manualDiscountFields');
    
    manualDiscount.enabled = discountCheckbox.checked;
    
    if (manualDiscount.enabled) {
        discountFields.classList.remove('hidden');
        // Set default values
        manualDiscount.type = document.getElementById('discountType').value;
        manualDiscount.value = parseFloat(document.getElementById('discountValue').value) || 0;
        manualDiscount.reason = document.getElementById('discountReason').value;
    } else {
        discountFields.classList.add('hidden');
        manualDiscount.amount = 0;
        // Clear hidden fields
        document.getElementById('manualDiscountType').value = '';
        document.getElementById('manualDiscountValue').value = '0';
        document.getElementById('manualDiscountAmountField').value = '0';
    }
    
    calculateTotal();
}

function updateManualDiscount() {
    if (!manualDiscount.enabled) return;
    
    manualDiscount.type = document.getElementById('discountType').value;
    manualDiscount.value = parseFloat(document.getElementById('discountValue').value) || 0;
    manualDiscount.reason = document.getElementById('discountReason').value;
    
    // Update hidden fields
    document.getElementById('manualDiscountType').value = manualDiscount.type;
    document.getElementById('manualDiscountValue').value = manualDiscount.value;
    
    calculateTotal();
}

function calculateManualDiscount(subtotal) {
    if (!manualDiscount.enabled || manualDiscount.value <= 0) {
        return 0;
    }
    
    let discountAmount = 0;
    
    if (manualDiscount.type === 'percentage') {
        // Cap percentage at 100%
        const percentage = Math.min(manualDiscount.value, 100);
        discountAmount = (subtotal * percentage) / 100;
    } else {
        // Fixed amount - cap at subtotal
        discountAmount = Math.min(manualDiscount.value, subtotal);
    }
    
    manualDiscount.amount = discountAmount;
    
    // Update display
    document.getElementById('manualDiscountAmount').textContent = `₱${discountAmount.toFixed(2)}`;
    document.getElementById('manualDiscountAmountField').value = discountAmount.toFixed(2);
    
    // Update breakdown
    const breakdownElement = document.getElementById('discountBreakdown');
    if (manualDiscount.type === 'percentage') {
        breakdownElement.textContent = `${manualDiscount.value}% of ₱${subtotal.toFixed(2)}`;
    } else {
        breakdownElement.textContent = `Fixed amount discount`;
    }
    
    return discountAmount;
}

/* =====================
   Availability & Price checker
   ===================== */
async function updateAvailabilityAndPrices() {
    const dateInput = document.getElementById('date_tour');
    const date = dateInput?.value;
    if (!date) return;

    // Show loading for availability
    document.querySelectorAll('.availability-text').forEach(el => {
        el.textContent = 'Availability: Checking…';
        el.classList.remove('text-green-600','text-red-500');
        el.classList.add('text-gray-500');
    });

    try {
        // Fetch both availability and prices in parallel
        const [availabilityRes, pricesRes] = await Promise.all([
            fetch(`/daytour/check-availability?date=${encodeURIComponent(date)}`, {cache:'no-store'}),
            fetch(`/daytour/check-prices?date=${encodeURIComponent(date)}`, {cache:'no-store'})
        ]);

        if (!availabilityRes.ok) throw new Error('Failed to fetch availability');
        if (!pricesRes.ok) throw new Error('Failed to fetch prices');

        const availabilityData = await availabilityRes.json();
        const priceData = await pricesRes.json();

        // Update availability
        availabilityData.forEach(item => {
            const available = Number(item.available ?? 0);

            const el = document.querySelector(`.availability-text[data-id="${item.id}"]`);
            if (el) {
                el.textContent = `Availability: ${available} (${item.status})`;
                el.classList.remove("text-gray-500","text-red-500","text-green-600");
                el.classList.add(item.status === 'Available' ? "text-green-600" : "text-red-500");
            }

            const input = document.querySelector(`input[name="accommodations[${item.id}]"]`);
            if (input) {
                input.max = available;
                input.disabled = available === 0;
                if (parseInt(input.value) > available) input.value = available;
            }
        });

        // Update prices with discounts
        priceData.forEach(item => {
            const input = document.querySelector(`input[name="accommodations[${item.id}]"]`);
            const priceDisplay = document.querySelector(`.price-display[data-id="${item.id}"]`);
            
            if (input && priceDisplay) {
                // Update input data with current prices
                input.dataset.originalPrice = item.price;
                input.dataset.price = item.discounted_price;
                input.dataset.hasDiscount = item.has_discount.toString();
                
                // Update the displayed price
                if (item.has_discount) {
                    priceDisplay.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-400 line-through">₱${parseFloat(item.price).toFixed(2)}</span>
                            <span class="text-sm text-green-600 font-semibold">₱${parseFloat(item.discounted_price).toFixed(2)}</span>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                Save ₱${parseFloat(item.discount_amount).toFixed(2)}
                            </span>
                        </div>
                    `;
                } else {
                    priceDisplay.innerHTML = `<span class="text-sm text-gray-600">₱${parseFloat(item.price).toFixed(2)}</span>`;
                }
            }
        });

        // Recalculate total with updated prices
        calculateTotal();
        
    } catch(err) {
        console.error("Error fetching data:", err);
        document.querySelectorAll('.availability-text').forEach(el => {
            el.textContent = 'Availability: Error';
            el.classList.add('text-red-500');
        });
    }
}

// Keep original function for backward compatibility
async function updateAvailability() {
    await updateAvailabilityAndPrices();
}

// Run on page load
document.addEventListener('DOMContentLoaded', () => {
    updateAvailabilityAndPrices();
});

// Run when date changes
document.getElementById('date_tour')?.addEventListener('change', updateAvailabilityAndPrices);

/* =====================
   Total calculation with discounts
   ===================== */
function calculateTotal() {
    let subtotal = 0;
    let originalTotal = 0;
    let breakdown = [];
    const serviceType = document.querySelector('input[name="service_type"]:checked').value;

    // Pool guests (no discounts for guest types)
    if (serviceType==='pool' || serviceType==='both') {
        const adults = parseInt(document.querySelector('input[name="pool_adult"]').value)||0;
        const kids = parseInt(document.querySelector('input[name="pool_kids"]').value)||0;
        const seniors = parseInt(document.querySelector('input[name="pool_seniors"]').value)||0;
        
        if (adults>0){
            const amt = adults * guestRates.pool.adult; 
            subtotal += amt;
            originalTotal += amt;
            breakdown.push(`Pool Adults: ${adults} × ₱${guestRates.pool.adult} = ₱${amt.toFixed(2)}`);
        }
        if (kids>0){
            const amt = kids * guestRates.pool.kids; 
            subtotal += amt;
            originalTotal += amt;
            breakdown.push(`Pool Kids: ${kids} × ₱${guestRates.pool.kids} = ₱${amt.toFixed(2)}`);
        }
        if (seniors>0){
            const amt = seniors * guestRates.pool.seniors; 
            subtotal += amt;
            originalTotal += amt;
            breakdown.push(`Pool Seniors: ${seniors} × ₱${guestRates.pool.seniors} = ₱${amt.toFixed(2)}`);
        }
    }

    // Park guests (no discounts for guest types)
    if (serviceType==='themed_park' || serviceType==='both') {
        const adults = parseInt(document.querySelector('input[name="park_adult"]').value)||0;
        const kids = parseInt(document.querySelector('input[name="park_kids"]').value)||0;
        const seniors = parseInt(document.querySelector('input[name="park_seniors"]').value)||0;
        
        if (adults>0){
            const amt = adults * guestRates.park.adult; 
            subtotal += amt;
            originalTotal += amt;
            breakdown.push(`Park Adults: ${adults} × ₱${guestRates.park.adult} = ₱${amt.toFixed(2)}`);
        }
        if (kids>0){
            const amt = kids * guestRates.park.kids; 
            subtotal += amt;
            originalTotal += amt;
            breakdown.push(`Park Kids: ${kids} × ₱${guestRates.park.kids} = ₱${amt.toFixed(2)}`);
        }
        if (seniors>0){
            const amt = seniors * guestRates.park.seniors; 
            subtotal += amt;
            originalTotal += amt;
            breakdown.push(`Park Seniors: ${seniors} × ₱${guestRates.park.seniors} = ₱${amt.toFixed(2)}`);
        }
    }

    // Accommodations (with discounts applied)
    if (serviceType!=='themed_park') {
        document.querySelectorAll('.accommodation-input').forEach(input => {
            const qty = parseInt(input.value)||0;
            const discountedPrice = parseFloat(input.dataset.price);
            const originalPrice = parseFloat(input.dataset.originalPrice);
            const hasDiscount = input.dataset.hasDiscount === 'true';
            const name = input.closest('div').querySelector('.font-semibold').textContent;
            
            if (qty>0){
                const cost = qty * discountedPrice;
                const originalCost = qty * originalPrice;
                subtotal += cost;
                originalTotal += originalCost;
                
                if (hasDiscount) {
                    breakdown.push(`${name}: ${qty} × ₱${discountedPrice.toFixed(2)} <span class="text-green-600">(Discounted)</span> = ₱${cost.toFixed(2)}`);
                } else {
                    breakdown.push(`${name}: ${qty} × ₱${discountedPrice.toFixed(2)} = ₱${cost.toFixed(2)}`);
                }
            }
        });
    }

    // Calculate manual discount
    const manualDiscountAmount = calculateManualDiscount(subtotal);
    const total = subtotal - manualDiscountAmount;

    // Update total display
    document.getElementById('totalAmount').textContent = total.toFixed(2);
    
    // Build breakdown HTML
    let breakdownHTML = breakdown.map(item => {
        return `<div class="flex justify-between py-1 border-b border-gray-100 last:border-0">${item}</div>`;
    }).join('');

    // Show automatic savings if there are facility discounts
    if (originalTotal > subtotal) {
        const automaticSavings = originalTotal - subtotal;
        breakdownHTML += `
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-green-600">Automatic Discounts:</span>
                <span class="text-green-600">-₱${automaticSavings.toFixed(2)}</span>
            </div>
        `;
    }

    // Show manual discount if applied
    if (manualDiscountAmount > 0) {
        breakdownHTML += `
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-red-600">Manual Discount:</span>
                <span class="text-red-600">-₱${manualDiscountAmount.toFixed(2)}</span>
            </div>
        `;
    }

    // Show total savings
    const totalSavings = (originalTotal - subtotal) + manualDiscountAmount;
    if (totalSavings > 0) {
        breakdownHTML += `
            <div class="flex justify-between py-2 border-t border-gray-200 mt-2">
                <span class="font-semibold text-green-600">Total Savings:</span>
                <span class="font-semibold text-green-600">₱${totalSavings.toFixed(2)}</span>
            </div>
        `;
    }

    document.getElementById('costBreakdown').innerHTML = breakdownHTML;
}

/* =====================
   Service selection
   ===================== */
function updateServiceSelection() {
    const selectedValue = document.querySelector('input[name="service_type"]:checked').value;
    document.querySelectorAll('.service-option').forEach(opt=>{
        const label = opt.querySelector('.service-label');
        if (opt.dataset.value===selectedValue){
            label.classList.add('border-red-500','bg-red-50');
        } else {
            label.classList.remove('border-red-500','bg-red-50');
        }
    });
    document.getElementById('poolAccessFields').classList.toggle('hidden', selectedValue==='themed_park');
    document.getElementById('themedParkFields').classList.toggle('hidden', selectedValue==='pool');
    document.getElementById('accommodationSection').classList.toggle('hidden', selectedValue==='themed_park');
    calculateTotal();
}

/* =====================
   Initialize everything
   ===================== */
document.addEventListener('DOMContentLoaded', function() {
    // Calendar event listeners
    document.getElementById('datePicker').addEventListener('click', e=>{
        if (e.target.closest('[data-action="prev-month"]')) changeMonth(-1);
        if (e.target.closest('[data-action="next-month"]')) changeMonth(1);
    });

    // Manual discount event listeners
    document.getElementById('applyManualDiscount').addEventListener('change', toggleManualDiscount);
    document.getElementById('discountType').addEventListener('change', updateManualDiscount);
    document.getElementById('discountValue').addEventListener('input', updateManualDiscount);
    document.getElementById('discountReason').addEventListener('input', updateManualDiscount);

    // Form event listeners
    document.querySelectorAll('input[name="service_type"]').forEach(r=>r.addEventListener('change', updateServiceSelection));
    document.querySelectorAll('.guest-counter, .accommodation-input').forEach(i=>i.addEventListener('input', calculateTotal));
    document.getElementById('date_tour').addEventListener('change', updateAvailabilityAndPrices);

    // Set initial date
    selectedDate = new Date();
    document.getElementById('walkinDateInput').value = formatDateDisplay(selectedDate);
    document.getElementById('date_tour').value = formatDateForInput(selectedDate);

    // Initialize components
    renderCalendar();
    updateServiceSelection();
    calculateTotal();
    updateAvailabilityAndPrices();

    // Handle flash messages
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(()=>{
            flash.classList.add('opacity-0','translate-y-2'); 
            setTimeout(()=>flash.remove(),500);
        },3000);
    }
});

// Form validation
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        const serviceType = document.querySelector("[name=service_type]").value;

        const poolTotal = 
            (parseInt(document.querySelector("[name=pool_adult]")?.value || 0)) +
            (parseInt(document.querySelector("[name=pool_kids]")?.value || 0)) +
            (parseInt(document.querySelector("[name=pool_seniors]")?.value || 0));

        const parkTotal = 
            (parseInt(document.querySelector("[name=park_adult]")?.value || 0)) +
            (parseInt(document.querySelector("[name=park_kids]")?.value || 0)) +
            (parseInt(document.querySelector("[name=park_seniors]")?.value || 0));

        if (serviceType === "pool" && poolTotal < 1) {
            e.preventDefault();
            alert("Please add at least 1 Pool guest.");
        }
        if (serviceType === "themed_park" && parkTotal < 1) {
            e.preventDefault();
            alert("Please add at least 1 Park guest.");
        }
        if (serviceType === "both" && (poolTotal < 1 || parkTotal < 1)) {
            e.preventDefault();
            alert("Please add at least 1 guest for both Pool and Park.");
        }
    });
});


// Add this to your existing JavaScript
function validateAccommodations() {
    const serviceType = document.querySelector('input[name="service_type"]:checked').value;
    const validationElement = document.getElementById('accommodationValidation');
    
    // Skip validation for themed_park only
    if (serviceType === 'themed_park') {
        validationElement.classList.add('hidden');
        return true;
    }
    
    const accommodationInputs = document.querySelectorAll('.accommodation-input');
    let hasAccommodation = false;
    
    accommodationInputs.forEach(input => {
        if (parseInt(input.value) > 0) {
            hasAccommodation = true;
        }
    });
    
    if (!hasAccommodation) {
        validationElement.classList.remove('hidden');
        // Highlight accommodation section
        document.getElementById('accommodationSection').classList.add('border-red-300', 'bg-red-50');
    } else {
        validationElement.classList.add('hidden');
        document.getElementById('accommodationSection').classList.remove('border-red-300', 'bg-red-50');
    }
    
    return hasAccommodation;
}

// Add event listeners for accommodation inputs
document.querySelectorAll('.accommodation-input').forEach(input => {
    input.addEventListener('input', validateAccommodations);
});

// Add to service type change
document.querySelectorAll('input[name="service_type"]').forEach(radio => {
    radio.addEventListener('change', validateAccommodations);
});

// Update form submission
document.getElementById('dayTourForm').addEventListener('submit', function(e) {
    const serviceType = document.querySelector('input[name="service_type"]:checked').value;
    
    if (serviceType !== 'themed_park' && !validateAccommodations()) {
        e.preventDefault();
        // Scroll to accommodations section
        document.getElementById('accommodationSection').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        return false;
    }
});

</script>
@endsection