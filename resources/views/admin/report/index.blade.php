@extends('layouts.admin')

@section('title', 'Financial Report | Mt. Claramuel Resort')
@php
	$active = 'reports';
@endphp

@section('content_css')
	<style>
		/* PRINT STYLES - OPTIMIZED FOR FULL DATA CAPTURE */
		@media print {

			/* 1. Reset Page */
			@page {
				size: auto;
				/* auto is usually best, or 'A4 portrait' */
				margin: 10mm;
			}

			/* 2. Hide Everything by Default */
			body {
				visibility: hidden !important;
				background-color: white !important;
				height: auto !important;
				width: auto !important;
				margin: 0 !important;
				padding: 0 !important;
				overflow: visible !important;
			}

			/* 3. Hide specific UI elements completely from layout flow */
			.no-print,
			nav,
			aside,
			header,
			footer,
			.sidebar,
			.filter-section,
			#loadingOverlay,
			button {
				display: none !important;
			}

			/* 4. Make the Report Container the ONLY visible thing and position it at 0,0 */
			.report-container {
				visibility: visible !important;
				position: absolute !important;
				top: 0 !important;
				left: 0 !important;
				width: 100% !important;
				margin: 0 !important;
				padding: 0 !important;
				border: none !important;
				box-shadow: none !important;
				z-index: 9999;
				min-width: 100% !important;
			}

			/* 5. Force Background Colors & Graphics */
			* {
				-webkit-print-color-adjust: exact !important;
				print-color-adjust: exact !important;
				color-adjust: exact !important;
			}

			/* 6. Handle Tables (Repeat Headers, Don't Cut Rows) */
			table {
				width: 100% !important;
				border-collapse: collapse !important;
				page-break-inside: auto !important;
			}

			thead {
				display: table-header-group !important;
				/* Repeats header on every page */
			}

			tfoot {
				display: table-footer-group !important;
				/* Repeats footer if needed */
			}

			tr {
				page-break-inside: avoid !important;
				page-break-after: auto !important;
			}

			td,
			th {
				page-break-inside: avoid !important;
			}

			/* 7. Handle Overflow/Scrollbars (CRITICAL: prevents cutting off data) */
			.overflow-x-auto,
			.overflow-hidden,
			.overflow-y-auto {
				overflow: visible !important;
				height: auto !important;
				display: block !important;
				width: auto !important;
			}

			/* 8. Text Adjustments for Print */
			.truncate {
				white-space: normal !important;
				overflow: visible !important;
				text-overflow: clip !important;
			}

			.whitespace-nowrap {
				white-space: normal !important;
				/* Wrap long text so it doesn't cut off */
			}

			/* 9. Signature Section */
			.signature-section {
				page-break-inside: avoid !important;
				margin-top: 30px !important;
				padding-bottom: 20px;
			}
		}

		/* Standard CSS */
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
	</style>
@endsection

