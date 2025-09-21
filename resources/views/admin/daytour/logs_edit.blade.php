@extends('layouts.admin')
@section('title', 'Edit Booking Details')
@php
    $active = 'day_tour';
@endphp

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="max-w-6xl mx-auto space-y-6 py-6">

    <!-- Header -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-indigo-600"></i>
                    Edit Booking Details #{{ $log->id }}
                </h1>
                <p class="text-gray-600 mt-2 flex items-center gap-2">
                    <i class="fas fa-user-circle text-indigo-500"></i>
                    Guest: {{ $log->user->firstname }} {{ $log->user->lastname }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.daytour.logs.show', $log->id) }}" 
                   class="px-4 py-2.5 bg-white text-gray-700 rounded-xl border border-gray-300 hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-4 p-4 rounded-xl bg-green-50 text-green-700 font-medium border border-green-200 flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 p-4 rounded-xl bg-red-50 text-red-700 font-medium border border-red-200 flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i>
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Form -->
    <form action="{{ route('admin.daytour.logs.update', $log->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-indigo-600"></i>
                        Basic Information
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tour Date</label>
                            <div class="relative">
                                <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="date" name="date_tour" id="date_tour_input" value="{{ $log->date_tour }}" 
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <div class="relative">
                                <i class="fas fa-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <select name="status" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none">
                                    <option value="pending" {{ $log->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $log->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="approved" {{ $log->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $log->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Calculated Total Price</label>
                            <div class="relative">
                                <i class="fas fa-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="total_price_display" value="₱{{ number_format($log->total_price, 2) }}" 
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl bg-gray-50" readonly>
                                <input type="hidden" name="total_price" id="total_price_input" value="{{ $log->total_price }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guest Info -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-indigo-600"></i>
                        Guest Information
                    </h3>
                    <div class="space-y-3">
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-user text-gray-400 w-5"></i>
                            <span class="font-medium text-gray-800">Name:</span> 
                            {{ $log->user->firstname }} {{ $log->user->lastname }}
                        </p>
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="font-medium text-gray-800">Email:</span> 
                            {{ $log->user->email }}
                        </p>
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-phone text-gray-400 w-5"></i>
                            <span class="font-medium text-gray-800">Phone:</span> 
                            {{ $log->user->phone }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Guest Composition -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-users text-indigo-600"></i>
                        Guest Composition
                    </h3>

                    @foreach($guestTypes->groupBy('location') as $location => $types)
                        <div class="mb-5">
                            <h4 class="font-medium text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-red-500"></i>
                                {{ $location }} Area Guests
                            </h4>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($types as $guestType)
                                    @php $currentQty = $guestDetails[$guestType->id]['quantity'] ?? 0; @endphp
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-800">{{ $guestType->type }}</p>
                                            <p class="text-sm text-gray-600">₱{{ number_format($guestType->rate, 2) }}/person</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button type="button" onclick="decrementQuantity('guest_{{ $guestType->id }}')"
                                                class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition text-gray-600 quantity-btn">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" 
                                                id="guest_{{ $guestType->id }}" 
                                                name="guest_types[{{ $guestType->id }}][quantity]" 
                                                value="{{ $currentQty }}" 
                                                min="0" 
                                                class="w-16 text-center border border-gray-300 rounded-lg py-1.5 guest-quantity"
                                                data-rate="{{ $guestType->rate }}">
                                            <button type="button" onclick="incrementQuantity('guest_{{ $guestType->id }}')"
                                                class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition text-gray-600 quantity-btn">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Facilities -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-home text-indigo-600"></i>
                        Accommodations (Cottages & Villas)
                    </h3>

                    <div class="space-y-3">
                        @foreach($facilities as $facility)
                            @php
                                $currentQty = $facilityDetails[$facility->id]['facility_quantity'] ?? 0;
                                $availability = $facilityAvailability[$facility->id] ?? ['available'=>0,'max_allowed'=>0];
                            @endphp
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $facility->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $facility->category }} - ₱{{ number_format($facility->price,2) }}/unit</p>
                                    <p class="text-xs mt-1.5 {{ $availability['available']>0?'text-green-600':'text-red-600' }} flex items-center gap-1" 
                                        id="availability_{{ $facility->id }}">
                                        <i class="fas {{ $availability['available']>0?'fa-check-circle':'fa-exclamation-circle' }}"></i>
                                        Available: {{ $availability['available'] }} / Total: {{ $facility->quantity }}
                                        @if($currentQty>0)
                                            <span class="text-indigo-600 ml-2">(You have {{ $currentQty }})</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="decrementQuantity('facility_{{ $facility->id }}')"
                                        class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition text-gray-600 quantity-btn">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number"
                                        id="facility_{{ $facility->id }}"
                                        name="facilities[{{ $facility->id }}][facility_quantity]"
                                        value="{{ $currentQty }}"
                                        min="0"
                                        max="{{ $availability['max_allowed'] }}"
                                        data-available="{{ $availability['available'] }}"
                                        data-rate="{{ $facility->price ?? 0 }}"
                                        data-total="{{ $facility->quantity }}"
                                        class="facility-quantity w-16 text-center border border-gray-300 rounded-lg py-1.5">
                                    <button type="button" onclick="incrementQuantity('facility_{{ $facility->id }}')"
                                        class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition text-gray-600 quantity-btn">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit -->
                <div class="text-center pt-4">
                    <button type="submit" class="w-full px-6 py-3.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition">
                        <i class="fas fa-save"></i>
                        Update Booking Details
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    function calculateTotal(){
        let total = 0;
        document.querySelectorAll('.guest-quantity').forEach(input => 
            total += (parseInt(input.value)||0)*(parseFloat(input.dataset.rate)||0)
        );
        document.querySelectorAll('.facility-quantity').forEach(input => 
            total += (parseInt(input.value)||0)*(parseFloat(input.dataset.rate)||0)
        );
        document.getElementById('total_price_input').value = total.toFixed(2);
        document.getElementById('total_price_display').value = '₱'+total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function updateButtonVisuals(input=null){
        const inputs = input ? [input] : document.querySelectorAll('.guest-quantity, .facility-quantity');
        inputs.forEach(inp=>{
            const container = inp.closest('div.flex.items-center.space-x-2');
            if(!container) return;
            const minus = container.querySelector('button:first-child');
            const plus = container.querySelector('button:last-child');
            let val = parseInt(inp.value)||0;
            const max = parseInt(inp.getAttribute('max'))||999;
            const available = parseInt(inp.dataset.available)||max;
            
            // Ensure the value never exceeds limits
            if(val > max) { val = max; inp.value = val; }
            if(val > available) { val = available; inp.value = val; }

            if(minus){ 
                minus.disabled = val <= 0;
                minus.classList.toggle('opacity-50', val <= 0);
                minus.classList.toggle('cursor-not-allowed', val <= 0);
            }
            if(plus){ 
                const isMax = val >= max || val >= available;
                plus.disabled = isMax;
                plus.classList.toggle('opacity-50', isMax);
                plus.classList.toggle('cursor-not-allowed', isMax);
            }
        });
    }

    window.incrementQuantity = function(id){
        const input=document.getElementById(id);
        if(input){
            let val=parseInt(input.value)||0; 
            let max=parseInt(input.getAttribute('max'))||999;
            let available=parseInt(input.dataset.available)||max;
            if(val < max && val < available){ input.value=val+1; calculateTotal(); updateButtonVisuals(input); }
        }
    }

    window.decrementQuantity = function(id){
        const input=document.getElementById(id);
        if(input){
            let val=parseInt(input.value)||0;
            if(val>0){ input.value=val-1; calculateTotal(); updateButtonVisuals(input); }
        }
    }

    // Input event listener to enforce limits
    document.querySelectorAll('.guest-quantity, .facility-quantity').forEach(input=>{
        input.addEventListener('input', ()=>{
            let val = parseInt(input.value)||0;
            const max = parseInt(input.getAttribute('max'))||999;
            const available = parseInt(input.dataset.available)||max;
            if(val > max) input.value = max;
            if(val > available) input.value = available;
            if(val < 0) input.value = 0;
            if(isNaN(val)) input.value = 0;
            calculateTotal();
            updateButtonVisuals(input);
        });
    });

    calculateTotal();
    updateButtonVisuals();

    // Update availability when changing tour date
    const dateInput = document.getElementById('date_tour_input');
    if(dateInput){
        dateInput.addEventListener('change', async function(){
            const date=this.value; if(!date) return;
            try{
                const res=await fetch(`/daytour/check-availability?date=${date}`);
                const data=await res.json();
                data.forEach(fac=>{
                    const input=document.getElementById('facility_'+fac.id);
                    const display=document.getElementById('availability_'+fac.id);
                    if(input && display){
                        const current=parseInt(input.value)||0;
                        input.setAttribute('max',current+fac.available);
                        input.dataset.available=fac.available;
                        display.innerHTML=`<i class="fas ${fac.available>0?'fa-check-circle':'fa-exclamation-circle'}"></i> Available: ${fac.available} / Total: ${input.dataset.total}`;
                        if(current>0) display.innerHTML+=` <span class="text-indigo-600 ml-2">(You have ${current})</span>`;
                        display.className=`text-xs ${fac.available>0?'text-green-600':'text-red-600'} mt-1.5 flex items-center gap-1 availability-text`;
                    }
                });
                calculateTotal(); updateButtonVisuals();
            }catch(err){ console.error(err); alert('Error fetching availability.'); }
        });
    }
});
</script>

<style>
.quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.quantity-btn:disabled:hover {
    background-color: #f3f4f6 !important;
}

.input-field:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    border-color: #6366f1;
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
</style>
@endsection