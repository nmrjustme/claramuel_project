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
			border-top-color: #10b981 !important; /* Green */
		}

		.border-t-expenses {
			border-top-color: #ef4444 !important; /* Red */
		}

		.border-t-profit {
			border-top-color: #3b82f6 !important; /* Blue */
		}

		.border-t-best {
			border-top-color: #8b5cf6 !important; /* Purple */
		}

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

		.breakdown-table th {
			background-color: #f9fafb;
			padding: 0.5rem 0.75rem;
			font-size: 0.75rem;
			font-weight: 600;
			color: #6b7280;
			text-align: left;
		}

		.breakdown-table td {
			padding: 0.5rem 0.75rem;
			border-top: 1px solid #e5e7eb;
			font-size: 0.875rem;
		}

		.breakdown-table tbody tr:hover {
			background-color: #f9fafb;
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
			to { transform: rotate(360deg); }
		}

		/* Ensure table columns have consistent alignment */
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
    font-weight: 600;
    font-size: 0.875rem;
}

.breakdown-table td {
    font-size: 0.875rem;
}

/* Ensure text alignment matches the headers */
.breakdown-table th.text-left,
.breakdown-table td:first-child {
    text-align: left;
}

.breakdown-table th.text-right,
.breakdown-table td:not(:first-child) {
    text-align: right;
}

/* Make the table responsive */
.overflow-x-auto {
    overflow-x: auto;
}
	</style>
@endsection