@section('content')

	{{-- CONTROLS BAR --}}
	<div class="filter-section mb-6 flex flex-col md:flex-row justify-end items-center no-print">
		<div
			class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-lg shadow-sm border border-gray-200 w-full md:w-auto">
			{{-- Filters --}}
			<div class="flex flex-col">
				<label for="periodType" class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Period
					Type</label>
				<select id="periodType"
					class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 min-w-[120px]">
					<option value="daily">Daily</option>
					<option value="monthly" selected>Monthly</option>
					<option value="yearly">Yearly</option>
				</select>
			</div>
			<div class="flex flex-col">
				<span class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Select Date</span>
				<div id="dailyInputGroup" class="filter-input-group hidden">
					<input type="date" id="dailyInput"
						class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 w-40">
				</div>
				<div id="monthlyInputGroup" class="filter-input-group">
					<input type="month" id="monthlyInput"
						class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 w-40">
				</div>
				<div id="yearlyInputGroup" class="filter-input-group hidden">
					<select id="yearlyInput"
						class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 w-40"></select>
				</div>
			</div>
			<div class="border-l border-gray-300 h-8 mx-2"></div>
			{{-- Buttons --}}
			<div class="flex items-end gap-2 h-full pb-0.5">
				<button id="applyFilters"
					class="cursor-pointer text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors shadow-sm">
					<i class="fas fa-filter mr-1"></i> Apply
				</button>
				<button onclick="window.print()"
					class="cursor-pointer text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 font-medium rounded-lg text-sm px-4 py-2.5 transition-colors">
					<i class="fas fa-print mr-1"></i> Print
				</button>
				<button onclick="exportReport('pdf')"
					class="cursor-pointer text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm px-4 py-2.5 transition-colors shadow-sm">
					<i class="fas fa-file-pdf mr-1"></i> PDF
				</button>
				<button onclick="exportReport('csv')"
					class="cursor-pointer text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-4 py-2.5 transition-colors shadow-sm">
					<i class="fas fa-file-csv mr-1"></i> CSV
				</button>
			</div>
		</div>
	</div>

	{{-- REPORT CONTAINER --}}
	{{-- Removed relative/min-h for cleaner print flow --}}
	<div class="report-container bg-white shadow-lg rounded-none md:rounded-lg border border-gray-200">

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
			{{-- HEADER --}}
			<div class="border-b-2 border-blue-600 pb-4 mb-6 flex justify-between items-end">
				<div>
					<h1 class="text-3xl font-extrabold text-blue-800 tracking-tight">FINANCIAL REPORT</h1>
					<p class="text-xs text-gray-500 mt-1 uppercase tracking-wide">
						{{ config('app.name', 'Mt. Claramuel Resort') }}
					</p>
				</div>
				<div class="text-right">
					<p class="text-xs text-gray-500">Generated: <span
							id="generatedDate">{{ now()->format('M d, Y h:i A') }}</span></p>
					<p class="text-sm font-bold text-gray-800 mt-1">Period: <span id="activeFilterLabel"
							class="text-blue-600">Loading...</span></p>
				</div>
			</div>

			{{-- SUMMARY CARDS --}}
			<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 sm:p-6 mb-8 print:border-gray-300">
				<div
					class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 divide-y sm:divide-y-0 sm:divide-x divide-gray-300">
					<div class="flex flex-col items-center text-center px-2 py-3">
						<div class="text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Total Revenue
						</div>
						<div class="text-xl sm:text-2xl font-bold text-emerald-600" id="displayTotalIncome"><span
								class="loading-spinner"></span></div>
						<div class="text-[10px] text-gray-400 mt-1">Gross Income</div>
					</div>
					<div class="px-2 py-3">
						<div class="text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-2 text-center">
							Revenue Sources</div>
						<div class="flex flex-col gap-2">
							<div class="text-xs flex justify-between"><span class="text-gray-500">Day
									Tours</span><span class="font-bold text-blue-700"
									id="displayDayTour">₱0.00</span></div>
							<div class="text-xs flex justify-between"><span class="text-gray-500">Rooms</span><span
									class="font-bold text-emerald-700" id="displayRoom">₱0.00</span></div>
						</div>
					</div>
					<div class="flex flex-col items-center text-center px-2 py-3">
						<div class="text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Total Expenses
						</div>
						<div class="text-xl sm:text-2xl font-bold text-red-600" id="displayTotalExpense"><span
								class="loading-spinner"></span></div>
						<div class="text-[10px] text-gray-400 mt-1">Operational Costs</div>
					</div>
					<div class="flex flex-col items-center text-center px-2 py-3">
						<div class="text-[10px] uppercase text-gray-500 font-bold tracking-wider mb-1">Net Profit</div>
						<div class="text-xl sm:text-2xl font-bold text-gray-800" id="displayNetProfit"><span
								class="loading-spinner"></span></div>
						<div class="text-[10px] text-gray-400 mt-1">Income − Expenses</div>
					</div>
				</div>
			</div>

			{{-- SECTION 1: PERIOD BREAKDOWN --}}
			<div class="mb-8">
				<div class="flex items-center mb-3">
					<div class="w-1 h-4 bg-blue-600 mr-2"></div>
					<h3 class="text-xs font-bold text-gray-700 uppercase">Period Summary Breakdown</h3>
				</div>
				<div class="border border-gray-200 rounded-sm">
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
						<tbody id="breakdownTableBody" class="divide-y divide-gray-200"></tbody>
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
				{{-- Removed overflow-x-auto, added print specific handling in CSS --}}
				<div class="border border-gray-200 rounded-sm">
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
								<td colspan="7" class="px-4 py-6 text-center text-gray-400 italic">Loading...</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			{{-- NEW SECTION: SIGNATORIES (CLERK & SIGNATORY) --}}
			<div class="mt-16 px-6 signature-section">
				<div class="flex justify-between items-end">
					{{-- Left Side: Clerk --}}
					<div class="flex flex-col">
						<div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-8">Prepared by:
						</div>
						<div class="border-b border-gray-800 w-64 mb-2"></div>
						<div class="font-bold text-gray-800 uppercase text-sm outline-none cursor-text hover:bg-gray-100 hover:text-blue-600 focus:bg-blue-50 focus:text-black focus:ring-2 focus:ring-blue-300 rounded px-2 -mx-2 transition-all duration-200 border border-transparent hover:border-dashed hover:border-gray-300"
							contenteditable="true" title="Click to edit name">
							{{ Auth::user()->firstname ?? 'CLERK' }} {{ Auth::user()->lastname ?? 'NAME' }}
						</div>
						<div class="text-[10px] text-gray-500">Clerk / Front Desk</div>
					</div>

					{{-- Right Side: Signatory --}}
					<div class="flex flex-col items-end">
						<div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-8 text-left w-64">
							Noted by:</div>
						<div class="border-b border-gray-800 w-64 mb-2"></div>
						<div class="font-bold text-gray-800 uppercase text-sm outline-none text-right w-64 cursor-text hover:bg-gray-100 hover:text-blue-600 focus:bg-blue-50 focus:text-black focus:ring-2 focus:ring-blue-300 rounded px-2 -mx-2 transition-all duration-200 border border-transparent hover:border-dashed hover:border-gray-300"
							contenteditable="true" title="Click to edit name">
							Administrator / OWNER
						</div>
						<div class="text-[10px] text-gray-500 text-right w-64">Signatory</div>
					</div>
				</div>
			</div>

			<div class="mt-8 pt-4 border-t border-gray-100 text-center text-[10px] text-gray-400">
				System Generated Report | {{ config('app.name') }} | {{ now()->year }}
			</div>
		</div>
	</div>

