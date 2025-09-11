@extends('layouts.admin')
@section('title', 'Day Tour Logs')
@php
    $active = 'day_tour';
@endphp

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="p-6 bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Day Tour Registration Logs</h2>
    <div class="flex justify-between items-center mb-6">
    <div class="flex space-x-3">
    <a href="{{ route('admin.daytour.cottages_monitoring') }}" 
       class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
        <i class="fas fa-tv mr-2"></i>Cottage Monitoring
    </a>
    <a href="{{ route('admin.daytour.index') }}" 
       class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
        <i class="fas fa-plus mr-2"></i>New Registration
    </a>
</div>
</div>
</div>

    <!-- User-Friendly Search and Filters -->
<div class="bg-gray-50 p-4 rounded-lg mb-6">
    <form method="GET" action="{{ route('admin.daytour.day_tour_logs') }}">
        <!-- Basic Search Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Everything</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search names, emails, phones, IDs, 'Adult', 'Gazebo', 'Pool', prices, dates..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" value="{{ request('date') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
            </div>
        </div>

        <!-- Advanced Filters Toggle -->
        <div class="mb-4">
            <button type="button" onclick="toggleAdvancedFilters()" 
                class="flex items-center text-sm text-red-600 hover:text-red-800">
                <i class="fas fa-cog mr-1"></i>
                <span id="advancedToggleText">Advanced Filters</span>
                <i class="fas fa-chevron-down ml-1 text-xs" id="advancedToggleIcon"></i>
            </button>
        </div>

        <!-- Advanced Filters (ALWAYS HIDDEN by default - using style attribute) -->
        <div id="advancedFilters" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Service Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                <select name="service_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
                    <option value="">All Services</option>
                    <option value="Pool" {{ request('service_type') == 'Pool' ? 'selected' : '' }}>Pool Only</option>
                    <option value="Park" {{ request('service_type') == 'Park' ? 'selected' : '' }}>Park Only</option>
                    <option value="Both" {{ request('service_type') == 'Both' ? 'selected' : '' }}>Both Services</option>
                </select>
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0.00" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="10000.00" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Guest Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guest Type</label>
                <input type="text" name="guest_type" value="{{ request('guest_type') }}" 
                    placeholder="e.g., Adult, Kids, Senior"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
            </div>

            <!-- Facility -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Facility</label>
                <input type="text" name="facility" value="{{ request('facility') }}" 
                    placeholder="e.g., Gazebo, Cottage"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-4">
            <a href="{{ route('admin.daytour.day_tour_logs') }}" 
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times mr-2"></i>Clear All
            </a>
            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </div>
    </form>
</div>

    <!-- Filter Summary -->
@if(request()->anyFilled(['search', 'date', 'date_from', 'date_to', 'status', 'service_type', 'guest_type', 'facility', 'min_price', 'max_price']))
<div class="mb-4 p-3 bg-blue-50 rounded-lg">
    <h3 class="text-sm font-medium text-blue-800 mb-2">Active Filters:</h3>
    <div class="flex flex-wrap gap-2">
        @foreach(request()->all() as $key => $value)
            @if(!empty($value) && !in_array($key, ['page', '_token']))
                @if(is_array($value))
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ implode(', ', $value) }}
                    </span>
                @else
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                    </span>
                @endif
            @endif
        @endforeach
        <a href="{{ route('admin.daytour.day_tour_logs') }}" class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full hover:bg-red-200">
            Clear All ×
        </a>
    </div>
