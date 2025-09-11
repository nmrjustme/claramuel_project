@extends('layouts.admin')
@section('title', 'Cottage & Villa Monitoring')
@php
    $active = 'day_tour';
@endphp

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .occupancy-bar {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        background-color: #e5e7eb;
    }
    .occupancy-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }
    .stats-card {
        border-left: 4px solid;
        transition: transform 0.2s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Cottage & Villa Monitoring</h1>
            <p class="text-gray-600 mt-1">Real-time status and availability tracking</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.daytour.logs') }}" 
               class="flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-list mr-2"></i>View Logs
            </a>
            <button onclick="window.location.reload()" 
                    class="flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                <i class="fas fa-sync-alt mr-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Filters & Controls</h2>
                <span class="text-sm text-gray-500">{{ now()->format('F j, Y - g:i A') }}</span>
            </div>
        </div>
        <div class="p-5">
            <form method="GET" action="{{ route('admin.daytour.cottages_monitoring') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">View Date</label>
                        <input type="date" name="date" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ $date }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range (From)</label>
                        <input type="date" name="date_from" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ $dateFrom ?? '' }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range (To)</label>
                        <input type="date" name="date_to" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               value="{{ $dateTo ?? '' }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Facility Type</label>
                        <select name="facility_type" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="both" {{ $facilityType == 'both' ? 'selected' : '' }}>All Facilities</option>
                            <option value="cottage" {{ $facilityType == 'cottage' ? 'selected' : '' }}>Cottages Only</option>
                            <option value="villa" {{ $facilityType == 'villa' ? 'selected' : '' }}>Villas Only</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end mt-5">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="stats-card bg-white p-5 rounded-xl shadow-sm border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Facilities</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $summary['total_facilities'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-home text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Available</p>
                    <p class="text-2xl font-bold text-green-600">{{ $summary['total_available'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-white p-5 rounded-xl shadow-sm border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Occupied</p>
                    <p class="text-2xl font-bold text-red-600">{{ $summary['total_booked'] }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-users text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stats-card bg-white p-5 rounded-xl shadow-sm border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Occupancy Rate</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $summary['overall_occupancy'] }}%</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="occupancy-bar">
                    <div class="occupancy-fill bg-purple-500" style="width: {{ $summary['overall_occupancy'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-8">
        <h3 class="text-md font-semibold text-gray-800 mb-4">Status Legend</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center p-3 bg-green-50 rounded-lg">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                <span class="text-sm font-medium">Available</span>
            </div>
            <div class="flex items-center p-3 bg-red-50 rounded-lg">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                <span class="text-sm font-medium">Occupied</span>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                <span class="text-sm font-medium">Maintenance</span>
            </div>
            <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                <span class="text-sm font-medium">Cleaning</span>
            </div>
        </div>
    </div>

    <!-- Tabs for Cottages and Villas -->
    <div class="bg-white rounded-xl shadow-sm mb-8">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('cottages')" id="cottages-tab" class="cottages-tab py-4 px-6 text-center border-b-2 font-medium text-sm flex items-center justify-center w-1/2 border-blue-500 text-blue-600">
                    <i class="fas fa-campground mr-2"></i> Cottages ({{ $facilities->where('category', 'Cottage')->count() }})
                </button>
                <button onclick="showTab('villas')" id="villas-tab" class="villas-tab py-4 px-6 text-center border-b-2 font-medium text-sm flex items-center justify-center w-1/2 border-transparent text-gray-500">
                    <i class="fas fa-building mr-2"></i> Villas ({{ $facilities->where('category', 'Private Villa')->count() }})
                </button>
            </nav>
        </div>
        
        <!-- Cottages Tab -->
        <div id="cottages-content" class="tab-content p-5">
            @if($facilities->where('category', 'Cottage')->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($facilities->where('category', 'Cottage') as $facility)
                @include('admin.daytour.facility-card', ['facility' => $facility])
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-campground text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-700">No cottages found</h3>
                <p class="text-gray-500 mt-1">Try adjusting your filters or check back later</p>
            </div>
            @endif
        </div>

        <!-- Villas Tab -->
        <div id="villas-content" class="tab-content p-5" style="display: none;">
            @if($facilities->where('category', 'Private Villa')->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($facilities->where('category', 'Private Villa') as $facility)
                @include('admin.daytour.facility-card', ['facility' => $facility])
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-building text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-700">No villas found</h3>
                <p class="text-gray-500 mt-1">Try adjusting your filters or check back later</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center animate-fade-in">
        <i class="fas fa-check-circle mr-3 text-xl"></i>
        <div>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-4 text-green-100 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="fixed bottom-6 right-6 bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center animate-fade-in">
        <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
        <div>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-4 text-red-100 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
</div>

<script>
    // Tab functionality
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.style.display = 'none';
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.cottages-tab, .villas-tab').forEach(tab => {
            tab.classList.remove('border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        document.getElementById(tabName + '-content').style.display = 'block';
        
        // Add active class to selected tab
        document.getElementById(tabName + '-tab').classList.remove('border-transparent', 'text-gray-500');
        document.getElementById(tabName + '-tab').classList.add('border-blue-500', 'text-blue-600');
    }

    // Auto-refresh the page every 5 minutes to keep data current
    setTimeout(function() {
        window.location.reload();
    }, 300000);

    // Auto-hide success/error messages after 5 seconds
    @if(session('success') || session('error'))
    setTimeout(function() {
        document.querySelectorAll('.fixed').forEach(function(el) {
            el.style.display = 'none';
        });
    }, 5000);
    @endif
</script>
@endsection