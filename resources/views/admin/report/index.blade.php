@extends('layouts.admin')

@section('title', 'Financial Report | Mt. Claramuel Resort')
@php
	$active = 'reports';
@endphp

@section('content_css')
	<style>
		/* PRINT STYLES - ALL COVERED FIX */
		@media print {
			@page {
				size: auto;
				margin: 5mm;
				/* Small margins to fit more content */
			}

			/* Hide UI Elements */
			.no-print,
			nav,
			aside,
			.sidebar,
			header,
			.filter-section,
			button,
			#loadingOverlay {
				display: none !important;
			}

			/* Reset Body */
			body {
				background-color: white !important;
				font-size: 10pt;
				/* Slightly smaller for better fit */
				color: black;
				margin: 0;
				padding: 0;
				width: 100%;
				-webkit-print-color-adjust: exact !important;
				print-color-adjust: exact !important;
			}

			/* Main Container Expansion */
			.report-container {
				box-shadow: none !important;
				border: none !important;
				margin: 0 !important;
				padding: 0 !important;
				width: 100% !important;
				max-width: 100% !important;
				min-height: auto !important;
				/* Allow height to collapse/expand */
			}

			/* CRITICAL: Override Scrollbars/Overflow for Printing */
			/* This ensures the full table prints, not just the scrollable area */
			.overflow-x-auto,
			.overflow-hidden {
				overflow: visible !important;
				height: auto !important;
				display: block !important;
			}

			/* Table Management */
			table {
				width: 100% !important;
				table-layout: auto;
				border-collapse: collapse;
				page-break-inside: auto;
			}

			thead {
				display: table-header-group;
				/* Repeats header on new page */
			}

			tr {
				page-break-inside: avoid;
				page-break-after: auto;
			}

			/* Reveal Truncated Text on Print */
			.truncate {
				white-space: normal !important;
				overflow: visible !important;
				text-overflow: clip !important;
				max-width: none !important;
			}

			/* Specific Adjustments for Layout */
			.grid {
				display: flex !important;
				/* Grid often behaves weirdly in print, flex is safer */
				flex-direction: row;
				flex-wrap: wrap;
			}

			.md\:grid-cols-4>div {
				width: 25%;
				border: 1px solid #eee;
			}

			/* Remove shadows/borders that look bad on paper */
			.shadow-lg,
			.shadow-sm {
				box-shadow: none !important;
			}
		}
	</style>
@endsection

