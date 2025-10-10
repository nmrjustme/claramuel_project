@extends('layouts.admin')
@section('title', 'Day Tour Analytics Dashboard')

@php
    $active = 'daytour-earnings'; // Update this to match your sidebar menu
@endphp

@section('content_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Use the same CSS styles from your Room Earnings dashboard */
    .min-h-screen {
        min-height: 100vh;
        position: relative;
    }

    .flex-1 {
        position: relative;
        z-index: 1;
    }

    .h-64 {
        position: relative;
        min-height: 16rem;
        z-index: 1;
    }

    #chartModal {
        z-index: 9999 !important;
    }

    #guestList {
        position: relative;
        z-index: 1;
        max-height: 20rem;
        overflow-y: auto;
    }

    #categoryLegend {
        min-height: 12rem;
        max-height: 15rem;
        overflow-y: auto;
    }

    @media (max-width: 768px) {
        .flex-wrap {
            gap: 0.5rem;
        }

        .flex-col {
            min-width: 0;
            flex: 1 1 auto;
        }

        .bg-white.rounded-xl.shadow-md.mb-6 {
            margin-bottom: 1rem;
        }
    }

    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    canvas {
        display: block;
        max-width: 100%;
        height: auto !important;
    }

    .loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
        background: rgba(255, 255, 255, 0.95);
    }

    /* Custom colors for Day Tour */
    .border-t-green-500 { border-top-color: #10B981 !important; }
    .border-t-orange-500 { border-top-color: #F59E0B !important; }
    .border-t-indigo-500 { border-top-color: #6366F1 !important; }
    
    .text-green-500 { color: #10B981 !important; }
    .text-orange-500 { color: #F59E0B !important; }
    .text-indigo-500 { color: #6366F1 !important; }
</style>
@endsection

@section('content')
<div class="min-h-screen px-6 py-6">
    <!-- Compact Header with Quick Stats -->
    <div class="bg-white rounded-xl shadow-md mb-6 p-4 border-t-4 border-t-green-500">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-swimming-pool text-green-600 text-xl"></i>
                <h1 class="text-green-600 font-bold text-xl">Day Tour Analytics Dashboard</h1>
            </div>

            <!-- Quick Stats Row -->
            <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center gap-1">
                    <span class="text-gray-600">Revenue:</span>
                    <span class="font-bold text-green-600" id="quickTotalRevenue">₱0</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-gray-600">Bookings:</span>
                    <span class="font-bold text-orange-600" id="quickTotalBookings">0</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-gray-600">Guests:</span>
                    <span class="font-bold text-indigo-600" id="quickTotalGuests">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Revenue -->
        <div class="bg-white rounded-xl p-4 shadow-md border-t-4 border-t-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-dollar-sign text-xs"></i> Total Revenue
                    </div>
                    <div class="text-xl font-bold my-1" id="totalRevenue">₱0</div>
                    <div class="text-gray-500 text-xs" id="revenuePeriod">This Month</div>
                </div>
                <div class="text-green-500 text-xs flex items-center gap-1" id="revenueChange">
                    <i class="fas fa-arrow-up text-xs"></i> <span id="revenueChangeValue">0%</span>
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="bg-white rounded-xl p-4 shadow-md border-t-4 border-t-orange-500">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-calendar-check text-xs"></i> Total Bookings
                    </div>
                    <div class="text-xl font-bold my-1" id="totalBookings">0</div>
                    <div class="text-gray-500 text-xs" id="bookingsPeriod">This Month</div>
                </div>
                <div class="text-green-500 text-xs flex items-center gap-1" id="bookingsChange">
                    <i class="fas fa-arrow-up text-xs"></i> <span id="bookingsChangeValue">0%</span>
                </div>
            </div>
        </div>

        <!-- Total Guests -->
        <div class="bg-white rounded-xl p-4 shadow-md border-t-4 border-t-indigo-500">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-users text-xs"></i> Total Guests
                    </div>
                    <div class="text-xl font-bold my-1" id="totalGuests">0</div>
                    <div class="text-gray-500 text-xs" id="guestsPeriod">This Month</div>
                </div>
                <div class="text-green-500 text-xs flex items-center gap-1" id="guestsChange">
                    <i class="fas fa-arrow-up text-xs"></i> <span id="guestsChangeValue">0%</span>
                </div>
            </div>
        </div>

        <!-- Top Category -->
        <div class="bg-white rounded-xl p-4 shadow-md border-t-4 border-t-purple-500">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-star text-xs"></i> Popular Category
                    </div>
                    <div class="text-xl font-bold my-1" id="topCategory">-</div>
                    <div class="text-gray-500 text-xs">Most Booked</div>
                </div>
                <div class="text-green-500 text-xs flex items-center gap-1" id="categoryChange">
                    <i class="fas fa-arrow-up text-xs"></i> <span id="categoryChangeValue">0%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Filter Bar -->
    <div class="bg-white rounded-xl shadow-md mb-6 p-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                <i class="fas fa-filter"></i> Filter Analytics
            </h2>

            <div class="flex flex-wrap gap-3 items-center w-full md:w-auto">
                <!-- Category -->
                <div class="flex flex-col">
                    <label for="category" class="text-gray-600 font-semibold text-xs mb-1">Category</label>
                    <select id="category" class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-32 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <option value="">All Categories</option>
                        <option value="Pool">Pool</option>
                        <option value="Park">Park</option>
                        <option value="Both">Both</option>
                    </select>
                </div>

                <!-- Facility Type -->
                <div class="flex flex-col">
					<label for="facilityType" class="text-gray-600 font-semibold text-xs mb-1">Facility</label>
					<select id="facilityType" class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-32 focus:outline-none focus:ring-1 focus:ring-green-500">
						<option value="">All Facilities</option>
						<option value="None">No Facility</option>
						@foreach($facilities as $facility)
							<option value="{{ $facility }}">{{ $facility }}</option>
						@endforeach
					</select>
				</div>

                <!-- Month -->
                <div class="flex flex-col">
                    <label for="month" class="text-gray-600 font-semibold text-xs mb-1">Month</label>
                    <select id="month" class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-28 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <option value="1">Jan</option>
                        <option value="2">Feb</option>
                        <option value="3">Mar</option>
                        <option value="4">Apr</option>
                        <option value="5">May</option>
                        <option value="6">Jun</option>
                        <option value="7">Jul</option>
                        <option value="8">Aug</option>
                        <option value="9">Sep</option>
                        <option value="10">Oct</option>
                        <option value="11">Nov</option>
                        <option value="12">Dec</option>
                    </select>
                </div>

                <!-- Year -->
                <div class="flex flex-col">
                    <label for="year" class="text-gray-600 font-semibold text-xs mb-1">Year</label>
                    <select id="year" class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-20 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <!-- Populated dynamically -->
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                    <button id="updateChart" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                        <i class="fas fa-filter"></i> Apply
                    </button>

                    <button id="refreshData" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>

                    <button id="exportData" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="bg-white rounded-xl shadow-md mb-6 overflow-hidden">
        <div class="p-3 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                <i class="fas fa-chart-line"></i> Revenue Trend
            </h2>
            <button class="text-xs text-green-500 font-medium expand-chart-btn" data-chart="revenue">
                <i class="fas fa-expand-alt mr-1"></i> Expand
            </button>
        </div>
        <div class="p-4">
            <div class="loading hidden flex-col items-center justify-center p-8 text-gray-600" id="loadingIndicator">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 text-green-500"></i>
                <p class="text-xs">Loading chart data...</p>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Two Column Layout for Guest Demographics and Category Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Guest Demographics -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                    <i class="fas fa-users"></i> Guest Demographics
                </h2>
                <button class="text-xs text-green-500 font-medium expand-chart-btn" data-chart="guests">
                    <i class="fas fa-expand-alt mr-1"></i> Expand
                </button>
            </div>
            <div class="p-4">
                <div class="h-64">
                    <canvas id="guestDemographicsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                    <i class="fas fa-chart-pie"></i> Category Distribution
                </h2>
                <button class="text-xs text-green-500 font-medium expand-chart-btn" data-chart="category">
                    <i class="fas fa-expand-alt mr-1"></i> Expand
                </button>
            </div>
            <div class="p-3">
                <div class="flex flex-row gap-3">
                    <div class="w-2/5">
                        <div class="h-48">
                            <canvas id="categoryPieChart"></canvas>
                        </div>
                    </div>
                    <div class="w-3/5" id="categoryLegend">
                        <!-- Legend will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Facility Utilization & Recent Bookings -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Facility Utilization -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                    <i class="fas fa-home"></i> Facility Utilization
                </h2>
                <button class="text-xs text-green-500 font-medium expand-chart-btn" data-chart="facility">
                    <i class="fas fa-expand-alt mr-1"></i> Expand
                </button>
            </div>
            <div class="p-4">
                <div class="h-64">
                    <canvas id="facilityUtilizationChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-3 border-b border-gray-200">
                <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                    <i class="fas fa-clock"></i> Recent Bookings
                </h2>
            </div>
            <div class="p-4">
                <div class="flex flex-col gap-2 max-h-64 overflow-y-auto" id="recentBookingsList">
                    <!-- Recent bookings will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details - Collapsible Section -->
    <div class="bg-white rounded-xl shadow-md mb-6 overflow-hidden">
        <div class="p-3 border-b border-gray-200 flex justify-between items-center cursor-pointer" id="bookingDetailsToggle">
            <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                <i class="fas fa-list-alt"></i> Booking Details
            </h2>
            <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform duration-300" id="bookingDetailsIcon"></i>
        </div>
        <div class="p-4 hidden" id="bookingDetailsContent">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Booking List -->
                <div class="lg:col-span-1">
                    <div class="text-xs font-semibold text-gray-600 mb-2">Select a Booking</div>
                    <div class="flex flex-col gap-2 max-h-80 overflow-y-auto pr-2" id="bookingList">
                        <!-- Booking list will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-4" id="bookingDetailView">
                        <div class="flex flex-col items-center justify-center p-6 text-gray-500">
                            <i class="fas fa-calendar-check text-3xl mb-2 text-gray-300"></i>
                            <p class="text-sm">Select a booking to view details</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expanded Chart Modal -->
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden" id="chartModal">
    <div class="bg-white rounded-xl shadow-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-auto">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold" id="modalTitle">Chart Title</h2>
            <button class="text-gray-500 hover:text-gray-700" id="closeModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="h-96">
                <canvas id="modalChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content_js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let revenueChart = null;
    let guestDemographicsChart = null;
    let categoryPieChart = null;
    let facilityUtilizationChart = null;
    let modalChart = null;
    let currentData = null;

    $(document).ready(function () {
        // Set CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Populate year dropdown
        populateYearDropdown();

        // Set current month and year
        const currentDate = new Date();
        $('#month').val(currentDate.getMonth() + 1);
        $('#year').val(currentDate.getFullYear());

        // Load initial data
        loadChartData();

        // Update chart when button is clicked
        $('#updateChart').click(function () {
            loadChartData();
        });

        // Refresh data
        $('#refreshData').click(function () {
            loadChartData();
        });

        // Export data
        $('#exportData').click(function () {
            exportData();
        });

        // Update when filters change
        $('#category, #facilityType, #month, #year').change(function () {
            loadChartData();
        });

        // Toggle booking details section
        $('#bookingDetailsToggle').click(function () {
            $('#bookingDetailsContent').slideToggle(300);
            $('#bookingDetailsIcon').toggleClass('fa-chevron-down fa-chevron-up');
        });

        // Expand chart functionality
        $('.expand-chart-btn').click(function () {
            const chartType = $(this).data('chart');
            expandChart(chartType);
        });

        // Close modal
        $('#closeModal').click(function () {
            $('#chartModal').addClass('hidden');
            if (modalChart) {
                modalChart.destroy();
                modalChart = null;
            }
        });
    });

    function populateYearDropdown() {
        const currentYear = new Date().getFullYear();
        const yearSelect = $('#year');
        yearSelect.empty();
        for (let i = currentYear; i >= currentYear - 5; i--) {
            yearSelect.append(`<option value="${i}">${i}</option>`);
        }
    }

    function loadChartData() {
        const category = $('#category').val();
        const facilityType = $('#facilityType').val();
        const month = $('#month').val();
        const year = $('#year').val();

        $('#loadingIndicator').removeClass('hidden').addClass('flex');

        $.ajax({
            url: '{{ route("day_tour.earnings.data") }}',
            type: 'GET',
            data: {
                category: category,
                facility_type: facilityType,
                month: month,
                year: year
            },
            success: function (response) {
                $('#loadingIndicator').removeClass('flex').addClass('hidden');

                if (response.success) {
                    currentData = response;
                    updateRevenueChart(response.revenue_data, response.labels);
                    updateGuestDemographicsChart(response.guest_data);
                    updateCategoryPieChart(response.category_data);
                    updateFacilityUtilizationChart(response.facility_data);
                    updateStats(response.stats);
                    updateRecentBookings(response.recent_bookings);
                    updateBookingList(response.bookings);
                } else {
                    alert('Error loading analytics data');
                }
            },
            error: function (xhr, status, error) {
                $('#loadingIndicator').removeClass('flex').addClass('hidden');
                console.error('Error loading analytics data:', error);
                alert('Error loading analytics data: ' + error);
            }
        });
    }

    function updateRevenueChart(revenueData, labels) {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        if (revenueChart) {
            revenueChart.destroy();
        }

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
        gradient.addColorStop(1, 'rgba(34, 197, 94, 0.6)');

        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: revenueData,
                    backgroundColor: gradient,
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function (context) {
                                return `₱${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function (value) {
                                return '₱' + value.toLocaleString();
                            },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    function updateGuestDemographicsChart(guestData) {
        const ctx = document.getElementById('guestDemographicsChart').getContext('2d');

        if (guestDemographicsChart) {
            guestDemographicsChart.destroy();
        }

        guestDemographicsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: guestData.labels,
                datasets: [{
                    label: 'Guests',
                    data: guestData.data,
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(99, 102, 241, 0.6)',
                        'rgba(245, 158, 11, 0.6)',
                        'rgba(16, 185, 129, 0.6)'
                    ],
                    borderColor: [
                        'rgb(99, 102, 241)',
                        'rgb(245, 158, 11)',
                        'rgb(16, 185, 129)',
                        'rgb(99, 102, 241)',
                        'rgb(245, 158, 11)',
                        'rgb(16, 185, 129)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.parsed.y} guests`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    function updateCategoryPieChart(categoryData) {
        const ctx = document.getElementById('categoryPieChart').getContext('2d');

        if (categoryPieChart) {
            categoryPieChart.destroy();
        }

        const categories = Object.keys(categoryData);
        const earnings = Object.values(categoryData);
        const totalEarnings = earnings.reduce((sum, earning) => sum + earning, 0);

        const backgroundColors = generateColors(categories.length);

        categoryPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: categories,
                datasets: [{
                    data: earnings,
                    backgroundColor: backgroundColors,
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = ((value / totalEarnings) * 100).toFixed(1);
                                return `${label}: ₱${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    duration: 800,
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        updateCompactCategoryLegend(categories, earnings, backgroundColors, totalEarnings);
    }

    function updateFacilityUtilizationChart(facilityData) {
        const ctx = document.getElementById('facilityUtilizationChart').getContext('2d');

        if (facilityUtilizationChart) {
            facilityUtilizationChart.destroy();
        }

        facilityUtilizationChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: facilityData.labels,
                datasets: [{
                    data: facilityData.data,
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11 },
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} bookings (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function generateColors(count) {
        const baseColors = [
            '#10B981', '#F59E0B', '#6366F1', '#EF4444', '#8B5CF6',
            '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#14B8A6'
        ];
        const colors = [];
        for (let i = 0; i < count; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    }

    function updateCompactCategoryLegend(categories, earnings, colors, totalEarnings) {
        const legendContainer = $('#categoryLegend');
        legendContainer.empty();

        categories.forEach((category, index) => {
            const earningsValue = earnings[index];
            const percentage = totalEarnings > 0 ? ((earningsValue / totalEarnings) * 100).toFixed(1) : 0;

            const legendItem = $(`
                <div class="flex items-center justify-between mb-1 p-1 hover:bg-white rounded text-xs transition-colors">
                    <div class="flex items-center gap-1 flex-1 min-w-0">
                       <div class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: ${colors[index]}"></div>
                       <span class="font-medium truncate" title="${category}">${category}</span>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                       <div class="font-semibold text-green-600 text-xs">₱${earningsValue.toLocaleString()}</div>
                    </div>
                </div>
             `);

            legendContainer.append(legendItem);
        });

        if (categories.length > 1) {
            const totalItem = $(`
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-200 text-xs font-bold">
                    <div class="text-gray-700">Total</div>
                    <div class="text-green-600">₱${totalEarnings.toLocaleString()}</div>
                </div>
             `);
            legendContainer.append(totalItem);
        }
    }

function updateStats(stats) {
    if (stats) {
        $('#totalRevenue').text('₱' + stats.total_revenue.toLocaleString());
        $('#totalBookings').text(stats.total_bookings);
        $('#totalGuests').text(stats.total_guests);
        $('#topCategory').text(stats.top_category);

        // Update percentage changes with proper formatting
        updatePercentageChange('revenue', stats.revenue_change || 0);
        updatePercentageChange('bookings', stats.bookings_change || 0);
        updatePercentageChange('guests', stats.guests_change || 0);

        // Update quick stats
        $('#quickTotalRevenue').text('₱' + stats.total_revenue.toLocaleString());
        $('#quickTotalBookings').text(stats.total_bookings);
        $('#quickTotalGuests').text(stats.total_guests);
        $('#quickTopCategory').text(stats.top_category);
    }
}

function updatePercentageChange(type, change) {
    const element = $('#' + type + 'Change');
    const valueElement = $('#' + type + 'ChangeValue');
    const iconElement = $('#' + type + 'Change i');
    
    const formattedChange = Math.abs(change).toFixed(1) + '%';
    
    if (change > 0) {
        element.removeClass('text-red-500').addClass('text-green-500');
        iconElement.removeClass('fa-arrow-down').addClass('fa-arrow-up');
        valueElement.text('+' + formattedChange);
    } else if (change < 0) {
        element.removeClass('text-green-500').addClass('text-red-500');
        iconElement.removeClass('fa-arrow-up').addClass('fa-arrow-down');
        valueElement.text('-' + formattedChange);
    } else {
        element.removeClass('text-green-500 text-red-500').addClass('text-gray-500');
        iconElement.removeClass('fa-arrow-up fa-arrow-down').addClass('fa-minus');
        valueElement.text('0%');
    }
}

    function updateRecentBookings(bookings) {
    const container = $('#recentBookingsList');
    container.empty();

    if (bookings && bookings.length > 0) {
        bookings.forEach(booking => {
            const bookingItem = $(`
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full ${booking.status === 'paid' ? 'bg-green-500' : 'bg-yellow-500'}"></div>
                        <div>
                            <div class="font-semibold">${booking.date}</div>
                            <div class="text-gray-500">${booking.customer_name}</div>
                            <div class="text-gray-400 text-xs">${booking.guests} guests • ${booking.category}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-green-600">₱${booking.amount}</div>
                        <div class="text-gray-500 text-xs">${booking.phone || 'No phone'}</div>
                    </div>
                </div>
            `);
            container.append(bookingItem);
        });
    } else {
        container.append('<div class="flex flex-col items-center justify-center p-4 text-gray-500 text-xs"><i class="fas fa-calendar-times text-xl mb-2 text-gray-300"></i><p>No recent bookings</p></div>');
    }
}

    function updateBookingList(bookings) {
        const bookingList = $('#bookingList');
        bookingList.empty();

        if (bookings && bookings.length > 0) {
            bookings.forEach((booking, index) => {
                const bookingItem = $('<div>').addClass('flex justify-between items-center p-3 bg-gray-50 rounded-lg text-xs cursor-pointer room-item transition-colors duration-150');
                if (index === 0) bookingItem.addClass('bg-green-50 border-l-2 border-l-green-500');

                $('<div>').addClass('font-semibold truncate').text(booking.reference).appendTo(bookingItem);
                $('<div>').addClass('font-bold text-green-500').text('₱' + booking.amount.toLocaleString()).appendTo(bookingItem);

                bookingItem.click(function () {
                    $('.room-item').removeClass('bg-green-50 border-l-2 border-l-green-500');
                    $(this).addClass('bg-green-50 border-l-2 border-l-green-500');
                    showBookingDetails(booking);
                });

                bookingList.append(bookingItem);
            });

            // Show details for first booking
            if (bookings.length > 0) {
                showBookingDetails(bookings[0]);
            }
        } else {
            bookingList.append('<div class="flex flex-col items-center justify-center p-4 text-gray-500 text-xs"><i class="fas fa-calendar-times text-xl mb-2 text-gray-300"></i><p>No bookings found</p></div>');
        }
    }

function showBookingDetails(booking) {
    const detailView = $('#bookingDetailView');
    detailView.empty();

    // Format facilities list
    let facilitiesHTML = '';
    if (booking.facilities && booking.facilities.length > 0) {
        facilitiesHTML = booking.facilities.map(facility => 
            `<div class="flex justify-between items-center p-2 bg-white rounded-lg shadow-sm text-xs">
                <div class="font-medium">${facility.name}</div>
                <div class="font-bold text-green-500">${facility.quantity} × ₱${facility.price}</div>
            </div>`
        ).join('');
    } else {
        facilitiesHTML = '<div class="flex flex-col items-center justify-center p-3 text-gray-500 text-xs"><p>No facilities booked</p></div>';
    }

    const detailHTML = `
        <div class="flex justify-between items-center mb-4">
            <div>
                <div class="font-bold">${booking.reference}</div>
                <div class="text-sm text-gray-600">${booking.customer_name}</div>
            </div>
            <div class="font-bold text-green-500">₱${booking.amount.toLocaleString()}</div>
        </div>
        
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm text-xs">
                <div class="font-bold">${booking.guest_count}</div>
                <div class="text-gray-500">Guests</div>
            </div>
            <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm text-xs">
                <div class="font-bold">${booking.category}</div>
                <div class="text-gray-500">Category</div>
            </div>
            <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm text-xs">
                <div class="font-bold ${booking.has_facility ? 'text-green-500' : 'text-gray-500'}">${booking.facilities && booking.facilities.length > 0 ? booking.facilities.length : 'No'} Facilities</div>
                <div class="text-gray-500">Facilities</div>
            </div>
            <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm text-xs">
                <div class="font-bold ${booking.status === 'paid' ? 'text-green-500' : 'text-yellow-500'}">${booking.status}</div>
                <div class="text-gray-500">Status</div>
            </div>
        </div>
        
        ${booking.facilities && booking.facilities.length > 0 ? `
        <div class="mb-4">
            <h3 class="text-sm font-semibold mb-2">Facilities Booked</h3>
            <div class="flex flex-col gap-2 max-h-24 overflow-y-auto text-xs">
                ${facilitiesHTML}
            </div>
        </div>
        ` : ''}
        
        <div class="mb-4">
            <h3 class="text-sm font-semibold mb-2">Customer Information</h3>
            <div class="bg-white rounded-lg p-3 text-xs">
                <div class="flex justify-between mb-1">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-medium">${booking.customer_name}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span class="text-gray-600">Phone:</span>
                    <span class="font-medium">${booking.phone || 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium">${booking.email || 'N/A'}</span>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3 class="text-sm font-semibold mb-2">Guest Breakdown</h3>
            <div class="flex flex-col gap-2 max-h-32 overflow-y-auto text-xs">
                ${booking.guest_breakdown && booking.guest_breakdown.length > 0 ?
                    booking.guest_breakdown.map(guest => `
                        <div class="flex justify-between items-center p-2 bg-white rounded-lg shadow-sm">
                            <div class="font-medium">${guest.type} (${guest.location})</div>
                            <div class="font-bold text-blue-500">${guest.count} × ₱${guest.rate}</div>
                        </div>
                     `).join('') :
                    '<div class="flex flex-col items-center justify-center p-3 text-gray-500 text-xs"><p>No guest data</p></div>'
                }
            </div>
        </div>
     `;

    detailView.html(detailHTML);
}

    function expandChart(chartType) {
        // Similar implementation to your Room Earnings expand functionality
        // This would clone the existing chart into the modal
        console.log('Expand chart:', chartType);
        // Implementation would be similar to your Room Earnings dashboard
    }

    function exportData() {
        const category = $('#category').val();
        const facilityType = $('#facilityType').val();
        const month = $('#month').val();
        const year = $('#year').val();

        const form = document.createElement('form');
        form.method = 'GET';
        form.action = '{{ route("day_tour.earnings.export") }}';

        const addInput = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        };

        addInput('category', category);
        addInput('facility_type', facilityType);
        addInput('month', month);
        addInput('year', year);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>
@endsection