@endsection

@section('content_js')
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<script>
		const moneyFormat = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 2 });

		$(document).ready(function () {
			initializeFilters();
			loadData();

			$('#periodType').change(handlePeriodChange);
			$('#applyFilters').click(loadData);
			$('#dailyInput, #monthlyInput, #yearlyInput').keypress(function (e) {
				if (e.which === 13) loadData();
			});
		});

		function initializeFilters() {
			const today = new Date();
			const year = today.getFullYear();
			const month = String(today.getMonth() + 1).padStart(2, '0');
			const day = String(today.getDate()).padStart(2, '0');

			const yearSelect = $('#yearlyInput');
			yearSelect.empty();
			for (let y = year + 2; y >= year - 5; y--) {
				yearSelect.append(new Option(y, y));
			}
			yearSelect.val(year);

			$('#dailyInput').val(`${year}-${month}-${day}`);
			$('#monthlyInput').val(`${year}-${month}`);
			handlePeriodChange();
		}

		function handlePeriodChange() {
			const period = $('#periodType').val();
			$('.filter-input-group').addClass('hidden');
			if (period === 'daily') $('#dailyInputGroup').removeClass('hidden');
			else if (period === 'monthly') $('#monthlyInputGroup').removeClass('hidden');
			else if (period === 'yearly') $('#yearlyInputGroup').removeClass('hidden');
		}

		function getFilterValue() {
			const period = $('#periodType').val();
			if (period === 'daily') return $('#dailyInput').val();
			if (period === 'monthly') return $('#monthlyInput').val();
			if (period === 'yearly') return $('#yearlyInput').val();
			return '';
		}

		async function loadData() {
			const period = $('#periodType').val();
			const filterValue = getFilterValue();

			if (!filterValue) { alert('Please select a valid date/month/year'); return; }

			const $btn = $('#applyFilters');
			const originalBtnText = $btn.html();
			$btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');
			$('#loadingOverlay').fadeIn(200);
			$('#activeFilterLabel').text(getLabelText(period, filterValue));
			$('#generatedDate').text(new Date().toLocaleString());

			const url = `{{ route('admin.report.api') }}?period=${period}&filter_value=${filterValue}`;

			try {
				const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
				if (!response.ok) throw new Error('Failed to fetch data');
				const data = await response.json();

				updateSummaryCards(data);
				updateBreakdownTable(data.combined);
				updateTransactionsTable(data.transactions);

			} catch (error) {
				console.error('Error:', error);
				alert('Failed to load data.');
			} finally {
				$btn.prop('disabled', false).html(originalBtnText);
				$('#loadingOverlay').fadeOut(200);
			}
		}

		function updateSummaryCards(data) {
			const format = (val) => moneyFormat.format(val || 0);

			$('#displayTotalIncome').text(format(data.totalIncome));
			$('#displayTotalExpense').text(format(data.totalExpense));

			const $netEl = $('#displayNetProfit');
			$netEl.text(format(data.netTotal));
			$netEl.removeClass('text-emerald-600 text-red-600').addClass(data.netTotal >= 0 ? 'text-emerald-600' : 'text-red-600');

			let roomTotal = 0, dayTourTotal = 0;
			if (data.combined) {
				data.combined.forEach(item => {
					roomTotal += parseFloat(item.room || 0);
					dayTourTotal += parseFloat(item.daytour || 0);
				});
			}

			$('#displayRoom').text(format(roomTotal));
			$('#displayDayTour').text(format(dayTourTotal));

			$('#footerRoom').text(format(roomTotal));
			$('#footerDayTour').text(format(dayTourTotal));
			$('#footerExpense').text(format(data.totalExpense));
			$('#footerTotal').text(format(data.totalIncome));
			const $footerNet = $('#footerNet');
			$footerNet.text(format(data.netTotal));
			$footerNet.removeClass('text-emerald-700 text-red-600').addClass(data.netTotal >= 0 ? 'text-emerald-700' : 'text-red-600');
		}

		function updateBreakdownTable(data) {
			const $tbody = $('#breakdownTableBody');
			$tbody.empty();

			if (!data || data.length === 0) {
				$tbody.html('<tr><td colspan="6" class="px-4 py-4 text-center text-gray-400">No data found.</td></tr>');
				return;
			}

			data.forEach((row, index) => {
				const bgClass = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
				const netClass = row.net >= 0 ? 'text-emerald-700 font-bold' : 'text-red-600 font-bold';

				$tbody.append(`
					<tr class="${bgClass}">
					    <td class="px-4 py-2 font-medium text-gray-700">${row.label}</td>
					    <td class="px-4 py-2 text-right text-emerald-600">${moneyFormat.format(row.room)}</td>
					    <td class="px-4 py-2 text-right text-blue-600">${moneyFormat.format(row.daytour)}</td>
					    <td class="px-4 py-2 text-right text-red-500">${moneyFormat.format(row.expense)}</td>
					    <td class="px-4 py-2 text-right font-semibold text-gray-800">${moneyFormat.format(row.income)}</td>
					    <td class="px-4 py-2 text-right ${netClass}">${moneyFormat.format(row.net)}</td>
					</tr>
				 `);
			});
		}

		function updateTransactionsTable(transactions) {
			const $tbody = $('#transactionsTableBody');
			$tbody.empty();

			if (!transactions || transactions.length === 0) {
				$tbody.html('<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No transactions found.</td></tr>');
				return;
			}

			transactions.forEach(txn => {
				let badgeClass = 'bg-gray-100 text-gray-800';
				if (txn.color === 'green') badgeClass = 'bg-emerald-100 text-emerald-800';
				else if (txn.color === 'blue') badgeClass = 'bg-blue-100 text-blue-800';
				else if (txn.color === 'red') badgeClass = 'bg-red-100 text-red-800';

				const amountClass = txn.amount < 0 ? 'text-red-600' : 'text-emerald-600';

				// NOTE: removed whitespace-nowrap and max-w for better printing
				$tbody.append(`
					<tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
					    <td class="px-3 py-2 text-gray-600 whitespace-nowrap">${txn.date}</td>
					    <td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold ${badgeClass}">${txn.type}</span></td>
					    <td class="px-3 py-2 font-mono text-gray-500 text-[10px]">${txn.reference}</td>
					    <td class="px-3 py-2 font-medium text-gray-800" title="${txn.customer}">${txn.customer}</td>
					    <td class="px-3 py-2 text-gray-500" title="${txn.description}">${txn.description}</td>
					    <td class="px-3 py-2 text-gray-600 text-[11px]">${txn.method}</td>
					    <td class="px-3 py-2 text-right font-bold ${amountClass}">${moneyFormat.format(Math.abs(txn.amount))}</td>
					</tr>
				 `);
			});
		}

		function getLabelText(period, value) {
			if (!value) return '-';
			if (period === 'daily') return new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
			if (period === 'monthly') {
				const [y, m] = value.split('-');
				const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
				return `${months[parseInt(m) - 1]} ${y}`;
			}
			if (period === 'yearly') return `Year ${value}`;
			return '';
		}

		function exportReport(type) {
			const period = $('#periodType').val();
			const filterValue = getFilterValue();
			let url = '';

			if (type === 'pdf') {
				url = `{{ route('admin.report.export_pdf') }}?period=${period}&filter_value=${filterValue}`;
			} else if (type === 'csv') {
				url = `{{ route('admin.report.export') }}?period=${period}&filter_value=${filterValue}`;
			}
			if (url) window.open(url, '_blank');
		}
	</script>
@endsection