@section('content')

	{{-- CONTROLS BAR (Hidden when printing) --}}
	<div class="filter-section mb-6 flex flex-col md:flex-row justify-end items-center no-print">
		<div
			class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-lg shadow-sm border border-gray-200 w-full md:w-auto">

			{{-- 1. Filter Type Selector --}}
			<div class="flex flex-col">
				<label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Period Type</label>
				<select id="periodType"
					class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 min-w-[120px]">
					<option value="daily">Daily</option>
					<option value="monthly" selected>Monthly</option>
					<option value="yearly">Yearly</option>
				</select>
			</div>

			{{-- 2. Dynamic Inputs --}}
			<div class="flex flex-col">
				<label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Select Date</label>
				<input type="date" id="dailyInput"
					class="filter-input hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 w-40">
				<input type="month" id="monthlyInput"
					class="filter-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 w-40">
				<select id="yearlyInput"
					class="filter-input hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 w-40"></select>
			</div>

			{{-- Divider --}}
			<div class="border-l border-gray-300 h-8 mx-2"></div>

			{{-- Buttons --}}
			<div class="flex items-end gap-2 h-full pb-0.5">
				<button onclick="fetchFinancialData()"
					class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors shadow-sm">
					<i class="fas fa-filter mr-1"></i> Apply
				</button>
				<button onclick="window.print()"
					class="text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 font-medium rounded-lg text-sm px-4 py-2.5 transition-colors">
					<i class="fas fa-print mr-1"></i> Print
				</button>
				<button onclick="exportReport('pdf')"
					class="text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm px-4 py-2.5 transition-colors shadow-sm">
					<i class="fas fa-file-pdf mr-1"></i> PDF
				</button>
			</div>
		</div>
	</div>

	{{-- REPORT "PAPER" CONTAINER --}}
	<div
		class="report-container bg-white shadow-lg rounded-none md:rounded-lg border border-gray-200 min-h-[800px] relative">

		{{-- Loading Overlay --}}
		<div id="loadingOverlay"
			class="absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-sm"
			style="display: none;">
			<div class="text-center">
				<div
					class="inline-block animate-spin w-10 h-10 border-4 border-gray-300 border-t-red-600 rounded-full mb-2">
				</div>
				<p class="text-sm text-gray-500 font-medium">Generating Financial Data...</p>
			</div>
		</div>

		<div class="p-8 md:p-10 font-sans text-gray-800">

			{{-- HEADER SECTION --}}
			<div class="border-b-2 border-blue-600 pb-4 mb-6 flex justify-between items-end">
				<div>
					<h1 class="text-3xl font-extrabold text-blue-800 tracking-tight">FINANCIAL REPORT</h1>
					<p class="text-xs text-gray-500 mt-1 uppercase tracking-wide">
						{{ config('app.name') }}
					</p>
				</div>
				<div class="text-right">
					<p class="text-xs text-gray-500">Generated: <span
							id="generatedDate">{{ now()->format('M d, Y h:i A') }}</span></p>
					<p class="text-sm font-bold text-gray-800 mt-1">
						Period: <span id="activeFilterLabel" class="text-blue-600">Loading...</span>
					</p>
				</div>
			</div>

			{{-- SUMMARY CARDS --}}
			<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-8">
				<div class="grid grid-cols-1 md:grid-cols-4 gap-6 divide-y md:divide-y-0 md:divide-x divide-gray-300">
					{{-- Total Revenue --}}
					<div class="text-center px-2">
						<div class="text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Total Revenue
						</div>
						<div class="text-2xl font-bold text-emerald-600" id="displayTotalIncome">₱0.00</div>
						<div class="text-[10px] text-gray-400 mt-1">Gross Income</div>
					</div>
					{{-- Breakdown --}}
					<div class="px-2 pt-4 md:pt-0">
						<div class="text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-2 text-center">
							Revenue Sources</div>
						<div class="flex flex-col gap-1">
							<div class="text-xs flex justify-between px-2">
								<span class="text-gray-500">Day Tours:</span>
								<span class="font-bold text-blue-700" id="displayDayTour">₱0.00</span>
							</div>
							<div class="text-xs flex justify-between px-2">
								<span class="text-gray-500">Rooms:</span>
								<span class="font-bold text-emerald-700" id="displayRoom">₱0.00</span>
							</div>
						</div>
					</div>
					{{-- Expenses --}}
					<div class="text-center px-2 pt-4 md:pt-0">
						<div class="text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Total Expenses
						</div>
						<div class="text-2xl font-bold text-red-600" id="displayTotalExpense">₱0.00</div>
						<div class="text-[10px] text-gray-400 mt-1">Operational Costs</div>
					</div>
					{{-- Net Profit --}}
					<div class="text-center px-2 pt-4 md:pt-0">
						<div class="text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Net Profit</div>
						<div class="text-2xl font-bold text-gray-800" id="displayNetProfit">₱0.00</div>
						<div class="text-[10px] text-gray-400 mt-1">Income - Expenses</div>
					</div>
				</div>
			</div>

			{{-- SECTION 1: PERIOD BREAKDOWN --}}
			<div class="mb-8 break-inside-avoid">
				<div class="flex items-center mb-3">
					<div class="w-1 h-4 bg-blue-600 mr-2"></div>
					<h3 class="text-xs font-bold text-gray-700 uppercase">Summary Breakdown</h3>
				</div>

				<div class="overflow-hidden border border-gray-200 rounded-sm">
					<table class="w-full text-xs text-left">
						<thead class="bg-blue-600 text-white uppercase font-semibold">
							<tr>
								<th class="px-4 py-2 w-1/5">Period</th>
								<th class="px-4 py-2 text-right">Room Rev</th>
								<th class="px-4 py-2 text-right">Day Tour Rev</th>
								<th class="px-4 py-2 text-right">Expenses</th>
								<th class="px-4 py-2 text-right">Total Rev</th>
								<th class="px-4 py-2 text-right">Net Profit</th>
							</tr>
						</thead>
						<tbody id="breakdownTableBody" class="divide-y divide-gray-200">
							{{-- JS Populated --}}
						</tbody>
						<tfoot class="bg-gray-100 font-bold border-t-2 border-gray-400">
							<tr>
								<td class="px-4 py-2">GRAND TOTAL</td>
								<td class="px-4 py-2 text-right text-emerald-700" id="footerRoom">₱0.00</td>
								<td class="px-4 py-2 text-right text-blue-700" id="footerDayTour">₱0.00</td>
								<td class="px-4 py-2 text-right text-red-600" id="footerExpense">₱0.00</td>
								<td class="px-4 py-2 text-right text-gray-800" id="footerTotal">₱0.00</td>
								<td class="px-4 py-2 text-right" id="footerNet">₱0.00</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

			{{-- SECTION 2: DETAILED LOGS --}}
			<div class="mb-4">
				<div class="flex items-center mb-3">
					<div class="w-1 h-4 bg-blue-600 mr-2"></div>
					<h3 class="text-xs font-bold text-gray-700 uppercase">Detailed Transaction Log</h3>
				</div>

				{{-- Note: 'overflow-x-auto' causes print cutoffs. The @media print override fixes this. --}}
				<div class="overflow-x-auto border border-gray-200 rounded-sm">
					<table class="w-full text-xs text-left">
						<thead class="bg-gray-100 text-gray-600 uppercase font-semibold border-b border-gray-200">
							<tr>
								<th class="px-3 py-2">Date</th>
								<th class="px-3 py-2">Type</th>
								<th class="px-3 py-2">Ref #</th>
								<th class="px-3 py-2">Customer</th>
								<th class="px-3 py-2">Description</th>
								<th class="px-3 py-2">Method</th>
								<th class="px-3 py-2 text-right">Amount</th>
							</tr>
						</thead>
						<tbody id="transactionsTableBody" class="divide-y divide-gray-200">
							<tr>
								<td colspan="7" class="px-4 py-6 text-center text-gray-400 italic">
									Loading transactions...
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			{{-- Footer Metadata --}}
			<div class="mt-8 pt-4 border-t border-gray-100 text-center text-[10px] text-gray-400">
				System Generated Report | {{ config('app.name') }} | {{ now()->year }}
			</div>
		</div>
	</div>
