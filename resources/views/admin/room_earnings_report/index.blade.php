@extends('layouts.admin')
@section('title', 'Room Earnings Dashboard')

@php
    $active = 'earnings';
@endphp

@section('content_css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Prevent overlapping for Room Earnings Dashboard specifically */
        .min-h-screen {
            min-height: 100vh;
            position: relative;
        }

        /* Ensure content doesn't conflict with sidebar */
        .flex-1 {
            position: relative;
            z-index: 1;
        }

        /* Chart container overlapping prevention */
        .h-64 {
            position: relative;
            min-height: 16rem;
            z-index: 1;
        }

        /* Prevent modal conflicts with sidebar */
        #chartModal {
            z-index: 9999 !important;
        }

        /* Room list scrolling */
        #roomList {
            position: relative;
            z-index: 1;
            max-height: 20rem;
            overflow-y: auto;
        }

        /* Category legend spacing */
        #categoryLegend {
            min-height: 12rem;
            max-height: 15rem;
            overflow-y: auto;
        }

        /* Filter bar overlapping prevention */
        @media (max-width: 768px) {
            .flex-wrap {
                gap: 0.5rem;
            }

            .flex-col {
                min-width: 0;
                flex: 1 1 auto;
            }

            /* Ensure filter elements stack properly */
            .bg-white.rounded-xl.shadow-md.mb-6 {
                margin-bottom: 1rem;
            }
        }

        /* Text truncation for long content */
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Chart responsive behavior */
        canvas {
            display: block;
            max-width: 100%;
            height: auto !important;
        }

        /* Loading states */
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
        }

        /* Grid layout overlapping prevention */
        @media (max-width: 1024px) {
            .lg\:grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
        }

        /* Mobile-specific fixes */
        @media (max-width: 640px) {
            /* Stack grid elements */
            .grid-cols-1 {
                grid-template-columns: 1fr;
            }

            /* Add spacing between cards */
            .gap-4 {
                gap: 1rem;
            }

            /* Ensure buttons don't overlap */
            .flex-wrap {
                justify-content: stretch;
            }

            .flex-wrap>button {
                flex: 1;
                min-width: 120px;
            }
        }

        /* Pie chart and legend layout */
        @media (max-width: 768px) {
            .flex-row {
                flex-direction: column;
            }

            .w-2\/5,
            .w-3\/5 {
                width: 100%;
            }
        }

        /* Scrollbar styling */
        .overflow-y-auto {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        .overflow-y-auto::-webkit-scrollbar {
            width: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 2px;
        }

        /* Ensure content doesn't overlap with fixed header */
        @media (max-width: 768px) {
            .min-h-screen.px-6.py-6 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
        }

        /* Room details section */
        #roomDetailsContent {
            position: relative;
            z-index: 1;
        }

        /* Modal content */
        .max-h-\[90vh\] {
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Stats cards text overflow */
        .text-xl {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Filter select elements */
        select {
            max-width: 100%;
        }

        /* Ensure proper spacing in two-column layout */
        @media (min-width: 1024px) {
            .gap-6 {
                gap: 1.5rem;
            }
        }

        /* Button group spacing */
        .flex-wrap.gap-2 {
            gap: 0.5rem;
        }

        /* Header quick stats */
        @media (max-width: 768px) {
            .flex-wrap.gap-4 {
                gap: 1rem;
                justify-content: space-between;
            }
        }

        /* Chart expand buttons */
        .expand-chart-btn {
            position: relative;
            z-index: 2;
        }

        /* Ensure room items don't overlap */
        .room-item {
            margin-bottom: 0.5rem;
        }

        .room-item:last-child {
            margin-bottom: 0;
        }

        /* Loading indicator positioning */
        #loadingIndicator {
            z-index: 20;
        }

        /* Prevent sidebar overlap conflicts */
        @media (min-width: 768px) {
            .md\:ml-64 {
                margin-left: 16rem;
            }
        }

        /* Ensure main content area doesn't conflict with sidebar */
        .flex.min-h-screen {
            position: relative;
        }

        /* Toast notification z-index */
        .toast {
            z-index: 10000 !important;
        }

        /* Dropdown menu z-index */
        .absolute.z-50 {
            z-index: 50;
        }

        /* Specific fixes for the accounting page */
        .bg-white.rounded-xl.shadow-md {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
        }

        /* Ensure filter bar doesn't overlap on very small screens */
        @media (max-width: 480px) {
            .min-h-screen.px-6.py-6 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .p-4 {
                padding: 0.75rem;
            }

            .text-xl {
                font-size: 1.25rem;
                line-height: 1.75rem;
            }
        }

        /* Print styles to prevent overlap */
        @media print {
            .bg-white {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .grid-cols-1,
            .lg\:grid-cols-2,
            .lg\:grid-cols-3 {
                display: block !important;
            }
        }

        /* Fix for cancellation section */
        .cancellation-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        /* Ensure cancellation charts are properly sized */
        .cancellation-chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }

        /* Fix for refund table responsiveness */
        .refund-table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Loading state for cancellation section */
        .cancellation-loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }
    </style>
