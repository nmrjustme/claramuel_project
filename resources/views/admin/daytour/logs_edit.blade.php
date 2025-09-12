@extends('layouts.admin')
@section('title', 'Edit Booking Details')
@php
    $active = 'day_tour';
@endphp

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Booking Details #{{ $log->id }}</h1>
                <p class="text-gray-600 mt-1">Guest: {{ $log->user->firstname }} {{ $log->user->lastname }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.daytour.logs.show', $log->id) }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-200 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </div>
        @if(session('success'))
        <div id="flash-message" class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 font-medium shadow-md transition transform duration-500">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div id="flash-message" class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 font-medium shadow-md transition transform duration-500">
            {{ session('error') }}
        </div>
        @endif
    </div>

    <form action="{{ route('admin.daytour.logs.update', $log->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column - Basic Info -->
            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Basic Information</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Tour Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tour Date</label>
                            <input type="date" name="date_tour" id="date_tour_input" value="{{ $log->date_tour }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-red-400" required
                                onchange="updateAvailability()">
                        </div>
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-red-400">
                                <option value="pending" {{ $log->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $log->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="approved" {{ $log->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $log->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <!-- Total Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Calculated Total Price (₱)</label>
                            <input type="text" value="₱{{ number_format($log->total_price, 2) }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                            <input type="hidden" name="total_price" id="total_price_input" value="{{ $log->total_price }}">
                        </div>
                    </div>
                </div>

                <!-- Guest Information (Readonly) -->
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-user-circle text-red-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Guest Information</h3>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Name</p>
                            <p class="font-medium text-gray-800">{{ $log->user->firstname }} {{ $log->user->lastname }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Email</p>
                            <p class="font-medium text-gray-800">{{ $log->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Phone</p>
                            <p class="font-medium text-gray-800">{{ $log->user->phone }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Guest & Facility Editing -->
            <div class="space-y-6">
                <!-- Guest Types Editing -->
<div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center mb-4">
        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-users text-green-500"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Guest Composition</h3>
    </div>

    <div class="space-y-4">
        @foreach($guestTypes->groupBy('location') as $location => $types)
        <div>
            <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                @if($location === 'Pool')
                <i class="fas fa-swimming-pool text-cyan-500 mr-2"></i>
                @elseif($location === 'Park')
                <i class="fas fa-tree text-emerald-500 mr-2"></i>
                @else
                <i class="fas fa-question-circle text-gray-500 mr-2"></i>
                @endif
                {{ $location }} Area Guests
            </h4>
            
            <div class="grid grid-cols-1 gap-3">
                @foreach($types as $guestType)
                @php
                    $currentQty = $guestDetails[$guestType->id]['quantity'] ?? 0;
                @endphp
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800">{{ $guestType->type }}</p>
                        <p class="text-sm text-gray-600">₱{{ number_format($guestType->rate, 2) }}/person</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="decrementQuantity('guest_{{ $guestType->id }}')" 
                            class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center hover:bg-gray-300 transition quantity-btn">
                            <i class="fas fa-minus text-gray-600"></i>
                        </button>
                        <input type="number" 
                            name="guest_types[{{ $guestType->id }}][quantity]" 
                            id="guest_{{ $guestType->id }}"
                            value="{{ $currentQty }}" 
                            min="0" 
                            class="w-16 text-center border border-gray-300 rounded-lg py-1 guest-quantity">
                        <button type="button" onclick="incrementQuantity('guest_{{ $guestType->id }}')" 
                            class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center hover:bg-gray-300 transition quantity-btn">
                            <i class="fas fa-plus text-gray-600"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

                <!-- Facilities Editing -->
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-home text-orange-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Accommodations (Cottages & Villas)</h3>
                    </div>

                    <div class="space-y-3">
                        @foreach($facilities as $facility)
                        @php
                            $currentQty = $facilityDetails[$facility->id]['facility_quantity'] ?? 0;
                            $price = $facility->rate ?? $facility->price ?? 0;
                            $availability = $facilityAvailability[$facility->id] ?? ['available' => 0, 'max_allowed' => 0];
                        @endphp
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-white hover:shadow-md transition">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-medium text-gray-800">{{ $facility->name }}</p>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $facility->category === 'Cottage' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $facility->category }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">₱{{ number_format($price, 2) }}/unit</p>
                                <p class="text-xs {{ $availability['available'] > 0 ? 'text-green-600' : 'text-red-600' }} mt-1 availability-text" id="availability_{{ $facility->id }}">
                                    Available: {{ $availability['available'] }} / Total: {{ $facility->quantity }}
                                    @if($currentQty > 0)
                                        <span class="text-blue-600 ml-2">(You have {{ $currentQty }})</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="decrementQuantity('facility_{{ $facility->id }}')" 
                                    class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center hover:bg-gray-300 transition quantity-btn">
                                    <i class="fas fa-minus text-gray-600"></i>
                                </button>
                                <input type="number" 
                                    name="facilities[{{ $facility->id }}][facility_quantity]" 
                                    id="facility_{{ $facility->id }}"
                                    value="{{ $currentQty }}" 
                                    min="0" 
                                    max="{{ $availability['max_allowed'] }}"
                                    data-available="{{ $availability['available'] }}"
                                    data-current="{{ $currentQty }}"
                                    data-total="{{ $facility->quantity }}"
                                    class="w-16 text-center border border-gray-300 rounded-lg py-1 facility-quantity">
                                <button type="button" onclick="incrementQuantity('facility_{{ $facility->id }}')" 
                                    class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center hover:bg-gray-300 transition quantity-btn">
                                    <i class="fas fa-plus text-gray-600"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($facilities->count() == 0)
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-home text-3xl mb-2"></i>
                            <p>No cottages or villas available</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                        <i class="fas fa-save mr-2"></i>Update Booking Details
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Loading Indicator -->
<div id="availability-loading" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mr-3"></div>
            <span>Checking availability...</span>
        </div>
    </div>
</div>

<style>
    .quantity-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .quantity-btn:hover:not([style*="cursor: not-allowed"]) {
        transform: scale(1.1);
        background-color: #e5e7eb;
    }
    
    /* Ensure buttons are never actually disabled */
    .quantity-btn {
        pointer-events: auto !important;
    }
    
    .category-header {
        border-left: 4px solid;
        padding-left: 12px;
    }
    
    .category-cottage {
        border-left-color: #f59e0b;
    }
    
    .category-villa {
        border-left-color: #8b5cf6;
    }
    
    #total-price-display {
        font-size: 1.125rem;
        font-weight: bold;
        color: #059669;
        margin-top: 0.5rem;
    }
</style>

<script>
    // Store rates and prices for calculation
    const guestTypeRates = {
        @foreach($guestTypes as $guestType)
        '{{ $guestType->id }}': {{ $guestType->rate ?? $guestType->price ?? 0 }},
        @endforeach
    };

    const facilityPrices = {
        @foreach($facilities as $facility)
        '{{ $facility->id }}': {{ $facility->rate ?? $facility->price ?? 0 }},
        @endforeach
    };

    // Simple increment function
    function incrementQuantity(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const max = parseInt(input.getAttribute('max')) || 999;
        const currentValue = parseInt(input.value || 0);
        
        if (currentValue < max) {
            input.value = currentValue + 1;
            calculateTotal();
            updateFacilityButtonVisuals(); // Update visual state only
        }
    }

    // Simple decrement function - NEVER disabled
    function decrementQuantity(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const currentValue = parseInt(input.value || 0);
        if (currentValue > 0) {
            input.value = currentValue - 1;
            calculateTotal();
            updateFacilityButtonVisuals(); // Update visual state only
        }
    }

    // Calculate total price
    function calculateTotal() {
        let total = 0;
        
        // Calculate guest type totals
        @foreach($guestTypes as $guestType)
        const guestQty{{ $guestType->id }} = parseInt(document.getElementById('guest_{{ $guestType->id }}')?.value || 0);
        total += guestQty{{ $guestType->id }} * {{ $guestType->rate ?? $guestType->price ?? 0 }};
        @endforeach
        
        // Calculate facility totals
        @foreach($facilities as $facility)
        const facilityQty{{ $facility->id }} = parseInt(document.getElementById('facility_{{ $facility->id }}')?.value || 0);
        total += facilityQty{{ $facility->id }} * {{ $facility->rate ?? $facility->price ?? 0 }};
        @endforeach
        
        // Update total price input
        const totalPriceInput = document.getElementById('total_price_input');
        if (totalPriceInput) {
            totalPriceInput.value = total.toFixed(2);
        }
    }

    // Store facility data for availability checks
    const facilitiesData = {
        @foreach($facilities as $facility)
        {{ $facility->id }}: {
            id: {{ $facility->id }},
            name: '{{ $facility->name }}',
            total_quantity: {{ $facility->quantity }},
            price: {{ $facility->rate ?? $facility->price ?? 0 }}
        },
        @endforeach
    };

    // Function to update availability based on selected date
    async function updateAvailability() {
        const dateInput = document.getElementById('date_tour_input');
        const selectedDate = dateInput.value;
        const loadingElement = document.getElementById('availability-loading');
        
        if (!selectedDate) return;
        
        // Show loading state
        if (loadingElement) loadingElement.classList.remove('hidden');
        
        try {
            // Fetch availability data for the selected date
            const response = await fetch('/daytour/check-availability?date=' + selectedDate);
            const availabilityData = await response.json();
            
            // Update each facility's availability
            availabilityData.forEach(facility => {
                const facilityElement = document.getElementById('facility_' + facility.id);
                const availabilityDisplay = document.getElementById('availability_' + facility.id);
                
                if (facilityElement && facilitiesData[facility.id]) {
                    const currentQty = parseInt(facilityElement.value || 0);
                    const maxAllowed = Math.min(facility.available + currentQty, facilitiesData[facility.id].total_quantity);
                    
                    // Update element attributes
                    facilityElement.setAttribute('max', maxAllowed);
                    facilityElement.setAttribute('data-available', facility.available);
                    
                    // Update availability display
                    if (availabilityDisplay) {
                        availabilityDisplay.innerHTML = `Available: ${facility.available} / Total: ${facilitiesData[facility.id].total_quantity}`;
                        if (currentQty > 0) {
                            availabilityDisplay.innerHTML += `<span class="text-blue-600 ml-2">(You have ${currentQty})</span>`;
                        }
                        availabilityDisplay.className = `text-xs ${facility.available > 0 ? 'text-green-600' : 'text-red-600'} mt-1 availability-text`;
                    }
                }
            });
            
        } catch (error) {
            console.error('Error fetching availability:', error);
            alert('Error checking availability. Please try again.');
        } finally {
            // Hide loading
            if (loadingElement) loadingElement.classList.add('hidden');
            updateFacilityButtonVisuals(); // Update button visuals after availability check
        }
    }

    // NEW: Update button visuals without disabling them
    function updateFacilityButtonVisuals() {
        document.querySelectorAll('.facility-quantity').forEach(input => {
            const currentValue = parseInt(input.value || 0);
            const minusButton = input.previousElementSibling;
            const plusButton = input.nextElementSibling;
            
            // Update minus button VISUALS only (never disable)
            if (minusButton) {
                // Just change opacity for visual feedback, but keep clickable
                minusButton.style.opacity = currentValue <= 0 ? '0.5' : '1';
                minusButton.style.cursor = currentValue <= 0 ? 'not-allowed' : 'pointer';
                minusButton.removeAttribute('disabled'); // Ensure never disabled
            }
            
            // Update plus button based on availability
            if (plusButton) {
                const available = parseInt(input.getAttribute('data-available')) || 0;
                const max = parseInt(input.getAttribute('max')) || 0;
                const shouldDisableVisual = available <= 0 || currentValue >= max;
                
                plusButton.style.opacity = shouldDisableVisual ? '0.5' : '1';
                plusButton.style.cursor = shouldDisableVisual ? 'not-allowed' : 'pointer';
                plusButton.removeAttribute('disabled'); // Ensure never disabled
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add input validation
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                const min = parseInt(this.getAttribute('min')) || 0;
                const max = parseInt(this.getAttribute('max')) || 999;
                let value = parseInt(this.value || 0);
                
                // Validate input
                if (isNaN(value)) value = 0;
                if (value < min) value = min;
                if (value > max) value = max;
                
                this.value = value;
                calculateTotal();
                updateFacilityButtonVisuals(); // Update visuals on input
            });
        });
        
        // Initial calculation
        calculateTotal();
        
        // Remove any disabled attributes from all buttons
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.removeAttribute('disabled');
            button.style.opacity = '1';
            button.style.cursor = 'pointer';
        });
        
        // Initial button visuals update
        updateFacilityButtonVisuals();
    });
</script>
@endsection