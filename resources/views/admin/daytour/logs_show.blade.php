@extends('layouts.admin')
@section('title', 'Show DayTour Details')
@php
    $active = 'day_tour';
@endphp

@section('content')



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>

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
                <h1 class="text-2xl font-bold text-gray-800">Booking Details</h1>
                <p class="text-gray-600 mt-1">#{{ $log->id }} • {{ $log->user->firstname }} {{ $log->user->lastname }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.daytour.logs.print', $log->id) }}" target="_blank" 
                   class="px-4 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition-all duration-200 flex items-center shadow-sm hover:shadow-md">
                    <i class="fas fa-print mr-2 text-blue-500"></i>Print
                </a>
                <a href="{{ route('admin.daytour.logs.edit', $log->id) }}" 
                   class="px-4 py-2 bg-red-50 text-red-700 rounded-lg border border-red-200 hover:bg-red-100 transition-all duration-200 flex items-center shadow-sm hover:shadow-md">
                    <i class="fas fa-edit mr-2 text-red-500"></i>Edit
                </a>
                <a href="{{ route('admin.daytour.logs') }}" 
                   class="px-4 py-2 bg-gray-50 text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all duration-200 flex items-center shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left mr-2 text-gray-500"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Service Type Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-tag text-red-500 text-lg"></i>
            </div>
            <p class="text-sm text-gray-600 mb-1">Service Type</p>
            <p class="font-semibold text-gray-800">{{ $serviceType['type'] }}</p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-users text-blue-500 text-lg"></i>
            </div>
            <p class="text-sm text-gray-600 mb-1">Total Guests</p>
            <p class="text-xl font-bold text-gray-800">{{ $serviceType['total'] }}</p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm text-center">
            <div class="w-12 h-12 bg-cyan-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-swimming-pool text-cyan-500 text-lg"></i>
            </div>
            <p class="text-sm text-gray-600 mb-1">Pool Guests</p>
            <p class="text-xl font-bold text-cyan-600">{{ $serviceType['pool_count'] }}</p>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-tree text-green-500 text-lg"></i>
            </div>
            <p class="text-sm text-gray-600 mb-1">Park Guests</p>
            <p class="text-xl font-bold text-green-600">{{ $serviceType['park_count'] }}</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-6">
            <!-- Guest Information -->
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-circle text-red-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Guest Information</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Name</span>
                        <span class="font-medium text-gray-800">{{ $log->user->firstname }} {{ $log->user->lastname }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Email</span>
                        <span class="font-medium text-gray-800">{{ $log->user->email }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Phone</span>
                        <span class="font-medium text-gray-800">{{ $log->user->phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Booking Information -->
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-alt text-blue-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Booking Information</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Tour Date</span>
                        <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($log->date_tour)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Status</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold 
                            {{ $log->status == 'paid' ? 'bg-green-100 text-green-800' : 
                               ($log->status == 'approved' ? 'bg-blue-100 text-blue-800' : 
                               ($log->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                            {{ ucfirst($log->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Total Amount</span>
                        <span class="font-semibold text-red-600">₱{{ number_format($log->total_price, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Guest Composition -->
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-purple-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Guest Composition</h3>
                </div>

                <!-- Pool Guests -->
                <div class="mb-4">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-swimming-pool text-cyan-500 mr-2"></i>
                        <h4 class="font-medium text-gray-700">Pool Area</h4>
                        <span class="ml-auto bg-cyan-100 text-cyan-800 px-2 py-1 rounded-full text-xs font-semibold">
                            {{ $serviceType['pool_count'] }} guests
                        </span>
                    </div>
                    <div class="space-y-2 pl-6">
                        @php
                            $poolGuests = [];
                            foreach ($log->bookingGuestDetails->where('facility_id', null) as $guest) {
                                if ($guest->quantity > 0 && $guest->guestType && $guest->guestType->location === 'Pool') {
                                    $typeName = $guest->guestType->type;
                                    $poolGuests[$typeName] = ($poolGuests[$typeName] ?? 0) + $guest->quantity;
                                }
                            }
                        @endphp
                        
                        @foreach($poolGuests as $type => $quantity)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">{{ $type }}</span>
                            <span class="font-medium">{{ $quantity }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Park Guests -->
                <div>
                    <div class="flex items-center mb-3">
                        <i class="fas fa-tree text-emerald-500 mr-2"></i>
                        <h4 class="font-medium text-gray-700">Park Area</h4>
                        <span class="ml-auto bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full text-xs font-semibold">
                            {{ $serviceType['park_count'] }} guests
                        </span>
                    </div>
                    <div class="space-y-2 pl-6">
                        @php
                            $parkGuests = [];
                            foreach ($log->bookingGuestDetails->where('facility_id', null) as $guest) {
                                if ($guest->quantity > 0 && $guest->guestType && $guest->guestType->location === 'Park') {
                                    $typeName = $guest->guestType->type;
                                    $parkGuests[$typeName] = ($parkGuests[$typeName] ?? 0) + $guest->quantity;
                                }
                            }
                        @endphp
                        
                        @foreach($parkGuests as $type => $quantity)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">{{ $type }}</span>
                            <span class="font-medium">{{ $quantity }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Accommodations -->
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-home text-orange-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Accommodations</h3>
                </div>
                <div class="space-y-3">
                    @php
                        $accommodationSummary = [];
                        foreach ($log->bookingGuestDetails->where('facility_id', '!=', null) as $accommodation) {
                            if ($accommodation->facility_quantity > 0 && $accommodation->facility) {
                                $facilityName = $accommodation->facility->name;
                                $accommodationSummary[$facilityName] = ($accommodationSummary[$facilityName] ?? 0) + $accommodation->facility_quantity;
                            }
                        }
                    @endphp
                    
                    @if(count($accommodationSummary) > 0)
                        @foreach($accommodationSummary as $facility => $quantity)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                            <span class="text-gray-600">{{ $facility }}</span>
                            <span class="font-medium">{{ $quantity }} unit(s)</span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm text-center py-4">No accommodations booked</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center mb-4">
        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-history text-gray-500"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Activity Timeline</h3>
    </div>
    <div class="space-y-4">
        <!-- Booking Created -->
        <div class="flex items-start">
            <div class="w-3 h-3 bg-green-500 rounded-full mt-2 mr-4"></div>
            <div>
                <p class="font-medium text-gray-800">Booking Created</p>
                <p class="text-sm text-gray-500">{{ $log->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        <!-- Last Updated -->
        @if($log->updated_at->gt($log->created_at))
        <div class="flex items-start">
            <div class="w-3 h-3 bg-blue-500 rounded-full mt-2 mr-4"></div>
            <div>
                <p class="font-medium text-gray-800">Last Updated</p>
                <p class="text-sm text-gray-500">{{ $log->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
</div>

<style>
    /* Flash message animation */
    #flash-message {
        animation: slideDown 0.5s ease-out, fadeOut 0.5s ease-in 3.5s forwards;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
        }
    }
</style>
@endsection