@section('content')
	<div class="min-h-screen px-6 py-6">
		<!-- Compact Header with Quick Stats -->
		<div class="accounting-card mb-6 border-t-4 border-t-blue-500">
			<div class="accounting-card-header">
				<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
					<div class="flex items-center gap-3">
						<i class="fas fa-chart-pie text-purple-600 text-xl"></i>
						<h1 class="text-blue-600 font-bold text-xl">Accounting Dashboard</h1>
					</div>

					<!-- Quick Stats Row -->
					<div class="flex flex-wrap gap-4 text-sm">
						<div class="flex items-center gap-1">
							<span class="text-gray-600">Revenue:</span>
							<span class="font-bold text-green-600" id="quickTotalRevenue">₱0</span>
						</div>
						<div class="flex items-center gap-1">
							<span class="text-gray-600">Expenses:</span>
							<span class="font-bold text-red-600" id="quickTotalExpenses">₱0</span>
						</div>
						<div class="flex items-center gap-1">
							<span class="text-gray-600">Profit:</span>
							<span class="font-bold text-blue-600" id="quickNetProfit">₱0</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Compact Stats Cards -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
			<!-- Total Revenue Card -->
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
						<div class="text-green-500 text-xs flex items-center gap-1 hidden" id="revenueChange">
							<i class="fas fa-arrow-up text-xs"></i> <span id="revenueChangeValue">0%</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Total Expenses Card -->
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
						<div class="text-red-500 text-xs flex items-center gap-1 hidden" id="expensesChange">
							<i class="fas fa-arrow-up text-xs"></i> <span id="expensesChangeValue">0%</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Net Profit Card -->
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
						<div class="text-blue-500 text-xs flex items-center gap-1 hidden" id="profitChange">
							<i class="fas fa-arrow-up text-xs"></i> <span id="profitChangeValue">0%</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Best Period Card -->
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
						<div class="text-purple-500 text-xs flex items-center gap-1 hidden" id="bestChange">
							<i class="fas fa-arrow-up text-xs"></i> <span id="bestChangeValue">0%</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Compact Filter Bar -->
		<div class="accounting-card mb-6">
			<div class="accounting-card-header">
				<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
					<h2 class="text-gray-900 font-semibold flex items-center gap-2 text-sm">
						<i class="fas fa-filter"></i> Filter Reports
					</h2>

					<div class="flex flex-wrap gap-3 items-center w-full md:w-auto filter-row">
						<!-- Period Select -->
						<div class="flex flex-col filter-controls">
							<label for="periodSelect" class="text-gray-600 font-semibold text-xs mb-1">Period</label>
							<select id="periodSelect" 
								class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-28 focus:outline-none focus:ring-1 focus:ring-blue-500">
								<option value="daily">Daily</option>
								<option value="weekly">Weekly</option>
								<option value="monthly" selected>Monthly</option>
							</select>
						</div>

						<!-- From Date -->
						<div class="flex flex-col filter-controls">
							<label for="fromDate" class="text-gray-600 font-semibold text-xs mb-1">From Date</label>
							<input id="fromDate" type="date" 
								class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-32 focus:outline-none focus:ring-1 focus:ring-blue-500" />
						</div>

						<!-- To Date -->
						<div class="flex flex-col filter-controls">
							<label for="toDate" class="text-gray-600 font-semibold text-xs mb-1">To Date</label>
							<input id="toDate" type="date" 
								class="p-2 border border-gray-300 rounded-md bg-white text-gray-700 text-xs w-32 focus:outline-none focus:ring-1 focus:ring-blue-500" />
						</div>

						<!-- Buttons -->
						<div class="flex flex-wrap gap-2 mt-2 md:mt-0 filter-buttons">
							<button id="applyFilters"
								class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
								<i class="fas fa-filter"></i> Apply
							</button>

							<a href="{{ route('earnings.chart') }}" 
								class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
								<i class="fas fa-chart-bar"></i> Analytics
							</a>

							<a id="exportCsv" href="{{ route('admin.reports.export') }}" 
								class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
								<i class="fas fa-file-csv"></i> Export CSV
							</a>

							<a id="exportPdf" href="{{ route('admin.reports.pdf') }}" 
								class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition-all duration-200">
								<i class="fas fa-file-pdf"></i> Export PDF
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Financial Trend Chart -->
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

		<!-- Financial Breakdown -->
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
	</div>

	<!-- Expanded Chart Modal -->
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
			// Set CSRF token for all AJAX requests
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			// Set default dates (last 30 days)
			const currentDate = new Date();
			const thirtyDaysAgo = new Date();
			thirtyDaysAgo.setDate(currentDate.getDate() - 30);
			
			$('#fromDate').val(formatDate(thirtyDaysAgo));
			$('#toDate').val(formatDate(currentDate));

			// Load initial data
			loadData();

			// Apply filters when button is clicked
			$('#applyFilters').click(function () {
				loadData();
			});

			// Update export links when filters change
			$('#periodSelect, #fromDate, #toDate').change(function() {
				updateExportLinks();
			});

			// Expand chart functionality
			$('.expand-chart-btn').click(function () {
				expandChart('financial');
			});

			// Close modal
			$('#closeModal').click(function () {
				$('#chartModal').addClass('hidden');
				if (modalChart) {
					modalChart.destroy();
					modalChart = null;
				}
			});

			// Enter key support for filters
			$('#fromDate, #toDate').keypress(function(e) {
				if (e.which === 13) {
					loadData();
				}
			});
		});

		function formatDate(date) {
			const year = date.getFullYear();
			const month = String(date.getMonth() + 1).padStart(2, '0');
			const day = String(date.getDate()).padStart(2, '0');
			return `${year}-${month}-${day}`;
		}

		async function loadData() {
			const period = $('#periodSelect').val();
			const from = $('#fromDate').val() || null;
			const to = $('#toDate').val() || null;

			// Show loading state
			$('#applyFilters').addClass('loading').prop('disabled', true);
			$('#applyFilters').html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');
			
			$('.loading-spinner').parent().html('<span class="loading-spinner mr-2"></span> Loading...');

			const params = new URLSearchParams();
			if (period) params.set('period', period);
			if (from) params.set('from', from);
			if (to) params.set('to', to);

			try {
				const response = await fetch("{{ route('admin.api.monthly-income') }}?" + params.toString());
				
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`);
				}
				
				const data = await response.json();

				// Update cards with proper formatting
				$('#cardTotalRevenue').text('₱' + formatNumber(data.totalIncome || 0));
				$('#cardTotalExpenses').text('₱' + formatNumber(data.totalExpense || 0));
				$('#cardNetProfit').text('₱' + formatNumber(data.netTotal || 0));
				$('#cardBestLabel').text(data.best?.label || 'N/A');

				// Update quick stats
				$('#quickTotalRevenue').text('₱' + formatNumber(data.totalIncome || 0));
				$('#quickTotalExpenses').text('₱' + formatNumber(data.totalExpense || 0));
				$('#quickNetProfit').text('₱' + formatNumber(data.netTotal || 0));

				// Update period labels
				updatePeriodLabels(period, from, to);

				// Update breakdown table
				updateBreakdownTable(data.combined || []);
				
				// Update chart
				updateChart(data.chartData);
				
				// Store current data for exports
				currentChartData = data;
				
			} catch (error) {
				console.error('Error loading accounting data:', error);
				showError('Error loading accounting data: ' + error.message);
				
				// Reset cards to error state
				$('#cardTotalRevenue').text('Error');
				$('#cardTotalExpenses').text('Error');
				$('#cardNetProfit').text('Error');
				$('#cardBestLabel').text('Error');
			} finally {
				// Remove loading state
				$('#applyFilters').removeClass('loading').prop('disabled', false);
				$('#applyFilters').html('<i class="fas fa-filter mr-1"></i> Apply');
			}
		}

		function formatNumber(num) {
			return Number(num).toLocaleString('en-US', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});
		}

		function updatePeriodLabels(period, from, to) {
			let periodText = '';
			
			if (period === 'daily') {
				periodText = from === to ? `Date: ${from}` : `${from} to ${to}`;
			} else if (period === 'weekly') {
				periodText = from && to ? `Week: ${from} to ${to}` : 'This Week';
			} else {
				if (from && to) {
					const fromDate = new Date(from);
					const toDate = new Date(to);
					const monthNames = ["January", "February", "March", "April", "May", "June",
						"July", "August", "September", "October", "November", "December"
					];
					
					if (fromDate.getMonth() === toDate.getMonth() && fromDate.getFullYear() === toDate.getFullYear()) {
						periodText = `${monthNames[fromDate.getMonth()]} ${fromDate.getFullYear()}`;
					} else {
						periodText = `${from} to ${to}`;
					}
				} else {
					const currentDate = new Date();
					const monthNames = ["January", "February", "March", "April", "May", "June",
						"July", "August", "September", "October", "November", "December"
					];
					periodText = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
				}
			}
			
			$('#revenuePeriod').text(periodText);
			$('#expensesPeriod').text(periodText);
			$('#profitPeriod').text(periodText);
			$('#bestPeriod').text(periodText);
		}

		function updateBreakdownTable(combinedData) {
    const tbody = $('#breakdownBody');
    tbody.empty();
    
    if (combinedData && combinedData.length > 0) {
        combinedData.forEach(row => {
            const netClass = row.net >= 0 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
            const income = (row.room || 0) + (row.daytour || 0) + (row.event || 0); // Still calculate with event
            
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

        // Add total row
        const totals = combinedData.reduce((acc, row) => {
            acc.room += row.room || 0;
            acc.daytour += row.daytour || 0;
            acc.event += row.event || 0; // Still track event internally
            acc.expense += row.expense || 0;
            acc.income += (row.room || 0) + (row.daytour || 0) + (row.event || 0); // Include event in income calculation
            acc.net += row.net || 0;
            return acc;
        }, { room: 0, daytour: 0, event: 0, expense: 0, income: 0, net: 0 });

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
                <td colspan="6" class="p-6 text-center text-gray-500 text-sm"> <!-- Now 6 columns -->
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-inbox text-2xl text-gray-300"></i>
                        <div>No data available for the selected period</div>
                        <div class="text-xs text-gray-400">Try adjusting your filters</div>
                    </div>
                </td>
            </tr>
        `);
    }
}

		function updateChart(chartData) {
			const ctx = document.getElementById('accountingChart').getContext('2d');

			// Destroy existing chart if it exists
			if (accountingChart) {
				accountingChart.destroy();
			}

			if (!chartData || !chartData.labels || !chartData.datasets) {
				// Create empty chart with message
				accountingChart = new Chart(ctx, {
					type: 'line',
					data: {
						labels: [],
						datasets: []
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { display: false },
							title: {
								display: true,
								text: 'No data available',
								font: { size: 16 }
							}
						}
					}
				});
				return;
			}

			// Define consistent colors
			const colors = {
				room: { border: '#3b82f6', background: 'rgba(59, 130, 246, 0.1)' },
				daytour: { border: '#f59e0b', background: 'rgba(245, 158, 11, 0.1)' },
				expenses: { border: '#ef4444', background: 'rgba(239, 68, 68, 0.1)' }
			};

			// Map datasets to consistent colors
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

			// Create new chart
			accountingChart = new Chart(ctx, {
				type: 'line',
				data: {
					labels: chartData.labels,
					datasets: mappedDatasets
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					interaction: {
						mode: 'index',
						intersect: false
					},
					plugins: {
						legend: {
							position: 'top',
							labels: {
								font: { size: 11 },
								padding: 15,
								usePointStyle: true
							}
						},
						tooltip: {
							backgroundColor: 'rgba(0, 0, 0, 0.8)',
							padding: 12,
							cornerRadius: 6,
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
							grid: {
								color: 'rgba(0, 0, 0, 0.05)'
							},
							ticks: {
								callback: function (value) {
									return '₱' + value.toLocaleString();
								},
								font: { size: 10 }
							}
						},
						x: {
							grid: { display: false },
							ticks: {
								font: { size: 10 },
								maxRotation: 45
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

		function expandChart(chartType) {
			const modal = $('#chartModal');
			const modalTitle = $('#modalTitle');
			const modalCanvas = document.getElementById('modalChart');

			if (!accountingChart) {
				showError('No chart data available to expand');
				return;
			}

			// Set modal title
			modalTitle.text('Financial Trends - Expanded View');

			// Show modal
			modal.removeClass('hidden');

			// Destroy existing modal chart
			if (modalChart) {
				modalChart.destroy();
			}

			// Clone the accounting chart with larger size
			const ctx = modalCanvas.getContext('2d');
			modalChart = new Chart(ctx, {
				...accountingChart.config,
				options: {
					...accountingChart.config.options,
					maintainAspectRatio: false,
					plugins: {
						...accountingChart.config.options.plugins,
						legend: {
							position: 'top',
							labels: {
								font: { size: 14 },
								padding: 20,
								usePointStyle: true
							}
						}
					},
					scales: {
						...accountingChart.config.options.scales,
						x: {
							...accountingChart.config.options.scales.x,
							ticks: {
								...accountingChart.config.options.scales.x.ticks,
								font: { size: 12 }
							}
						},
						y: {
							...accountingChart.config.options.scales.y,
							ticks: {
								...accountingChart.config.options.scales.y.ticks,
								font: { size: 12 }
							}
						}
					}
				}
			});
		}

		function updateExportLinks() {
			const period = $('#periodSelect').val();
			const from = $('#fromDate').val() || null;
			const to = $('#toDate').val() || null;

			const params = new URLSearchParams();
			if (period) params.set('period', period);
			if (from) params.set('from', from);
			if (to) params.set('to', to);

			const queryString = params.toString();
			$('#exportCsv').attr('href', "{{ route('admin.reports.export') }}" + (queryString ? '?' + queryString : ''));
			$('#exportPdf').attr('href', "{{ route('admin.reports.pdf') }}" + (queryString ? '?' + queryString : ''));
		}

		function showError(message) {
			// You could replace this with a toast notification
			console.error('Error:', message);
			alert('Error: ' + message);
		}
	</script>
@endsection