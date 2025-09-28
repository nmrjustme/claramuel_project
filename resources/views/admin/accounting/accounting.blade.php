@extends('layouts.admin')
@section('title', 'Accounting Dashboard')
@php
    $active = 'accounting';
@endphp

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="p-6 bg-white rounded-xl border border-gray-100 shadow-sm h-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Accounting Dashboard</h2>
            <p class="text-sm text-gray-500">View and track your revenue, monthly trends, and performance.</p>
        </div>

        <a href="{{ route('admin.reports.export') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow transition duration-200">
            <i class="fas fa-file-export mr-2"></i> Export Reports
        </a>
    </div>

    <!-- Summary Stats -->
    <div class="grid md:grid-cols-3 gap-6 mb-6">
        <div class="bg-green-50 rounded-2xl border border-green-100 shadow-lg p-6 flex flex-col items-start">
            <div class="p-3 bg-green-100 rounded-xl shadow-sm mb-4">
                <i class="fas fa-wallet text-green-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Total Revenue</h3>
            <p class="text-2xl font-bold text-green-600" id="totalRevenue">₱0.00</p>
        </div>

        <div class="bg-yellow-50 rounded-2xl border border-yellow-100 shadow-lg p-6 flex flex-col items-start">
            <div class="p-3 bg-yellow-100 rounded-xl shadow-sm mb-4">
                <i class="fas fa-calendar-alt text-yellow-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Average Monthly</h3>
            <p class="text-2xl font-bold text-yellow-600" id="averageMonthly">₱0.00</p>
        </div>

        <div class="bg-purple-50 rounded-2xl border border-purple-100 shadow-lg p-6 flex flex-col items-start">
            <div class="p-3 bg-purple-100 rounded-xl shadow-sm mb-4">
                <i class="fas fa-star text-purple-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Best Month</h3>
            <p class="text-xl font-semibold text-purple-600" id="bestMonthName">N/A</p>
            <p class="text-lg font-bold text-purple-600" id="bestMonthIncome">₱0.00</p>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 mb-6">
        <div class="flex items-center space-x-3 mb-6">
            <div class="p-3 bg-blue-100 rounded-xl shadow-sm">
                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800">Monthly Income Trend</h3>
        </div>
        <canvas id="incomeChart" class="w-full h-80"></canvas>
    </div>

    <!-- Revenue Breakdown Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <div class="flex items-center space-x-3 mb-6">
            <div class="p-3 bg-red-100 rounded-xl shadow-sm">
                <i class="fas fa-receipt text-red-500 text-xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800">Revenue Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-left text-gray-700">
                <thead class="bg-gray-100 rounded-t-xl">
                    <tr>
                        <th class="px-4 py-2">Month</th>
                        <th class="px-4 py-2">Room Income</th>
                        <th class="px-4 py-2">Day Tour Income</th>
                        <th class="px-4 py-2">Total</th>
                    </tr>
                </thead>
                <tbody id="revenueBreakdown">
                    <!-- Filled dynamically via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function fetchAccountingData() {
    const res = await fetch("{{ route('admin.api.monthly-income') }}");
    const data = await res.json();

    // Update summary stats
    document.getElementById('totalRevenue').textContent = `₱${Number(data.totalRevenue).toLocaleString()}`;
    document.getElementById('averageMonthly').textContent = `₱${Number(data.averageMonthly).toLocaleString()}`;
    document.getElementById('bestMonthName').textContent = data.bestMonth.month ?? 'N/A';
    document.getElementById('bestMonthIncome').textContent = `₱${Number(data.bestMonth.income ?? 0).toLocaleString()}`;

    // Populate breakdown table
    const tbody = document.getElementById('revenueBreakdown');
    tbody.innerHTML = '';
    data.chartData.labels.forEach((label, i) => {
        const room = data.chartData.datasets[0].data[i];
        const daytour = data.chartData.datasets[1].data[i];
        const total = room + daytour;
        tbody.innerHTML += `
            <tr class="border-b last:border-b-0">
                <td class="px-4 py-2">${label}</td>
                <td class="px-4 py-2">₱${Number(room).toLocaleString()}</td>
                <td class="px-4 py-2">₱${Number(daytour).toLocaleString()}</td>
                <td class="px-4 py-2 font-semibold">₱${Number(total).toLocaleString()}</td>
            </tr>
        `;
    });

    // Render Chart.js
    new Chart(document.getElementById('incomeChart'), {
        type: 'line',
        data: data.chartData,
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' }},
            scales: {
                y: { beginAtZero: true, ticks: { callback: value => '₱' + value.toLocaleString() }}
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', fetchAccountingData);
</script>
@endsection
