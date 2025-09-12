@extends('layouts.admin')
@php
    $active = 'day_tour';
@endphp

@section('title', 'Cottage & Villa Monitoring')

@section('content')
<div class="min-h-screen p-6 bg-gray-50">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-home text-primary"></i>
                Cottage & Villa Monitoring
            </h2>
            <p class="text-gray-500 mt-2 flex items-center gap-1">
                <i class="fas fa-calendar-alt text-sm"></i>
                Overview for <span class="font-semibold ml-1">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</span>
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <form method="GET" action="{{ route('admin.daytour.cottages_monitoring') }}" class="flex gap-2 items-center">
                <div class="relative">
                    <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="date" name="date" value="{{ $date }}" class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition">
                </div>
                <button type="submit" class="px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-primaryDark transition flex items-center gap-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
            <button onclick="window.location.reload()" class="px-4 py-2.5 bg-white text-gray-700 rounded-xl border border-gray-300 hover:bg-gray-50 transition flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700">{{ session('error') }}</div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 mb-10">
        <div class="summary-card bg-white rounded-2xl shadow p-5 flex flex-col border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-400 text-sm">Total Facilities</p>
                <i class="fas fa-building text-blue-500 bg-blue-100 p-2 rounded-lg"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $summary['total_facilities'] }}</h3>
        </div>
        <div class="summary-card bg-white rounded-2xl shadow p-5 flex flex-col border-l-4 border-l-green-500">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-400 text-sm">Available</p>
                <i class="fas fa-check-circle text-green-500 bg-green-100 p-2 rounded-lg"></i>
            </div>
            <h3 class="text-2xl font-bold text-green-600">{{ $summary['total_available'] }}</h3>
        </div>
        <div class="summary-card bg-white rounded-2xl shadow p-5 flex flex-col border-l-4 border-l-red-500">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-400 text-sm">Occupied</p>
                <i class="fas fa-users text-red-500 bg-red-100 p-2 rounded-lg"></i>
            </div>
            <h3 class="text-2xl font-bold text-red-600">{{ $summary['total_booked'] }}</h3>
        </div>
        <div class="summary-card bg-white rounded-2xl shadow p-5 flex flex-col border-l-4 border-l-primary">
            <div class="flex items-center justify-between mb-4">
                <p class="text-gray-400 text-sm">Overall Occupancy</p>
                <i class="fas fa-chart-pie text-primary bg-indigo-100 p-2 rounded-lg"></i>
            </div>
            <h3 class="text-2xl font-bold text-primary">{{ $summary['overall_occupancy'] }}%</h3>
        </div>
    </div>

    <!-- Tabs Container -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 px-6">
            <nav class="flex gap-8">
                <button onclick="showTab('cottages')" id="cottages-tab" class="tab-active py-4 text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-cabin"></i> Cottages
                </button>
                <button onclick="showTab('villas')" id="villas-tab" class="py-4 text-sm font-medium text-gray-500 flex items-center gap-2">
                    <i class="fas fa-house-user"></i> Villas
                </button>
            </nav>
        </div>

        <!-- Cottages Content -->
        <div id="cottages-content" class="tab-content p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($facilities->where('category','Cottage') as $facility)
                <div class="card-hover bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition p-5 flex flex-col h-full">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-lg text-gray-800 flex items-center gap-2">
                            <i class="fas fa-cabin text-primary"></i> {{ $facility->name }}
                        </h4>
                        <span class="status-badge px-3 py-1 rounded-full text-sm font-medium
                            {{ $facility->display_status=='available'?'bg-green-100 text-green-800':'' }}
                            {{ $facility->display_status=='occupied'?'bg-red-100 text-red-800':'' }}
                            {{ $facility->display_status=='maintenance'?'bg-yellow-100 text-yellow-800':'' }}
                            {{ $facility->display_status=='cleaning'?'bg-blue-100 text-blue-800':'' }}">
                            <i class="fas
                                {{ $facility->display_status=='available'?'fa-check-circle':'' }}
                                {{ $facility->display_status=='occupied'?'fa-users':'' }}
                                {{ $facility->display_status=='maintenance'?'fa-tools':'' }}
                                {{ $facility->display_status=='cleaning'?'fa-broom':'' }} text-xs"></i>
                            {{ ucfirst($facility->display_status) }}
                        </span>
                    </div>

                    <!-- Occupancy Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Occupancy</span>
                            <span>{{ $facility->occupancy_rate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="progress-bar h-2.5 rounded-full bg-primary" style="width: {{ $facility->occupancy_rate }}%"></div>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-4 flex items-center gap-1">
                        <i class="fas fa-chart-bar text-xs"></i> Booked: {{ $facility->booked }} | Available: {{ $facility->available }}
                    </p>

                    <!-- Units -->
                    <div class="mb-4 grid grid-cols-2 gap-2">
                        @foreach($facility->units as $unit)
                            <span class="px-2 py-1.5 rounded-lg text-xs font-semibold text-white flex items-center justify-center gap-1
                                {{ $unit['status']=='Available'?'bg-green-500':'' }}
                                {{ $unit['status']=='Occupied'?'bg-red-500':'' }}">
                                <i class="fas
                                    {{ $unit['status']=='Available'?'fa-check':'' }}
                                    {{ $unit['status']=='Occupied'?'fa-user':'' }} text-xs"></i>
                                {{ $unit['name'] }}
                            </span>
                        @endforeach
                    </div>

                    <!-- Bookings Accordion -->
                    <details class="mt-auto pt-4 border-t border-gray-100">
                        <summary class="cursor-pointer font-medium text-primary flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-list"></i> View Bookings
                            </span>
                        </summary>
                        <div class="mt-3 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-left">
                                    <tr>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($facility->bookings as $booking)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2">#{{ $booking['booking_id'] }}</td>
                                            <td class="px-3 py-2">{{ $booking['customer'] }}</td>
                                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($booking['date'])->format('M d, Y') }}</td>
                                            <td class="px-3 py-2">{{ $booking['quantity'] }}</td>
                                            <td class="px-3 py-2">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    {{ $booking['status']=='approved'?'bg-indigo-100 text-indigo-800':'' }}
                                                    {{ $booking['status']=='paid'?'bg-green-100 text-green-800':'' }}
                                                    {{ !in_array($booking['status'],['approved','paid'])?'bg-gray-100 text-gray-800':'' }}">
                                                    {{ ucfirst($booking['status']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-3 py-4 text-gray-400 text-center">
                                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                                No bookings found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </details>
                </div>
            @endforeach
        </div>

        <!-- Villas Content -->
        <div id="villas-content" class="tab-content p-6 hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($facilities->where('category','Villa') as $facility)
                <div class="card-hover bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition p-5 flex flex-col h-full">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-lg text-gray-800 flex items-center gap-2">
                            <i class="fas fa-cabin text-primary"></i> {{ $facility->name }}
                        </h4>
                        <span class="status-badge px-3 py-1 rounded-full text-sm font-medium
                            {{ $facility->display_status=='available'?'bg-green-100 text-green-800':'' }}
                            {{ $facility->display_status=='occupied'?'bg-red-100 text-red-800':'' }}
                            {{ $facility->display_status=='maintenance'?'bg-yellow-100 text-yellow-800':'' }}
                            {{ $facility->display_status=='cleaning'?'bg-blue-100 text-blue-800':'' }}">
                            <i class="fas
                                {{ $facility->display_status=='available'?'fa-check-circle':'' }}
                                {{ $facility->display_status=='occupied'?'fa-users':'' }}
                                {{ $facility->display_status=='maintenance'?'fa-tools':'' }}
                                {{ $facility->display_status=='cleaning'?'fa-broom':'' }} text-xs"></i>
                            {{ ucfirst($facility->display_status) }}
                        </span>
                    </div>

                    <!-- Occupancy Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Occupancy</span>
                            <span>{{ $facility->occupancy_rate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="progress-bar h-2.5 rounded-full bg-primary" style="width: {{ $facility->occupancy_rate }}%"></div>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-4 flex items-center gap-1">
                        <i class="fas fa-chart-bar text-xs"></i> Booked: {{ $facility->booked }} | Available: {{ $facility->available }}
                    </p>

                    <!-- Units -->
                    <div class="mb-4 grid grid-cols-2 gap-2">
                        @foreach($facility->units as $unit)
                            <span class="px-2 py-1.5 rounded-lg text-xs font-semibold text-white flex items-center justify-center gap-1
                                {{ $unit['status']=='Available'?'bg-green-500':'' }}
                                {{ $unit['status']=='Occupied'?'bg-red-500':'' }}">
                                <i class="fas
                                    {{ $unit['status']=='Available'?'fa-check':'' }}
                                    {{ $unit['status']=='Occupied'?'fa-user':'' }} text-xs"></i>
                                {{ $unit['name'] }}
                            </span>
                        @endforeach
                    </div>

                    <!-- Bookings Accordion -->
                    <details class="mt-auto pt-4 border-t border-gray-100">
                        <summary class="cursor-pointer font-medium text-primary flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-list"></i> View Bookings
                            </span>
                        </summary>
                        <div class="mt-3 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-left">
                                    <tr>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($facility->bookings as $booking)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2">#{{ $booking['booking_id'] }}</td>
                                            <td class="px-3 py-2">{{ $booking['customer'] }}</td>
                                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($booking['date'])->format('M d, Y') }}</td>
                                            <td class="px-3 py-2">{{ $booking['quantity'] }}</td>
                                            <td class="px-3 py-2">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    {{ $booking['status']=='approved'?'bg-indigo-100 text-indigo-800':'' }}
                                                    {{ $booking['status']=='paid'?'bg-green-100 text-green-800':'' }}
                                                    {{ !in_array($booking['status'],['approved','paid'])?'bg-gray-100 text-gray-800':'' }}">
                                                    {{ ucfirst($booking['status']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-3 py-4 text-gray-400 text-center">
                                                <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                                No bookings found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </details>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function showTab(tabName){
    document.querySelectorAll('.tab-content').forEach(el=>el.classList.add('hidden'));
    document.getElementById(tabName+'-content').classList.remove('hidden');

    const tabs=['cottages','villas'];
    tabs.forEach(name=>{
        const tab=document.getElementById(name+'-tab');
        if(name===tabName){
            tab.classList.add('tab-active','text-primary');
            tab.classList.remove('text-gray-500');
        }else{
            tab.classList.remove('tab-active','text-primary');
            tab.classList.add('text-gray-500');
        }
    });
}
showTab('cottages');
</script>
@endsection
