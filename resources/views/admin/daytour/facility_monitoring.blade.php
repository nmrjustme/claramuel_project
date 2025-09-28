{{-- resources/views/admin/daytour/facility_monitoring.blade.php --}}

@extends('layouts.admin')

@php
    $active = 'facility_monitoring';

    $category_icons = [
        'default' => 'fa-building',
        'private villa' => 'fa-house-user',
        'tennoji' => 'fa-torii-gate',
        'japanese inspired' => 'fa-torii-gate',
        'hobbiton village' => 'fa-campground',
        'cottage' => 'fa-house',
        'cabana' => 'fa-umbrella-beach',
        'room' => 'fa-bed',
        'pool' => 'fa-swimming-pool',
        'hall' => 'fa-door-open',
    ];
@endphp

@section('title', 'Facility Monitoring')


@section('content')
<div x-data="{ activeTab: 'all', loading: false }" class="min-h-screen p-6 bg-gray-50">
    {{-- FontAwesome, AlpineJS & Flatpickr --}}
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Facility Monitoring</h1>
            <p class="mt-1 text-gray-600 text-sm">
                Overview for <time datetime="{{ $date }}" class="font-semibold text-red-600">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</time>
            </p>
        </div>

        {{-- Date Filter --}}
        <form id="filter-form" method="GET" action="{{ route('admin.daytour.facility_monitoring') }}" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <label for="flatpickr-date" class="text-sm font-medium text-gray-700">Select Date:</label>
            <input type="text" name="date" id="flatpickr-date" value="{{ $date }}"
                   class="rounded-md border border-gray-300 px-4 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                   placeholder="Select date" />
            <button id="filter-button" type="submit"
                    class="inline-flex items-center gap-2 rounded-md bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @php
            $cards = [
                'total_facilities' => ['label' => 'Total Facilities', 'icon' => 'fa-building', 'color' => 'bg-blue-50 text-blue-700'],
                'total_available' => ['label' => 'Available', 'icon' => 'fa-check-circle', 'color' => 'bg-green-50 text-green-700'],
                'total_booked' => ['label' => 'Occupied', 'icon' => 'fa-house-lock', 'color' => 'bg-red-50 text-red-700'],
                'overall_occupancy' => ['label' => 'Occupancy Rate', 'icon' => 'fa-chart-pie', 'color' => 'bg-yellow-50 text-yellow-700'],
            ];
        @endphp

        @foreach($cards as $key => $card)
            <div class="flex items-center gap-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-lg transition-shadow">
                <div class="flex h-14 w-14 items-center justify-center rounded-lg {{ $card['color'] }}">
                    <i class="fas {{ $card['icon'] }} fa-2x"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-gray-900">
                        {{ $key === 'overall_occupancy' ? number_format($summary[$key], 1) . '%' : $summary[$key] }}
                    </p>
                    <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Facility Tabs --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6">
            <nav class="flex gap-6 overflow-x-auto" aria-label="Facility categories">
                <button
                    @click="activeTab = 'all'"
                    :class="{ 'border-b-4 border-red-600 text-red-600 font-semibold': activeTab === 'all' }"
                    class="whitespace-nowrap py-4 px-3 text-gray-600 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 rounded-t transition"
                    type="button"
                >
                    <i class="fas fa-th-large mr-1"></i> All Facilities
                </button>

                @foreach($facilities->pluck('category')->unique() as $category)
                    @php $categorySlug = strtolower(str_replace(' ', '_', $category)); @endphp
                    <button
                        @click="activeTab = '{{ $categorySlug }}'"
                        :class="{ 'border-b-4 border-red-600 text-red-600 font-semibold': activeTab === '{{ $categorySlug }}' }"
                        class="whitespace-nowrap py-4 px-3 text-gray-600 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 rounded-t transition flex items-center gap-1"
                        type="button"
                    >
                        <i class="fas {{ $category_icons[strtolower($category)] ?? $category_icons['default'] }}"></i>
                        {{ $category }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div>
            {{-- All Facilities Tab --}}
            <div x-show="activeTab === 'all'" x-transition:enter.duration.200ms class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @forelse($facilities as $facility)
                    @include('admin.daytour.facility-card', ['facility' => $facility, 'category_icons' => $category_icons])
                @empty
                    <p class="text-gray-500 col-span-full text-center py-10">No facilities to display.</p>
                @endforelse
            </div>

            {{-- Category-specific Tabs --}}
            @foreach($facilities->pluck('category')->unique() as $category)
                @php $categorySlug = strtolower(str_replace(' ', '_', $category)); @endphp
                <div x-show="activeTab === '{{ $categorySlug }}'" x-transition:enter.duration.200ms class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($facilities->where('category', $category) as $facility)
                        @include('admin.daytour.facility-card', ['facility' => $facility, 'category_icons' => $category_icons])
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div x-show="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-100 bg-opacity-75 transition-opacity duration-300">
        <div class="flex flex-col items-center">
            <i class="fas fa-spinner fa-spin text-red-600 text-6xl"></i>
            <p class="mt-4 text-lg font-medium text-gray-700">Loading data...</p>
        </div>
    </div>
</div>

<script>
    // Initialize Flatpickr
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('flatpickr-date');
        const filterButton = document.getElementById('filter-button');
        const filterForm = document.getElementById('filter-form');

        flatpickr(dateInput, {
            dateFormat: "Y-m-d", 
            disableMobile: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr) {
                    filterForm.submit();
                }
            }
        });
        
        // Hide the filter button for a cleaner look with JavaScript enabled
        filterButton.style.display = 'none';
    });
</script>
@endsection