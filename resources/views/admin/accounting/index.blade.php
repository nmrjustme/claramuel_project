@extends('layouts.admin')
@section('title', 'Accounting Dashboard')

@php
    $active = 'accounting';
@endphp

@section('content_css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Accounting-specific styles with consistent theme */
        .accounting-card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.75rem;
            background-color: white;
        }

        .accounting-card-header {
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
        }

        .accounting-card-body {
            padding: 1rem;
        }

        /* Consistent border colors for stats cards */
        .border-t-revenue {
            border-top-color: #10b981 !important;
        }

        /* Green */
        .border-t-expenses {
            border-top-color: #ef4444 !important;
        }

        /* Red */
        .border-t-profit {
            border-top-color: #3b82f6 !important;
        }

        /* Blue */
        .border-t-best {
            border-top-color: #8b5cf6 !important;
        }

        /* Purple */

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
                gap: 0.75rem;
            }

            .filter-controls {
                width: 100%;
            }

            .filter-buttons {
                width: 100%;
                justify-content: flex-end;
            }
        }

        /* Table styling consistency */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }

        .breakdown-table th,
        .breakdown-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .breakdown-table th {
            background-color: #f9fafb;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-align: left;
        }

        .breakdown-table td {
            font-size: 0.875rem;
        }

        .breakdown-table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Alignment classes */
        .breakdown-table th.text-left,
        .breakdown-table td:first-child {
            text-align: left;
        }

        .breakdown-table th.text-right,
        .breakdown-table td:not(:first-child) {
            text-align: right;
        }

        /* Chart container */
        .chart-container {
            position: relative;
            height: 20rem;
        }

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #f3f4f6;
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .overflow-x-auto {
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')

    <div class="accounting-card mb-6 border-t-4 border-t-blue-500">
        <div class="accounting-card-header">

            <div class="flex items-center gap-3">
                <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                <h1 class="text-blue-600 font-bold text-xl">Accounting Dashboard</h1>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="accounting-card border-t-4 border-t-revenue">
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-dollar-sign text-xs"></i> Total Revenue
                        </div>
                        <div class="text-xl font-bold my-1 text-green-600" id="cardTotalRevenue">
                            <span class="loading-spinner mr-2"></span> Loading...
                        </div>
                        <div class="text-gray-500 text-xs" id="revenuePeriod">This Month</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accounting-card border-t-4 border-t-expenses">
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-money-bill-wave text-xs"></i> Total Expenses
                        </div>
                        <div class="text-xl font-bold my-1 text-red-600" id="cardTotalExpenses">
                            <span class="loading-spinner mr-2"></span> Loading...
                        </div>
                        <div class="text-gray-500 text-xs" id="expensesPeriod">This Month</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accounting-card border-t-4 border-t-profit">
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-chart-line text-xs"></i> Net Profit
                        </div>
                        <div class="text-xl font-bold my-1 text-blue-600" id="cardNetProfit">
                            <span class="loading-spinner mr-2"></span> Loading...
                        </div>
                        <div class="text-gray-500 text-xs" id="profitPeriod">This Month</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accounting-card border-t-4 border-t-best">
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-gray-600 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-trophy text-xs"></i> Best Period
                        </div>
                        <div class="text-xl font-bold my-1 text-purple-600" id="cardBestLabel">
                            <span class="loading-spinner mr-2"></span> Loading...
                        </div>
                        <div class="text-gray-500 text-xs" id="bestPeriod">Highest Revenue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="accounting-card mb-6">
        <div class="accounting-card-header">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                    <i class="fas fa-filter"></i> Filter Reports
                </h2>

                <div class="flex flex-wrap gap-3 items-center w-full md:w-auto filter-row">

                    <div class="flex flex-col filter-controls">
                        <label for="periodSelect" class="text-gray-600 font-semibold text-xs mb-1">Filter Type</label>
                        <select id="periodSelect"
                            class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-28 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="daily">Daily</option>
                            <option value="monthly" selected>Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>

                    <div class="flex flex-col filter-controls filter-input-group hidden" id="dailyInputGroup">
                        <label for="dateFilter" class="text-gray-600 font-semibold text-xs mb-1">Select Date</label>
                        <input id="dateFilter" type="date"
                            class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-36 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div class="flex flex-col filter-controls filter-input-group" id="monthlyInputGroup">
                        <label for="monthFilter" class="text-gray-600 font-semibold text-xs mb-1">Select Month</label>
                        <input id="monthFilter" type="month"
                            class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-36 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div class="flex flex-col filter-controls filter-input-group hidden" id="yearlyInputGroup">
                        <label for="yearFilter" class="text-gray-600 font-semibold text-xs mb-1">Select Year</label>
                        <select id="yearFilter"
                            class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-36 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-2 md:mt-0 filter-buttons">
                        <button id="applyFilters"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                            <i class="fas fa-filter"></i> Apply
                        </button>

                        <a href="{{ route('admin.earnings.chart') }}"
                            class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                            <i class="fas fa-chart-bar"></i> Analytics
                        </a>

                        <!-- <a id="exportCsv" href="#" 
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                                        <i class="fas fa-file-csv"></i> Export CSV
                                    </a> -->

                        <a id="exportPdf" href="#"
                            class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="accounting-card mb-6 overflow-hidden">
        <div class="accounting-card-header flex justify-between items-center">
            <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                <i class="fas fa-chart-line"></i> Financial Trends
            </h2>
            <button class="text-xs text-blue-500 font-medium expand-chart-btn" data-chart="financial">
                <i class="fas fa-expand-alt mr-1"></i> Expand
            </button>
        </div>
        <div class="accounting-card-body">
            <div class="chart-container">
                <canvas id="accountingChart"></canvas>
            </div>
        </div>
    </div>

    <div class="accounting-card overflow-hidden">
        <div class="accounting-card-header">
            <h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
                <i class="fas fa-table"></i> Financial Breakdown
            </h2>
        </div>
        <div class="accounting-card-body">
            <div class="overflow-x-auto">
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th class="text-left">Period</th>
                            <th class="text-right">Room</th>
                            <th class="text-right">Day Tour</th>
                            <th class="text-right">Expenses</th>
                            <th class="text-right">Income</th>
                            <th class="text-right">Net</th>
                        </tr>
                    </thead>
                    <tbody id="breakdownBody">
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500 text-sm">
                                <i class="fas fa-inbox mr-2"></i> Select filters and click Apply to load data
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden" id="chartModal">
        <div class="bg-white rounded-xl shadow-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-auto">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold" id="modalTitle">Financial Trends</h2>
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
        let accountingChart = null;
        let modalChart = null;
        let currentChartData = null;

        $(document).ready(function () {
            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // Initialize default values (Populate Year, set default Month/Date)
            initializeFilters();

            // Load initial data
            loadData();

            // Event Listeners
            $('#periodSelect').change(handlePeriodChange);
            $('#applyFilters').click(loadData);

            // Trigger export link updates when filters change
            $('#periodSelect, #dateFilter, #monthFilter, #yearFilter').change(updateExportLinks);
            $('#dateFilter, #monthFilter, #yearFilter').change(function () {
                loadData();
            });
            // Enter key support
            $('.filter-input-group input, .filter-input-group select').keypress(function (e) {
                if (e.which === 13) loadData();
            });

            // Expand chart
            $('.expand-chart-btn').click(function () { expandChart('financial'); });

            // Close modal
            $('#closeModal').click(function () {
                $('#chartModal').addClass('hidden');
                if (modalChart) { modalChart.destroy(); modalChart = null; }
            });
        });

        function initializeFilters() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');

            // 1. Populate Year Dropdown (Current year +/- 5 years)
            const yearSelect = $('#yearFilter');
            yearSelect.empty();
            for (let y = year + 2; y >= year - 5; y--) {
                yearSelect.append(new Option(y, y));
            }
            yearSelect.val(year);

            // 2. Set Default Values
            $('#dateFilter').val(`${year}-${month}-${day}`);
            $('#monthFilter').val(`${year}-${month}`);

            // 3. Ensure correct input is visible initially
            handlePeriodChange();
        }

        function handlePeriodChange() {
            const period = $('#periodSelect').val();

            // Hide all filter inputs first
            $('#dailyInputGroup, #monthlyInputGroup, #yearlyInputGroup').addClass('hidden');

            // Show the relevant one
            if (period === 'daily') {
                $('#dailyInputGroup').removeClass('hidden');
            } else if (period === 'monthly') {
                $('#monthlyInputGroup').removeClass('hidden');
            } else if (period === 'yearly') {
                $('#yearlyInputGroup').removeClass('hidden');
            }

            updateExportLinks();
        }

        function getSelectedFilterData() {
            const period = $('#periodSelect').val();
            let value = '';

            if (period === 'daily') {
                value = $('#dateFilter').val();
            } else if (period === 'monthly') {
                value = $('#monthFilter').val();
            } else if (period === 'yearly') {
                value = $('#yearFilter').val();
            }

            return { period, value };
        }

        async function loadData() {
            const { period, value } = getSelectedFilterData();

            if (!value) {
                alert("Please select a valid date/month/year");
                return;
            }

            // Show loading state
            $('#applyFilters').addClass('loading').prop('disabled', true);
            $('#applyFilters').html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');
            $('.loading-spinner').parent().html('<span class="loading-spinner mr-2"></span> Loading...');

            // Build Query Params
            const params = new URLSearchParams({
                period: period,
                filter_value: value
            });

            try {
                const response = await fetch("{{ route('admin.api.monthly-incomes') }}?" + params.toString());

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                currentChartData = data;

                // Update Stats Cards
                $('#cardTotalRevenue').text('₱' + formatNumber(data.totalIncome || 0));
                $('#cardTotalExpenses').text('₱' + formatNumber(data.totalExpense || 0));
                $('#cardNetProfit').text('₱' + formatNumber(data.netTotal || 0));
                $('#cardBestLabel').text(data.best?.label || 'N/A');

                // Update Quick Stats
                $('#quickTotalRevenue').text('₱' + formatNumber(data.totalIncome || 0));
                $('#quickTotalExpenses').text('₱' + formatNumber(data.totalExpense || 0));
                $('#quickNetProfit').text('₱' + formatNumber(data.netTotal || 0));

                // Update Period Labels on cards
                const labelText = getLabelText(period, value);
                $('#revenuePeriod, #expensesPeriod, #profitPeriod, #bestPeriod').text(labelText);

                // Update Breakdown Table
                updateBreakdownTable(data.combined || []);

                // Update Chart
                updateChart(data.chartData);

            } catch (error) {
                console.error('Error loading accounting data:', error);
                alert('Error loading accounting data: ' + error.message);

                // Reset to error state
                $('#cardTotalRevenue').text('Error');
            } finally {
                // Remove loading state
                $('#applyFilters').removeClass('loading').prop('disabled', false);
                $('#applyFilters').html('<i class="fas fa-filter mr-1"></i> Apply');
            }
        }

        function getLabelText(period, value) {
            if (!value) return '-';

            if (period === 'daily') {
                return `Date: ${value}`;
            }
            else if (period === 'monthly') {
                const [y, m] = value.split('-');
                const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                return `${months[parseInt(m) - 1]} ${y}`;
            }
            else if (period === 'yearly') {
                return `Year: ${value}`;
            }
            return '';
        }

        function formatNumber(num) {
            return Number(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function updateExportLinks() {
            const { period, value } = getSelectedFilterData();
            const params = new URLSearchParams({
                period: period,
                filter_value: value
            }).toString();

            // $('#exportCsv').attr('href', "{{ route('admin.reports.exports') }}?" + params);
            $('#exportPdf').attr('href', "{{ route('admin.reports.pdf') }}?" + params);
        }

        function updateBreakdownTable(combinedData) {
            const tbody = $('#breakdownBody');
            tbody.empty();

            if (combinedData && combinedData.length > 0) {
                combinedData.forEach(row => {
                    const netClass = row.net >= 0 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';

                    // We display income as (Room + Daytour)
                    // You mentioned "Income" column should contain room + daytour + event
                    const income = (row.income || 0);

                    tbody.append(`
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-3 text-sm font-medium text-gray-900">${row.label}</td>
                                    <td class="p-3 text-right text-sm text-blue-600">₱${formatNumber(row.room || 0)}</td>
                                    <td class="p-3 text-right text-sm text-amber-600">₱${formatNumber(row.daytour || 0)}</td>
                                    <td class="p-3 text-right text-sm text-red-600">₱${formatNumber(row.expense || 0)}</td>
                                    <td class="p-3 text-right text-sm text-green-600 font-medium">₱${formatNumber(income)}</td>
                                    <td class="p-3 text-right text-sm ${netClass}">₱${formatNumber(row.net || 0)}</td>
                                </tr>
                            `);
                });

                // Calculate Totals
                const totals = combinedData.reduce((acc, row) => {
                    acc.room += row.room || 0;
                    acc.daytour += row.daytour || 0;
                    acc.expense += row.expense || 0;
                    acc.income += row.income || 0;
                    acc.net += row.net || 0;
                    return acc;
                }, { room: 0, daytour: 0, expense: 0, income: 0, net: 0 });

                const totalNetClass = totals.net >= 0 ? 'text-green-600 font-bold' : 'text-red-600 font-bold';

                tbody.append(`
                            <tr class="bg-gray-50 border-t-2 border-gray-200">
                                <td class="p-3 text-sm font-bold text-gray-900">TOTAL</td>
                                <td class="p-3 text-right text-sm font-bold text-blue-700">₱${formatNumber(totals.room)}</td>
                                <td class="p-3 text-right text-sm font-bold text-amber-700">₱${formatNumber(totals.daytour)}</td>
                                <td class="p-3 text-right text-sm font-bold text-red-700">₱${formatNumber(totals.expense)}</td>
                                <td class="p-3 text-right text-sm font-bold text-green-700">₱${formatNumber(totals.income)}</td>
                                <td class="p-3 text-right text-sm font-bold ${totalNetClass}">₱${formatNumber(totals.net)}</td>
                            </tr>
                        `);
            } else {
                tbody.append(`
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-inbox text-2xl text-gray-300"></i>
                                        <div>No data available for the selected period</div>
                                    </div>
                                </td>
                            </tr>
                        `);
            }
        }

        function updateChart(chartData) {
            const ctx = document.getElementById('accountingChart').getContext('2d');

            if (accountingChart) {
                accountingChart.destroy();
            }

            if (!chartData || !chartData.labels || !chartData.datasets) {
                // Handle empty chart case if necessary
                return;
            }

            // Define consistent colors
            const colors = {
                room: { border: '#3b82f6', background: 'rgba(59, 130, 246, 0.1)' },
                daytour: { border: '#f59e0b', background: 'rgba(245, 158, 11, 0.1)' },
                expenses: { border: '#ef4444', background: 'rgba(239, 68, 68, 0.1)' }
            };

            const mappedDatasets = chartData.datasets.map(dataset => {
                const label = dataset.label.toLowerCase();
                let colorConfig = colors.room; // default

                if (label.includes('room')) colorConfig = colors.room;
                else if (label.includes('day tour')) colorConfig = colors.daytour;
                else if (label.includes('expense')) colorConfig = colors.expenses;

                return {
                    ...dataset,
                    borderColor: colorConfig.border,
                    backgroundColor: colorConfig.background,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointBackgroundColor: colorConfig.border,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                };
            });

            accountingChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: mappedDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 6 } },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function (context) {
                                    return `${context.dataset.label}: ₱${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: {
                                callback: function (value) { return '₱' + value.toLocaleString(); },
                                font: { size: 10 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, maxRotation: 45 }
                        }
                    }
                }
            });
        }

        function expandChart(chartType) {
            const modal = $('#chartModal');
            const modalTitle = $('#modalTitle');
            const modalCanvas = document.getElementById('modalChart');

            if (!accountingChart) {
                alert('No chart data available to expand');
                return;
            }

            modalTitle.text('Financial Trends - Expanded View');
            modal.removeClass('hidden');

            if (modalChart) modalChart.destroy();

            const ctx = modalCanvas.getContext('2d');
            modalChart = new Chart(ctx, {
                ...accountingChart.config,
                options: {
                    ...accountingChart.config.options,
                    maintainAspectRatio: false,
                    plugins: {
                        ...accountingChart.config.options.plugins,
                        legend: { position: 'top', labels: { font: { size: 14 }, usePointStyle: true } }
                    }
                }
            });
        }
    </script>
@endsection