@endsection

@section('content_js')
	<script>
		const moneyFormat = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 2 });

		document.addEventListener('DOMContentLoaded', function () {
			initializeFilters();
			fetchFinancialData();
		});

		function initializeFilters() {
			const today = new Date();
			const year = today.getFullYear();
			const month = String(today.getMonth() + 1).padStart(2, '0');
			const day = String(today.getDate()).padStart(2, '0');

			const yearSelect = document.getElementById('yearlyInput');
			yearSelect.innerHTML = '';
			for (let y = year + 2; y >= year - 5; y--) {
				const option = document.createElement('option');
				option.value = y;
				option.text = y;
				yearSelect.appendChild(option);
			}
			yearSelect.value = year;

			document.getElementById('dailyInput').value = `${year}-${month}-${day}`;
			document.getElementById('monthlyInput').value = `${year}-${month}`;
			document.getElementById('periodType').addEventListener('change', handlePeriodChange);
			handlePeriodChange();
		}

		function handlePeriodChange() {
			const period = document.getElementById('periodType').value;
			document.getElementById('dailyInput').classList.add('hidden');
			document.getElementById('monthlyInput').classList.add('hidden');
			document.getElementById('yearlyInput').classList.add('hidden');

			if (period === 'daily') document.getElementById('dailyInput').classList.remove('hidden');
			else if (period === 'monthly') document.getElementById('monthlyInput').classList.remove('hidden');
			else if (period === 'yearly') document.getElementById('yearlyInput').classList.remove('hidden');
		}

		function getFilterValue() {
			const period = document.getElementById('periodType').value;
			if (period === 'daily') return document.getElementById('dailyInput').value;
			if (period === 'monthly') return document.getElementById('monthlyInput').value;
			if (period === 'yearly') return document.getElementById('yearlyInput').value;
			return '';
		}

		function getFormattedDateLabel(period, value) {
			if (!value) return '-';
			if (period === 'daily') return new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
			if (period === 'monthly') {
				const [y, m] = value.split('-');
				const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
				return `${months[parseInt(m) - 1]} ${y}`;
			}
			if (period === 'yearly') return `Year ${value}`;
		}

		function fetchFinancialData() {
			const period = document.getElementById('periodType').value;
			const filterValue = getFilterValue();
			const loader = document.getElementById('loadingOverlay');

			if (!filterValue) { alert('Please select a valid date/month/year'); return; }

			document.getElementById('activeFilterLabel').innerText = getFormattedDateLabel(period, filterValue);
			document.getElementById('generatedDate').innerText = new Date().toLocaleString();
			loader.style.display = 'flex';

			const url = `{{ route('admin.report.api') }}?period=${period}&filter_value=${filterValue}`;

			fetch(url)
				.then(response => response.json())
				.then(data => {
					updateSummaryCards(data);
					updateBreakdownTable(data.combined);
					updateTransactionsTable(data.transactions);
				})
				.catch(error => console.error('Error:', error))
				.finally(() => loader.style.display = 'none');
		}

		function updateSummaryCards(data) {
			const format = (val) => moneyFormat.format(val || 0);

			document.getElementById('displayTotalIncome').innerText = format(data.totalIncome);
			document.getElementById('displayTotalExpense').innerText = format(data.totalExpense);

			const elNet = document.getElementById('displayNetProfit');
			elNet.innerText = format(data.netTotal);
			elNet.className = `text-2xl font-bold ${data.netTotal >= 0 ? 'text-emerald-600' : 'text-red-600'}`;

			let roomTotal = 0, dayTourTotal = 0;
			if (data.combined) {
				data.combined.forEach(item => {
					roomTotal += parseFloat(item.room || 0);
					dayTourTotal += parseFloat(item.daytour || 0);
				});
			}

			document.getElementById('displayRoom').innerText = format(roomTotal);
			document.getElementById('displayDayTour').innerText = format(dayTourTotal);
			document.getElementById('footerRoom').innerText = format(roomTotal);
			document.getElementById('footerDayTour').innerText = format(dayTourTotal);
			document.getElementById('footerExpense').innerText = format(data.totalExpense);
			document.getElementById('footerTotal').innerText = format(data.totalIncome);
			document.getElementById('footerNet').innerText = format(data.netTotal);
			document.getElementById('footerNet').className = `px-4 py-2 text-right font-bold ${data.netTotal >= 0 ? 'text-emerald-700' : 'text-red-600'}`;
		}

		function updateBreakdownTable(data) {
			const tbody = document.getElementById('breakdownTableBody');
			tbody.innerHTML = '';
			if (!data || data.length === 0) {
				tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-4 text-center text-gray-400">No data found.</td></tr>';
				return;
			}
			data.forEach((row, index) => {
				const tr = document.createElement('tr');
				tr.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
				const netClass = row.net >= 0 ? 'text-emerald-700 font-bold' : 'text-red-600 font-bold';
				tr.innerHTML = `
						<td class="px-4 py-2 font-medium text-gray-700">${row.label}</td>
						<td class="px-4 py-2 text-right text-emerald-600">${moneyFormat.format(row.room)}</td>
						<td class="px-4 py-2 text-right text-blue-600">${moneyFormat.format(row.daytour)}</td>
						<td class="px-4 py-2 text-right text-red-500">${moneyFormat.format(row.expense)}</td>
						<td class="px-4 py-2 text-right font-semibold text-gray-800">${moneyFormat.format(row.income)}</td>
						<td class="px-4 py-2 text-right ${netClass}">${moneyFormat.format(row.net)}</td>
					`;
				tbody.appendChild(tr);
			});
		}

		function updateTransactionsTable(transactions) {
			const tbody = document.getElementById('transactionsTableBody');
			tbody.innerHTML = '';
			if (!transactions || transactions.length === 0) {
				tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No transactions found.</td></tr>';
				return;
			}
			transactions.forEach(txn => {
				const tr = document.createElement('tr');
				tr.className = 'hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0';

				let badgeClass = 'bg-gray-100 text-gray-800';
				if (txn.color === 'green') badgeClass = 'bg-emerald-100 text-emerald-800';
				else if (txn.color === 'blue') badgeClass = 'bg-blue-100 text-blue-800';
				else if (txn.color === 'red') badgeClass = 'bg-red-100 text-red-800';

				const amountClass = txn.amount < 0 ? 'text-red-600' : 'text-emerald-600';

				tr.innerHTML = `
						<td class="px-3 py-2 text-gray-600 whitespace-nowrap">${txn.date}</td>
						<td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold ${badgeClass}">${txn.type}</span></td>
						<td class="px-3 py-2 font-mono text-gray-500 text-[10px]">${txn.reference}</td>
						<td class="px-3 py-2 font-medium text-gray-800 truncate max-w-[150px]" title="${txn.customer}">${txn.customer}</td>
						<td class="px-3 py-2 text-gray-500 truncate max-w-[200px]" title="${txn.description}">${txn.description}</td>
						<td class="px-3 py-2 text-gray-600 text-[11px]">${txn.method}</td>
						<td class="px-3 py-2 text-right font-bold ${amountClass}">${moneyFormat.format(Math.abs(txn.amount))}</td>
					`;
				tbody.appendChild(tr);
			});
		}

		function exportReport(type) {
			const period = document.getElementById('periodType').value;
			const filterValue = getFilterValue();
			if (type === 'pdf') {
				const url = `{{ route('admin.report.export_pdf') }}?period=${period}&filter_value=${filterValue}`;
				window.open(url, '_blank');
			}
		}
	</script>
@endsection