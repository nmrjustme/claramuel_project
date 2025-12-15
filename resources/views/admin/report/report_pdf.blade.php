<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Financial Report | Mt. Claramuel Resort</title>
	<style>
		/* ✅ FIX 1: Use 'DejaVu Sans' to display the Peso (₱) symbol correctly */
		body {
			font-family: 'DejaVu Sans', sans-serif;
			font-size: 10pt;
			color: #333;
			line-height: 1.4;
		}

		/* ✅ FIX 2: Define Page Margins to stop Footer Overlap */
		@page {
			margin: 100px 25px;
			/* Top/Bottom margin reserves space for Header/Footer */
		}

		/* Layout Helpers */
		.text-right {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		.font-bold {
			font-weight: bold;
		}

		.uppercase {
			text-transform: uppercase;
		}

		.mb-4 {
			margin-bottom: 1rem;
		}

		/* Colors */
		.text-blue {
			color: #1d4ed8;
		}

		.text-green {
			color: #059669;
		}

		.text-emerald {
			color: #047857;
		}

		.text-red {
			color: #dc2626;
		}

		.text-gray {
			color: #6b7280;
		}

		.bg-gray {
			background-color: #f3f4f6;
		}

		/* Fixed Header */
		header {
			position: fixed;
			top: -80px;
			left: 0px;
			right: 0px;
			height: 80px;
			border-bottom: 2px solid #1d4ed8;
		}

		/* Fixed Footer */
		footer {
			position: fixed;
			bottom: -60px;
			left: 0px;
			right: 0px;
			height: 50px;
			text-align: center;
			font-size: 8pt;
			color: #9ca3af;
			border-top: 1px solid #e5e7eb;
			padding-top: 10px;
		}

		/* Header Content Styling */
		.header-table {
			width: 100%;
		}

		.company-name {
			font-size: 18pt;
			font-weight: bold;
			color: #1e3a8a;
		}

		.report-title {
			font-size: 14pt;
			font-weight: bold;
			text-transform: uppercase;
			text-align: right;
		}

		/* Summary Cards (Table Layout) */
		.summary-table {
			width: 100%;
			border-collapse: separate;
			border-spacing: 5px 0;
			margin-bottom: 25px;
			margin-top: 10px;
		}

		.summary-box {
			background-color: #f9fafb;
			border: 1px solid #e5e7eb;
			padding: 8px;
			border-radius: 4px;
			height: 70px;
			/* Fixed height for alignment */
		}

		.summary-label {
			font-size: 7pt;
			text-transform: uppercase;
			color: #6b7280;
			margin-bottom: 4px;
		}

		.summary-value {
			font-size: 13pt;
			font-weight: bold;
		}

		/* Revenue Source Breakdown Styling */
		.source-row {
			font-size: 8pt;
			margin-bottom: 2px;
		}

		.source-row span {
			float: right;
			font-weight: bold;
		}

		.clearfix:after {
			content: "";
			display: table;
			clear: both;
		}

		/* Data Tables */
		.data-table {
			width: 100%;
			border-collapse: collapse;
			font-size: 9pt;
		}

		.data-table th {
			background-color: #1d4ed8;
			color: white;
			padding: 8px;
			text-transform: uppercase;
			font-size: 7pt;
			text-align: left;
		}

		.data-table td {
			border-bottom: 1px solid #e5e7eb;
			padding: 6px 8px;
		}

		.data-table tr:nth-child(even) {
			background-color: #f9fafb;
		}

		/* Ensure tables don't break awkwardly */
		tr {
			page-break-inside: avoid;
		}
	</style>
</head>

<body>

	<header>
		<table class="header-table">
			<tr>
				<td width="50%" style="vertical-align: bottom;">
					<div class="company-name">{{ config('app.name', 'Mt. Claramuel Resort') }}</div>
					<div class="text-gray" style="font-size: 9pt;">Official Financial Statement</div>
				</td>
				<td width="50%" class="text-right" style="vertical-align: bottom;">
					<div class="report-title text-blue">Financial Report</div>
					<div style="font-size: 9pt; margin-top: 5px;">
						Generated: {{ now()->format('M d, Y h:i A') }}
					</div>
					<div style="font-size: 9pt; font-weight: bold;">
						Period:
						@if($period == 'monthly')
							{{ \Carbon\Carbon::parse($filterValue)->format('F Y') }}
						@elseif($period == 'daily')
							{{ \Carbon\Carbon::parse($filterValue)->format('F d, Y') }}
						@elseif($period == 'yearly')
							Year {{ $filterValue }}
						@endif
					</div>
				</td>
			</tr>
		</table>
	</header>

	<footer>
		System Generated Report | {{ config('app.name') }} | {{ now()->year }} <br>
		This document is for internal use only.
	</footer>

	<main>

		@php
			// Calculate breakdown totals for the "Revenue Sources" card
			$totalRoom = collect($summary['combined'])->sum('room');
			$totalDayTour = collect($summary['combined'])->sum('daytour');
		   @endphp

		<table class="summary-table">
			<tr>
				<td width="25%">
					<div class="summary-box text-center">
						<div class="summary-label">Total Revenue</div>
						<div class="summary-value text-green">₱{{ number_format($summary['totalIncome'], 2) }}
						</div>
						<div style="font-size: 6pt; color: #9ca3af; margin-top: 2px;">Gross Income</div>
					</div>
				</td>

				<td width="25%">
					<div class="summary-box">
						<div class="summary-label text-center"
							style="border-bottom: 1px solid #eee; padding-bottom:2px; margin-bottom:4px;">Revenue
							Sources</div>

						<div class="source-row clearfix">
							<span class="text-emerald">₱{{ number_format($totalRoom, 2) }}</span>
							<div style="color: #555;">Rooms:</div>
						</div>

						<div class="source-row clearfix">
							<span class="text-blue">₱{{ number_format($totalDayTour, 2) }}</span>
							<div style="color: #555;">Day Tours:</div>
						</div>
					</div>
				</td>

				<td width="25%">
					<div class="summary-box text-center">
						<div class="summary-label">Total Expenses</div>
						<div class="summary-value text-red">₱{{ number_format($summary['totalExpense'], 2) }}
						</div>
						<div style="font-size: 6pt; color: #9ca3af; margin-top: 2px;">Operational Costs</div>
					</div>
				</td>

				<td width="25%">
					<div class="summary-box text-center">
						<div class="summary-label">Net Profit</div>
						<div class="summary-value {{ $summary['netTotal'] >= 0 ? 'text-green' : 'text-red' }}">
							₱{{ number_format($summary['netTotal'], 2) }}
						</div>
						<div style="font-size: 6pt; color: #9ca3af; margin-top: 2px;">Income - Expenses</div>
					</div>
				</td>
			</tr>
		</table>

		<div class="mb-4">
			<h3
				style="border-left: 4px solid #1d4ed8; padding-left: 10px; margin-bottom: 10px; color: #374151; font-size: 11pt;">
				SUMMARY BREAKDOWN</h3>
			<table class="data-table">
				<thead>
					<tr>
						<th width="20%">Period</th>
						<th class="text-right" width="15%">Room Rev</th>
						<th class="text-right" width="15%">Day Tour Rev</th>
						<th class="text-right" width="15%">Expenses</th>
						<th class="text-right" width="15%">Total Rev</th>
						<th class="text-right" width="15%">Net Profit</th>
					</tr>
				</thead>
				<tbody>
					@forelse($summary['combined'] as $row)
						<tr>
							<td class="font-bold">{{ $row['label'] }}</td>
							<td class="text-right text-emerald">₱{{ number_format($row['room'], 2) }}</td>
							<td class="text-right text-blue">₱{{ number_format($row['daytour'], 2) }}</td>
							<td class="text-right text-red">₱{{ number_format($row['expense'], 2) }}</td>
							<td class="text-right font-bold">₱{{ number_format($row['income'], 2) }}</td>
							<td class="text-right font-bold {{ $row['net'] >= 0 ? 'text-green' : 'text-red' }}">
								₱{{ number_format($row['net'], 2) }}
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="text-center" style="padding: 20px;">No breakdown data available.
							</td>
						</tr>
					@endforelse
				</tbody>
				<tfoot>
					<tr style="background-color: #e5e7eb; font-weight: bold;">
						<td style="border-top: 2px solid #9ca3af;">GRAND TOTAL</td>
						<td class="text-right text-emerald" style="border-top: 2px solid #9ca3af;">
							₱{{ number_format($totalRoom, 2) }}</td>
						<td class="text-right text-blue" style="border-top: 2px solid #9ca3af;">
							₱{{ number_format($totalDayTour, 2) }}</td>
						<td class="text-right text-red" style="border-top: 2px solid #9ca3af;">
							₱{{ number_format($summary['totalExpense'], 2) }}</td>
						<td class="text-right" style="border-top: 2px solid #9ca3af;">
							₱{{ number_format($summary['totalIncome'], 2) }}</td>
						<td class="text-right {{ $summary['netTotal'] >= 0 ? 'text-green' : 'text-red' }}"
							style="border-top: 2px solid #9ca3af;">
							₱{{ number_format($summary['netTotal'], 2) }}
						</td>
					</tr>
				</tfoot>
			</table>
		</div>

		<div class="mt-4">
			<h3
				style="border-left: 4px solid #1d4ed8; padding-left: 10px; margin-bottom: 10px; color: #374151; font-size: 11pt;">
				DETAILED TRANSACTION LOG</h3>
			<table class="data-table">
				<thead>
					<tr>
						<th>Date</th>
						<th>Type</th>
						<th>Ref #</th>
						<th>Customer</th>
						<th>Description</th>
						<th>Method</th>
						<th class="text-right">Amount</th>
					</tr>
				</thead>
				<tbody>
					@forelse($transactions as $txn)
						@php $txn = (array) $txn; @endphp
						<tr>
							<td>{{ $txn['date'] }}</td>
							<td>
								<span style="font-size: 7pt; font-weight: bold; text-transform: uppercase;">
									{{ $txn['type'] }}
								</span>
							</td>
							<td style="font-family: monospace;">{{ $txn['reference'] }}</td>

							<td class="capitalize">{{ \Illuminate\Support\Str::limit($txn['customer'], 20) }}</td>
							<td class="capitalize">{{ \Illuminate\Support\Str::limit($txn['description'], 30) }}</td>

							<td>{{ $txn['method'] }}</td>
							<td class="text-right font-bold {{ $txn['amount'] < 0 ? 'text-red' : 'text-green' }}">
								{{ $txn['amount'] < 0 ? '-' : '' }}₱{{ number_format(abs($txn['amount']), 2) }}
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="7" class="text-center" style="padding: 20px; color: #9ca3af;">No transactions
								found.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</main>

</body>

</html>