</div>
@endif

    <!-- Logs Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guests</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accommodations</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $log->user->firstname }} {{ $log->user->lastname }}</div>
                        <div class="text-sm text-gray-500">{{ $log->user->email }}</div>
                        <div class="text-sm text-gray-500">{{ $log->user->phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($log->date_tour)->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
    $serviceType = 'Unknown';
    
    // Use filter() instead of whereHas() for collections
    $poolGuests = $log->bookingGuestDetails
        ->where('facility_id', null)
        ->where('quantity', '>', 0)
        ->filter(function($guest) {
            return $guest->guestType && $guest->guestType->location == 'Pool';
        })
        ->count();
    
    $parkGuests = $log->bookingGuestDetails
        ->where('facility_id', null)
        ->where('quantity', '>', 0)
        ->filter(function($guest) {
            return $guest->guestType && $guest->guestType->location == 'Park';
        })
        ->count();
    
    if ($poolGuests > 0 && $parkGuests > 0) {
        $serviceType = 'Both';
    } elseif ($poolGuests > 0) {
        $serviceType = 'Pool';
    } elseif ($parkGuests > 0) {
        $serviceType = 'Themed Park';
    }
@endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $serviceType == 'Both' ? 'bg-purple-100 text-purple-800' : 
                               ($serviceType == 'Pool' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                            {{ $serviceType }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $guestCounts = [];
                            foreach ($log->bookingGuestDetails->where('facility_id', null) as $guest) {
                                if ($guest->quantity > 0) {
                                    $guestCounts[] = $guest->guestType->type . ': ' . $guest->quantity;
                                }
                            }
                        @endphp
                        <div class="text-sm text-gray-900">{{ implode(', ', $guestCounts) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $accommodations = [];
                            foreach ($log->bookingGuestDetails->where('facility_id', '!=', null) as $accommodation) {
                                if ($accommodation->facility_quantity > 0) {
                                    $accommodations[] = $accommodation->facility->name . ': ' . $accommodation->facility_quantity;
                                }
                            }
                        @endphp
                        <div class="text-sm text-gray-900">{{ implode(', ', $accommodations) ?: 'None' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-red-600">₱{{ number_format($log->total_price, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $log->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($log->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    <a href="{{ route('admin.daytour.logs.show', $log->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
        <i class="fas fa-eye mr-1"></i>View
    </a>
    <a href="{{ route('admin.daytour.logs.edit', $log->id) }}" class="text-green-600 hover:text-green-900 mr-3">
        <i class="fas fa-edit mr-1"></i>Edit
    </a>
    <a href="{{ route('admin.daytour.logs.print', $log->id) }}" target="_blank" class="text-red-600 hover:text-red-900">
        <i class="fas fa-print mr-1"></i>Print
    </a>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                        No day tour registrations found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>

<script>
    function toggleAdvancedFilters() {
    const advancedFilters = document.getElementById('advancedFilters');
    const toggleText = document.getElementById('advancedToggleText');
    const toggleIcon = document.getElementById('advancedToggleIcon');
    
    if (advancedFilters.style.display === 'none') {
        advancedFilters.style.display = 'grid';
        toggleText.textContent = 'Hide Advanced';
        toggleIcon.className = 'fas fa-chevron-up ml-1 text-xs';
    } else {
        advancedFilters.style.display = 'none';
        toggleText.textContent = 'Advanced Filters';
        toggleIcon.className = 'fas fa-chevron-down ml-1 text-xs';
    }
}

// Force advanced filters to be hidden on page load
document.addEventListener('DOMContentLoaded', function() {
    const advancedFilters = document.getElementById('advancedFilters');
    const toggleText = document.getElementById('advancedToggleText');
    const toggleIcon = document.getElementById('advancedToggleIcon');
    
    // Force hide regardless of any previous state
    advancedFilters.style.display = 'none';
    toggleText.textContent = 'Advanced Filters';
    toggleIcon.className = 'fas fa-chevron-down ml-1 text-xs';
    
    // Optional: Show a badge if advanced filters are active
    const advancedParams = ['date_from', 'date_to', 'service_type', 'min_price', 'max_price', 'guest_type', 'facility'];
    const urlParams = new URLSearchParams(window.location.search);
    const hasAdvancedFilters = advancedParams.some(param => urlParams.has(param));
    
    if (hasAdvancedFilters) {
        // Add a badge to indicate advanced filters are active but hidden
        const toggleButton = document.querySelector('button[onclick="toggleAdvancedFilters()"]');
        const badge = document.createElement('span');
        badge.className = 'ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full';
        badge.textContent = 'Active';
        toggleButton.appendChild(badge);
    }
});
    </script>
@endsection