@endsection

@section('content')
    <div class="min-h-screen px-6 py-6">
        <!-- Compact Header with Quick Stats -->
        <div class="bg-white rounded-xl shadow-md mb-6 p-4 border-t-4 border-t-blue-500">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    <h1 class="text-blue-600 font-bold text-xl">Room Earnings Dashboard</h1>
                </div>

                <!-- Quick Stats Row -->
                <div class="flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center gap-1">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-bold text-blue-600" id="quickTotalEarnings">₱0</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-gray-600">Top:</span>
                        <span class="font-bold text-cyan-600" id="quickTopCategory">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-md border-t-4 border-t-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-dollar-sign text-xs"></i> Total Earnings
                        </div>
                        <div class="text-xl font-bold my-1" id="totalEarnings">₱0</div>
                        <div class="text-gray-500 text-xs" id="earningsPeriod">This Month</div>
                    </div>
                    <div class="text-green-500 text-xs flex items-center gap-1" id="earningsChange">
                        <i class="fas fa-arrow-up text-xs"></i> <span id="earningsChangeValue">0%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-md border-t-4 border-t-purple-600">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-bed text-xs"></i> Rooms Booked
                        </div>
                        <div class="text-xl font-bold my-1" id="roomsBooked">0</div>
                        <div class="text-gray-500 text-xs" id="bookingsPeriod">This Month</div>
                    </div>
                    <div class="text-green-500 text-xs flex items-center gap-1" id="bookingsChange">
                        <i class="fas fa-arrow-up text-xs"></i> <span id="bookingsChangeValue">0%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-md border-t-4 border-t-cyan-400">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-star text-xs"></i> Top Category
                        </div>
                        <div class="text-xl font-bold my-1" id="topCategory">-</div>
                        <div class="text-gray-500 text-xs">Highest Earnings</div>
                    </div>
                    <div class="text-green-500 text-xs flex items-center gap-1" id="topCategoryChange">
                        <i class="fas fa-arrow-up text-xs"></i> <span id="topCategoryChangeValue">0%</span>
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
                        <select id="category"
                            class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-32 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">All</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month -->
                    <div class="flex flex-col">
                        <label for="month" class="text-gray-600 font-semibold text-xs mb-1">Month</label>
                        <select id="month"
                            class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-28 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">All</option>
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
                        <select id="year"
                            class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-20 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <!-- Populated dynamically -->
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                        <button id="updateChart"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                            <i class="fas fa-filter"></i> Apply
                        </button>

                        <button id="refreshData"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>

                        <button id="exportData"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings Trend Chart -->
        <div class="bg-white rounded-xl shadow-md mb-6 overflow-hidden">
            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                    <i class="fas fa-chart-bar"></i> Earnings Trend
                </h2>
                <button class="text-xs text-blue-500 font-medium expand-chart-btn" data-chart="earnings">
                    <i class="fas fa-expand-alt mr-1"></i> Expand
                </button>
            </div>
            <div class="p-4">
                <div class="loading hidden flex-col items-center justify-center p-8 text-gray-600" id="loadingIndicator">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2 text-blue-500"></i>
                    <p class="text-xs">Loading chart data...</p>
                </div>
                <div class="h-64">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Two Column Layout for Period Comparison and Earnings by Category -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Period Comparison Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                        <i class="fas fa-chart-line"></i> Period Comparison
                    </h2>
                    <button class="text-xs text-blue-500 font-medium expand-chart-btn" data-chart="comparison">
                        <i class="fas fa-expand-alt mr-1"></i> Expand
                    </button>
                </div>
                <div class="p-4">
                    <div class="h-64">
                        <canvas id="comparisonChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Earnings by Category Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                        <i class="fas fa-chart-pie"></i> Earnings by Category
                    </h2>
                    <button class="text-xs text-blue-500 font-medium expand-chart-btn" data-chart="category">
                        <i class="fas fa-expand-alt mr-1"></i> Expand
                    </button>
                </div>
                <div class="p-3">
                    <!-- Compact side-by-side layout -->
                    <div class="flex flex-row gap-3">
                        <!-- Chart - smaller but still visible -->
                        <div class="w-2/5">
                            <div class="h-48">
                                <canvas id="categoryPieChart"></canvas>
                            </div>
                        </div>

                        <!-- Legend - compact list -->
                        <div class="w-3/5" id="categoryLegend">
                            <!-- Legend will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancellation & Refund Analytics -->
        <div class="bg-white rounded-xl shadow-md mb-6 overflow-hidden">
            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                    <i class="fas fa-ban text-red-500"></i> Cancellation & Refund Analytics
                </h2>
                <button class="text-xs text-blue-500 font-medium expand-chart-btn" data-chart="cancellations">
                    <i class="fas fa-expand-alt mr-1"></i> Expand
                </button>
            </div>
            <div class="p-4">
                <!-- Loading state for cancellation data -->
                <div class="cancellation-loading hidden flex-col items-center justify-center p-8 text-gray-600" id="cancellationLoading">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2 text-red-500"></i>
                    <p class="text-xs">Loading cancellation data...</p>
                </div>

                <!-- Quick Stats Row -->
                <div class="cancellation-stats-grid mb-6">
                    <div class="bg-red-50 rounded-lg p-4 border-l-4 border-l-red-500">
                        <div class="text-red-600 text-xs font-semibold flex items-center gap-1 mb-1">
                            <i class="fas fa-times-circle"></i> Cancelled Bookings
                        </div>
                        <div class="text-xl font-bold text-red-700" id="cancelledBookings">0</div>
                        <div class="text-red-500 text-xs" id="cancellationRate">0% Rate</div>
                    </div>

                    <div class="bg-orange-50 rounded-lg p-4 border-l-4 border-l-orange-500">
                        <div class="text-orange-600 text-xs font-semibold flex items-center gap-1 mb-1">
                            <i class="fas fa-money-bill-wave"></i> Total Refunds
                        </div>
                        <div class="text-xl font-bold text-orange-700" id="totalRefunds">₱0</div>
                        <div class="text-orange-500 text-xs" id="refundRate">0% of Revenue</div>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-l-blue-500">
                        <div class="text-blue-600 text-xs font-semibold flex items-center gap-1 mb-1">
                            <i class="fas fa-percentage"></i> Full Refunds
                        </div>
                        <div class="text-xl font-bold text-blue-700" id="fullRefunds">0</div>
                        <div class="text-blue-500 text-xs" id="fullRefundAmount">₱0</div>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-l-purple-500">
                        <div class="text-purple-600 text-xs font-semibold flex items-center gap-1 mb-1">
                            <i class="fas fa-half"></i> Partial Refunds
                        </div>
                        <div class="text-xl font-bold text-purple-700" id="partialRefunds">0</div>
                        <div class="text-purple-500 text-xs" id="partialRefundAmount">₱0</div>
                    </div>
                </div>

                <!-- Two Column Layout for Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Cancellation Reasons -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-gray-900 font-semibold text-sm mb-3 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-red-500"></i> Cancellation Reasons
                        </h3>
                        <div class="cancellation-chart-container">
                            <canvas id="cancellationReasonsChart"></canvas>
                        </div>
                    </div>

                    <!-- Refund Trends -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-gray-900 font-semibold text-sm mb-3 flex items-center gap-2">
                            <i class="fas fa-chart-line text-orange-500"></i> Refund Trends
                        </h3>
                        <div class="cancellation-chart-container">
                            <canvas id="refundTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Refund Details Table -->
                <div class="mt-6">
                    <h3 class="text-gray-900 font-semibold text-sm mb-3 flex items-center gap-2">
                        <i class="fas fa-list text-green-500"></i> Recent Refunds
                    </h3>
                    <div class="refund-table-container">
                        <table class="min-w-full text-xs bg-white rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-3 text-left">Booking ID</th>
                                    <th class="py-2 px-3 text-left">Room</th>
                                    <th class="py-2 px-3 text-left">Refund Date</th>
                                    <th class="py-2 px-3 text-right">Amount</th>
                                    <th class="py-2 px-3 text-left">Type</th>
                                    <th class="py-2 px-3 text-left">Reason</th>
                                </tr>
                            </thead>
                            <tbody id="refundDetailsTable" class="divide-y divide-gray-200">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Details - Collapsible Section -->
        <div class="bg-white rounded-xl shadow-md mb-6 overflow-hidden">
            <div class="p-3 border-b border-gray-200 flex justify-between items-center cursor-pointer"
                id="roomDetailsToggle">
                <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                    <i class="fas fa-door-open"></i> Room Details
                </h2>
                <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform duration-300"
                    id="roomDetailsIcon"></i>
            </div>
            <div class="p-4 hidden" id="roomDetailsContent">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Room List -->
                    <div class="lg:col-span-1">
                        <div class="text-xs font-semibold text-gray-600 mb-2">Select a Room</div>
                        <div class="flex flex-col gap-2 max-h-80 overflow-y-auto pr-2" id="roomList">
                            <!-- Room list will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Room Details -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 rounded-lg p-4" id="roomDetailView">
                            <div class="flex flex-col items-center justify-center p-6 text-gray-500">
                                <i class="fas fa-door-open text-3xl mb-2 text-gray-300"></i>
                                <p class="text-sm">Select a room to view details</p>
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
        let earningsChart = null;
        let comparisonChart = null;
        let categoryPieChart = null;
        let modalChart = null;
        let currentData = null;
        let cancellationReasonsChart = null;
        let refundTrendsChart = null;

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

            // Load initial chart and comparison data
            loadChartData();
            loadComparisonData();

            // Update chart when button is clicked
            $('#updateChart').click(function () {
                loadChartData();
                loadComparisonData();
            });

            // Refresh data
            $('#refreshData').click(function () {
                loadChartData();
                loadComparisonData();
            });

            // Export data
            $('#exportData').click(function () {
                exportData();
            });

            // Update when filters change
            $('#category').change(function () {
                loadChartData();
                loadComparisonData();
            });

            // Update when month/year changes
            $('#month, #year').change(function () {
                loadChartData();
                loadComparisonData();
            });

            // Toggle room details section
            $('#roomDetailsToggle').click(function () {
                $('#roomDetailsContent').slideToggle(300);
                $('#roomDetailsIcon').toggleClass('fa-chevron-down fa-chevron-up');
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

            // Load cancellation and refund data when page loads
            loadCancellationRefundData();

            // Update cancellation data when filters change
            $('#updateChart, #refreshData').click(function () {
                loadCancellationRefundData();
            });

            // Add expand functionality for cancellation chart
            $('.expand-chart-btn[data-chart="cancellations"]').click(function () {
                expandCancellationChart();
            });
        });

        function populateYearDropdown() {
            const currentYear = new Date().getFullYear();
            const yearSelect = $('#year');

            // Clear existing options
            yearSelect.empty();

            // Add options for current year and previous 5 years
            for (let i = currentYear; i >= currentYear - 5; i--) {
                yearSelect.append(`<option value="${i}">${i}</option>`);
            }
        }

        function loadChartData() {
            const category = $('#category').val();
            const month = $('#month').val();
            const year = $('#year').val();

            $('#loadingIndicator').removeClass('hidden').addClass('flex');

            $.ajax({
                url: '{{ route("earnings.data") }}',
                type: 'GET',
                data: {
                    category: category,
                    month: month,
                    year: year
                },
                success: function (response) {
                    $('#loadingIndicator').removeClass('flex').addClass('hidden');

                    if (response.success) {
                        currentData = response;
                        updateChart(response.earnings, response.labels, response.currency);
                        updateStats(response.stats);
                        updateRoomList(response.rooms);

                        // Update pie chart with category data
                        if (response.categoryEarnings) {
                            updateCategoryPieChart(response.categoryEarnings);
                        }
                    } else {
                        alert('Error loading chart data');
                    }
                },
                error: function (xhr, status, error) {
                    $('#loadingIndicator').removeClass('flex').addClass('hidden');
                    console.error('Error loading chart data:', error);
                    alert('Error loading chart data: ' + error);
                }
            });
        }

        function loadComparisonData() {
            const category = $('#category').val();
            const month = $('#month').val();
            const year = $('#year').val();

            $.ajax({
                url: '{{ route("earnings.comparison") }}',
                type: 'GET',
                data: {
                    category: category,
                    month: month,
                    year: year
                },
                success: function (response) {
                    if (response.success) {
                        updateComparisonChart(response);
                    } else {
                        alert('Error loading comparison data');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error loading comparison data:', error);
                    alert('Error loading comparison data: ' + error);
                }
            });
        }

        function updateCategoryPieChart(categoryEarnings) {
            const ctx = document.getElementById('categoryPieChart').getContext('2d');

            // Destroy existing chart if it exists
            if (categoryPieChart) {
                categoryPieChart.destroy();
            }

            const categories = Object.keys(categoryEarnings);
            const earnings = Object.values(categoryEarnings);
            const totalEarnings = earnings.reduce((sum, earning) => sum + earning, 0);

            // Generate colors for each category
            const backgroundColors = generateColors(categories.length);

            // Create pie chart with compact options
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
                        legend: {
                            display: false
                        },
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

            // Update compact legend
            updateCompactCategoryLegend(categories, earnings, backgroundColors, totalEarnings);
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
                           <div class="font-semibold text-blue-600 text-xs">₱${earningsValue.toLocaleString()}</div>
                        </div>
                    </div>
                 `);

                legendContainer.append(legendItem);
            });

            // Add compact total
            if (categories.length > 1) {
                const totalItem = $(`
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-200 text-xs font-bold">
                        <div class="text-gray-700">Total</div>
                        <div class="text-blue-600">₱${totalEarnings.toLocaleString()}</div>
                    </div>
                 `);
                legendContainer.append(totalItem);
            }
        }

        function generateColors(count) {
            const baseColors = [
                '#4361ee', '#7209b7', '#4cc9f0', '#f72585', '#4895ef',
                '#3a0ca3', '#4cc9f0', '#f72585', '#4361ee', '#7209b7',
                '#4895ef', '#3a0ca3'
            ];

            const colors = [];
            for (let i = 0; i < count; i++) {
                colors.push(baseColors[i % baseColors.length]);
            }
            return colors;
        }

        function updateComparisonChart(response) {
            const ctx = document.getElementById('comparisonChart').getContext('2d');

            if (comparisonChart) {
                comparisonChart.destroy();
            }

            const years = Object.keys(response.data).sort((a, b) => b - a);
            const datasets = [];
            const colors = [
                { border: 'rgba(67, 97, 238, 1)', background: 'rgba(67, 97, 238, 0.1)' },
                { border: 'rgba(114, 9, 183, 1)', background: 'rgba(114, 9, 183, 0.1)' },
                { border: 'rgba(76, 201, 240, 1)', background: 'rgba(76, 201, 240, 0.1)' }
            ];

            years.forEach((year, index) => {
                datasets.push({
                    label: year,
                    data: response.data[year],
                    borderColor: colors[index] ? colors[index].border : getRandomColor(),
                    backgroundColor: colors[index] ? colors[index].background : getRandomColor(0.1),
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                });
            });

            comparisonChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: response.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 11
                                },
                                padding: 15
                            }
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function (value) {
                                    return '₱' + value.toLocaleString();
                                },
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        }

        function getRandomColor(alpha = 1) {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        function updateChart(earnings, labels, currency) {
            const ctx = document.getElementById('earningsChart').getContext('2d');

            // Destroy existing chart if it exists
            if (earningsChart) {
                earningsChart.destroy();
            }

            // Create gradient for bars
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(67, 97, 238, 0.8)');
            gradient.addColorStop(1, 'rgba(114, 9, 183, 0.6)');

            // Create new chart
            earningsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Earnings',
                        data: earnings,
                        backgroundColor: gradient,
                        borderColor: 'rgba(67, 97, 238, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false
                        },
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
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function (value) {
                                    return '₱' + value.toLocaleString();
                                },
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        }

        function updateStats(stats) {
            if (stats) {
                $('#totalEarnings').text('₱' + stats.totalEarnings.toLocaleString());
                $('#roomsBooked').text(stats.roomsBooked);
                $('#topCategory').text(stats.topCategory);

                // Update quick stats
                $('#quickTotalEarnings').text('₱' + stats.totalEarnings.toLocaleString());
                $('#quickTopCategory').text(stats.topCategory);

                // Update comparison values if available
                if (stats.comparison) {
                    updateComparisonValues(stats.comparison);
                }

                // Update period labels based on selected month/year
                const monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                const month = monthNames[parseInt($('#month').val()) - 1] || 'All Months';
                const year = $('#year').val();
                $('#earningsPeriod').text(`${month} ${year}`);
                $('#bookingsPeriod').text(`${month} ${year}`);
            }
        }

        function updateComparisonValues(comparison) {
            // Update earnings change
            const earningsChange = comparison.earnings_change || 0;
            $('#earningsChangeValue').text(Math.abs(earningsChange) + '%');
            if (earningsChange >= 0) {
                $('#earningsChange').removeClass('text-red-500').addClass('text-green-500');
                $('#earningsChange i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
            } else {
                $('#earningsChange').removeClass('text-green-500').addClass('text-red-500');
                $('#earningsChange i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
            }

            // Update bookings change
            const bookingsChange = comparison.bookings_change || 0;
            $('#bookingsChangeValue').text(Math.abs(bookingsChange) + '%');
            if (bookingsChange >= 0) {
                $('#bookingsChange').removeClass('text-red-500').addClass('text-green-500');
                $('#bookingsChange i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
            } else {
                $('#bookingsChange').removeClass('text-green-500').addClass('text-red-500');
                $('#bookingsChange i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
            }
        }

        function updateRoomList(rooms) {
            const roomList = $('#roomList');
            roomList.empty();

            if (rooms && rooms.length > 0) {
                rooms.forEach((room, index) => {
                    const roomItem = $('<div>').addClass('flex justify-between items-center p-3 bg-gray-50 rounded-lg text-xs cursor-pointer room-item transition-colors duration-150');
                    if (index === 0) roomItem.addClass('bg-blue-50 border-l-2 border-l-blue-500');

                    $('<div>').addClass('font-semibold truncate').text(room.name).appendTo(roomItem);
                    $('<div>').addClass('font-bold text-blue-500').text('₱' + room.earnings.toLocaleString()).appendTo(roomItem);

                    roomItem.click(function () {
                        $('.room-item').removeClass('bg-blue-50 border-l-2 border-l-blue-500');
                        $(this).addClass('bg-blue-50 border-l-2 border-l-blue-500');
                        showRoomDetails(room);
                    });

                    roomList.append(roomItem);
                });

                // Show details for first room
                if (rooms.length > 0) {
                    showRoomDetails(rooms[0]);
                }
            } else {
                roomList.append('<div class="flex flex-col items-center justify-center p-4 text-gray-500 text-xs"><i class="fas fa-door-closed text-xl mb-2 text-gray-300"></i><p>No rooms found</p></div>');
            }
        }

        function showRoomDetails(room) {
            const detailView = $('#roomDetailView');
            detailView.empty();

            // Create room details HTML
            const detailHTML = `
                <div class="flex justify-between items-center mb-4">
                    <div class="font-bold">${room.name}</div>
                    <div class="font-bold text-blue-500">₱${room.earnings.toLocaleString()}</div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm text-xs">
                        <div class="font-bold">${room.bookings}</div>
                        <div class="text-gray-500">Bookings</div>
                    </div>
                    <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm text-xs">
                        <div class="font-bold">${room.occupancy}%</div>
                        <div class="text-gray-500">Occupancy</div>
                    </div>
                    <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm text-xs">
                        <div class="font-bold">₱${room.averageRate}</div>
                        <div class="text-gray-500">Avg. Rate</div>
                    </div>
                    <div class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm text-xs">
                        <div class="font-bold">${room.category}</div>
                        <div class="text-gray-500">Category</div>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-semibold mb-2">Recent Bookings</h3>
                    <div class="flex flex-col gap-2 max-h-32 overflow-y-auto text-xs">
                        ${room.recentBookings && room.recentBookings.length > 0 ?
                            room.recentBookings.map(booking => `
                                <div class="flex justify-between items-center p-2 bg-white rounded-lg shadow-sm">
                                    <div class="font-medium">${booking.date}</div>
                                    <div class="font-bold text-blue-500">₱${booking.amount}</div>
                                </div>
                            `).join('') :
                            '<div class="flex flex-col items-center justify-center p-3 text-gray-500 text-xs"><p>No recent bookings</p></div>'
                        }
                    </div>
                </div>
            `;

            detailView.html(detailHTML);
        }

        function expandChart(chartType) {
            const modal = $('#chartModal');
            const modalTitle = $('#modalTitle');
            const modalCanvas = document.getElementById('modalChart').getContext('2d');

            // Set modal title
            if (chartType === 'earnings') {
                modalTitle.text('Earnings Trend');
            } else if (chartType === 'comparison') {
                modalTitle.text('Period Comparison');
            } else if (chartType === 'category') {
                modalTitle.text('Earnings by Category');
            }

            // Show modal
            modal.removeClass('hidden');

            // Create expanded chart
            if (chartType === 'earnings' && earningsChart) {
                // Clone earnings chart data
                const data = earningsChart.data;
                modalChart = new Chart(modalCanvas, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: data.datasets[0].label,
                            data: data.datasets[0].data,
                            backgroundColor: data.datasets[0].backgroundColor,
                            borderColor: data.datasets[0].borderColor,
                            borderWidth: data.datasets[0].borderWidth,
                            borderRadius: data.datasets[0].borderRadius,
                            borderSkipped: data.datasets[0].borderSkipped,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                padding: 12,
                                cornerRadius: 8,
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
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: function (value) {
                                        return '₱' + value.toLocaleString();
                                    },
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            } else if (chartType === 'comparison' && comparisonChart) {
                // Clone comparison chart data
                const data = comparisonChart.data;
                modalChart = new Chart(modalCanvas, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 14
                                    },
                                    padding: 20
                                }
                            },
                            title: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: function (value) {
                                        return '₱' + value.toLocaleString();
                                    },
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            } else if (chartType === 'category' && categoryPieChart) {
                // Clone pie chart data
                const data = categoryPieChart.data;
                modalChart = new Chart(modalCanvas, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.datasets[0].data,
                            backgroundColor: data.datasets[0].backgroundColor,
                            borderColor: '#ffffff',
                            borderWidth: 3,
                            hoverOffset: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: {
                                        size: 14
                                    },
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ₱${value.toLocaleString()} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }
        }

        // Load cancellation and refund data
        function loadCancellationRefundData() {
            const category = $('#category').val();
            const month = $('#month').val();
            const year = $('#year').val();

            $('#cancellationLoading').removeClass('hidden').addClass('flex');

            $.ajax({
                url: '{{ route("earnings.cancellation-refund-data") }}',
                type: 'GET',
                data: {
                    category: category,
                    month: month,
                    year: year
                },
                success: function (response) {
                    $('#cancellationLoading').removeClass('flex').addClass('hidden');

                    if (response.success) {
                        updateCancellationRefundStats(response.stats);
                        updateCancellationReasonsChart(response.cancellationReasons);
                        updateRefundTrendsChart(response.refundTrends);
                        updateRefundDetailsTable(response.recentRefunds);
                    } else {
                        console.error('Error loading cancellation data:', response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $('#cancellationLoading').removeClass('flex').addClass('hidden');
                    console.error('Error loading cancellation/refund data:', error);
                }
            });
        }

        // Update cancellation and refund statistics
        function updateCancellationRefundStats(stats) {
            if (stats) {
                $('#cancelledBookings').text(stats.cancelledBookings || 0);
                $('#cancellationRate').text((stats.cancellationRate || 0) + '% Rate');
                $('#totalRefunds').text('₱' + (stats.totalRefunds || 0).toLocaleString());
                $('#refundRate').text((stats.refundRate || 0) + '% of Revenue');
                $('#fullRefunds').text(stats.fullRefunds || 0);
                $('#fullRefundAmount').text('₱' + (stats.fullRefundAmount || 0).toLocaleString());
                $('#partialRefunds').text(stats.partialRefunds || 0);
                $('#partialRefundAmount').text('₱' + (stats.partialRefundAmount || 0).toLocaleString());
            }
        }

        // Update cancellation reasons chart
        function updateCancellationReasonsChart(reasonsData) {
            const ctx = document.getElementById('cancellationReasonsChart').getContext('2d');

            // Destroy existing chart if it exists
            if (cancellationReasonsChart) {
                cancellationReasonsChart.destroy();
            }

            if (reasonsData && Object.keys(reasonsData).length > 0) {
                const labels = Object.keys(reasonsData);
                const data = Object.values(reasonsData);

                cancellationReasonsChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                '#ef4444', '#f97316', '#f59e0b', '#eab308',
                                '#84cc16', '#22c55e', '#14b8a6', '#06b6d4'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 9
                                    },
                                    boxWidth: 10
                                }
                            }
                        }
                    }
                });
            } else {
                // Show empty state
                ctx.font = '12px Arial';
                ctx.fillStyle = '#9ca3af';
                ctx.textAlign = 'center';
                ctx.fillText('No cancellation data', ctx.canvas.width / 2, ctx.canvas.height / 2);
            }
        }

        // Update refund trends chart
        function updateRefundTrendsChart(trendsData) {
            const ctx = document.getElementById('refundTrendsChart').getContext('2d');

            // Destroy existing chart if it exists
            if (refundTrendsChart) {
                refundTrendsChart.destroy();
            }

            if (trendsData && trendsData.labels && trendsData.data) {
                refundTrendsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: trendsData.labels,
                        datasets: [{
                            label: 'Refund Amount',
                            data: trendsData.data,
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return '₱' + value.toLocaleString();
                                    },
                                    font: {
                                        size: 9
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 9
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                // Show empty state
                ctx.font = '12px Arial';
                ctx.fillStyle = '#9ca3af';
                ctx.textAlign = 'center';
                ctx.fillText('No refund trend data', ctx.canvas.width / 2, ctx.canvas.height / 2);
            }
        }

        // Update refund details table
        function updateRefundDetailsTable(refunds) {
            const tableBody = $('#refundDetailsTable');
            tableBody.empty();

            if (refunds && refunds.length > 0) {
                refunds.forEach(refund => {
                    const row = $(`
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-3">#${refund.booking_id}</td>
                            <td class="py-2 px-3">${refund.room_name}</td>
                            <td class="py-2 px-3">${refund.refund_date}</td>
                            <td class="py-2 px-3 text-right font-semibold text-red-600">₱${refund.refund_amount.toLocaleString()}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded-full text-xs ${refund.refund_type === 'full'
                                    ? 'bg-blue-100 text-blue-800'
                                    : 'bg-purple-100 text-purple-800'
                                }">
                                    ${refund.refund_type}
                                </span>
                            </td>
                            <td class="py-2 px-3 truncate max-w-xs" title="${refund.refund_reason}">
                                ${refund.refund_reason}
                            </td>
                        </tr>
                    `);
                    tableBody.append(row);
                });
            } else {
                tableBody.append(`
                    <tr>
                        <td colspan="6" class="py-4 px-3 text-center text-gray-500">
                            <i class="fas fa-receipt text-2xl mb-2 text-gray-300 block"></i>
                            No refund records found
                        </td>
                    </tr>
                `);
            }
        }

        // Expand chart for cancellations
        function expandCancellationChart() {
            const modal = $('#chartModal');
            const modalTitle = $('#modalTitle');
            const modalCanvas = document.getElementById('modalChart').getContext('2d');

            modalTitle.text('Cancellation & Refund Analytics');
            modal.removeClass('hidden');

            // Create a comprehensive view of cancellation data
            if (cancellationReasonsChart && refundTrendsChart) {
                // You can implement a more detailed combined chart here
                // For now, let's show the cancellation reasons chart in expanded view
                const data = cancellationReasonsChart.data;
                modalChart = new Chart(modalCanvas, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.datasets[0].data,
                            backgroundColor: data.datasets[0].backgroundColor,
                            borderColor: '#ffffff',
                            borderWidth: 2,
                            hoverOffset: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: {
                                        size: 14
                                    },
                                    padding: 20
                                }
                            },
                            title: {
                                display: true,
                                text: 'Cancellation Reasons Distribution',
                                font: {
                                    size: 16
                                }
                            }
                        }
                    }
                });
            }
        }

        function exportData() {
            const category = $('#category').val();
            const month = $('#month').val();
            const year = $('#year').val();

            // Create a temporary form to submit the export request
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("earnings.export") }}';

            // Add parameters as hidden inputs
            const addInput = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            };

            addInput('category', category);
            addInput('month', month);
            addInput('year', year);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    </script>
@endsection