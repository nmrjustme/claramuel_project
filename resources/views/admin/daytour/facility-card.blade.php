@php
    // Determine status for each facility
    if ($facility->display_status === 'maintenance') {
        $statusColor = 'gray';
        $statusText = 'Maintenance';
        $statusIcon = 'fas fa-tools';
    } elseif ($facility->display_status === 'cleaning') {
        $statusColor = 'yellow';
        $statusText = 'Cleaning';
        $statusIcon = 'fas fa-broom';
    } elseif ($facility->display_status === 'occupied') {
        $statusColor = 'red';
        $statusText = 'Occupied';
        $statusIcon = 'fas fa-users';
    } else {
        $statusColor = 'green';
        $statusText = 'Available';
        $statusIcon = 'fas fa-check-circle';
    }
@endphp

<div class="card-hover bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Status Header -->
    <div class="bg-{{ $statusColor }}-50 p-4 border-b border-{{ $statusColor }}-100">
        <div class="flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg">{{ $facility->name }}</h3>
            <span class="status-badge bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                <i class="{{ $statusIcon }} mr-1.5"></i>{{ $statusText }}
            </span>
        </div>
        <p class="text-sm text-gray-600 mt-1">{{ $facility->category }}</p>
    </div>

    <!-- Facility Details -->
    <div class="p-4">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-gray-800">{{ $facility->available }}</p>
                <p class="text-xs text-gray-600 mt-1">Available</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-gray-800">{{ $facility->booked }}</p>
                <p class="text-xs text-gray-600 mt-1">Booked</p>
            </div>
        </div>

        <!-- Occupancy Progress -->
        <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-700">Occupancy</span>
                <span class="text-sm font-bold text-gray-800">{{ $facility->occupancy_rate }}%</span>
            </div>
            <div class="occupancy-bar">
                <div class="occupancy-fill bg-{{ $facility->occupancy_rate > 75 ? 'red' : ($facility->occupancy_rate > 50 ? 'yellow' : 'green') }}-500" 
                      style="width: {{ $facility->occupancy_rate }}%"></div>
            </div>
        </div>

        <!-- Capacity Info -->
        <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
            <span>Capacity: {{ $facility->quantity }} people</span>
        </div>

        <!-- Current Bookings -->
        @if($facility->bookings && count($facility->bookings) > 0)
        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
            <p class="text-xs font-semibold text-blue-800 mb-2">Active Bookings:</p>
            @foreach($facility->bookings->take(2) as $booking)
            <div class="text-xs text-blue-700 mb-1 flex justify-between">
                <span>{{ Illuminate\Support\Str::limit($booking['customer'], 15) }}</span>
                <span class="font-medium">{{ $booking['quantity'] }} units</span>
            </div>
            @endforeach
            @if(count($facility->bookings) > 2)
            <p class="text-xs text-blue-600 mt-1">+{{ count($facility->bookings) - 2 }} more</p>
            @endif
        </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="p-4 bg-gray-50 border-t border-gray-200">
        <div class="grid grid-cols-2 gap-2">
            <!-- Status Update Buttons -->
            <form action="{{ route('admin.daytour.update-facility-status', $facility->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="available">
                <button type="submit" class="w-full px-3 py-2 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition flex items-center justify-center">
                    <i class="fas fa-check mr-1"></i>Available
                </button>
            </form>
            
            <form action="{{ route('admin.daytour.update-facility-status', $facility->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="maintenance">
                <button type="submit" class="w-full px-3 py-2 bg-gray-500 text-white text-xs rounded-lg hover:bg-gray-600 transition flex items-center justify-center">
                    <i class="fas fa-tools mr-1"></i>Maintenance
                </button>
            </form>
            
            <form action="{{ route('admin.daytour.update-facility-status', $facility->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="cleaning">
                <button type="submit" class="w-full px-3 py-2 bg-yellow-500 text-white text-xs rounded-lg hover:bg-yellow-600 transition flex items-center justify-center">
                    <i class="fas fa-broom mr-1"></i>Cleaning
                </button>
            </form>
            
            <!-- Checkout Button -->
            @if($facility->booked > 0)
            <form action="{{ route('admin.daytour.checkout-facility', $facility->id) }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <button type="submit" class="w-full px-3 py-2 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-1"></i>Check Out
                </button>
            </form>
            @else
            <button class="w-full px-3 py-2 bg-gray-200 text-gray-500 text-xs rounded-lg cursor-not-allowed flex items-center justify-center">
                <i class="fas fa-sign-out-alt mr-1"></i>No Checkouts
            </button>
            @endif
        </div>
    </div>
</div>