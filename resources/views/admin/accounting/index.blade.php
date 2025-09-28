@extends('layouts.admin')
@section('title', 'Accounting Dashboard')
@php
    $active = 'accounting';
@endphp

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="p-6 bg-white rounded-xl border border-gray-100 shadow-sm h-full">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
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
        @php
            $cards = [
                ['title'=>'Total Revenue','id'=>'totalRevenue','icon'=>'fa-wallet','bg'=>'green','value'=>'₱0.00'],
                ['title'=>'Average Monthly','id'=>'averageMonthly','icon'=>'fa-calendar-alt','bg'=>'yellow','value'=>'₱0.00'],
                ['title'=>'Best Month','id'=>'bestMonthName','icon'=>'fa-star','bg'=>'purple','value'=>'N/A','incomeId'=>'bestMonthIncome','incomeValue'=>'₱0.00'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="bg-{{ $card['bg'] }}-50 rounded-2xl border border-{{ $card['bg'] }}-100 shadow-lg p-6 flex flex-col items-start">
                <div class="p-3 bg-{{ $card['bg'] }}-100 rounded-xl shadow-sm mb-4">
                    <i class="fas {{ $card['icon'] }} text-{{ $card['bg'] }}-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $card['title'] }}</h3>
                <p class="text-2xl font-bold text-{{ $card['bg'] }}-600" id="{{ $card['id'] }}">{{ $card['value'] }}</p>
                @if(isset($card['incomeId']))
                    <p class="text-lg font-bold text-{{ $card['bg'] }}-600" id="{{ $card['incomeId'] }}">{{ $card['incomeValue'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Monthly Income Chart -->
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
                    <!-- Populated via JS -->
                </tbody>
            </table>
        </div>
    </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let incomeChart = null;

async function fetchAccountingData() {
    try {
        const res = await fetch("{{ route('admin.api.monthly-income') }}");
        const data = await res.json();

        // --- Update summary cards ---
        document.getElementById('totalRevenue').textContent = `₱${Number(data.totalRevenue).toLocaleString()}`;
        document.getElementById('averageMonthly').textContent = `₱${Number(data.averageMonthly).toLocaleString()}`;
        document.getElementById('bestMonthName').textContent = data.bestMonth.month ?? 'N/A';
        document.getElementById('bestMonthIncome').textContent = `₱${Number(data.bestMonth.income ?? 0).toLocaleString()}`;

        // --- Populate revenue breakdown table ---
        const tbody = document.getElementById('revenueBreakdown');
        tbody.innerHTML = '';

        const labels = data.chartData.labels || [];
        const roomData = data.chartData.datasets[0]?.data || [];
        const dayTourData = data.chartData.datasets[1]?.data || [];

        labels.forEach((label, i) => {
            const room = roomData[i] ?? 0;
            const daytour = dayTourData[i] ?? 0;
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

        // --- Render or update Chart.js line chart ---
        const ctx = document.getElementById('incomeChart').getContext('2d');
        if (incomeChart) incomeChart.destroy();

        incomeChart = new Chart(ctx, {
            type: 'line',
            data: data.chartData,
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₱' + value.toLocaleString()
                        }
                    }
                }
            }
        });

    } catch (err) {
        console.error('Error fetching accounting data:', err);
    }
}

document.addEventListener('DOMContentLoaded', fetchAccountingData);

async function fetchTopPerformers() {
    const res = await fetch("{{ route('admin.api.top-performers') }}");
    const data = await res.json();

    const roomsList = document.getElementById('topRooms');
    roomsList.innerHTML = '';
    data.rooms.forEach(r => {
        roomsList.innerHTML += `<li>${r.facility} - <strong>₱${Number(r.total_income).toLocaleString()}</strong></li>`;
    });

    const dayToursList = document.getElementById('topDayTours');
    dayToursList.innerHTML = '';
    data.daytours.forEach(r => {
        dayToursList.innerHTML += `<li>${r.facility} - <strong>₱${Number(r.total_income).toLocaleString()}</strong></li>`;
    });

    const eventsList = document.getElementById('topEvents');
    eventsList.innerHTML = '';
    data.events.forEach(r => {
        eventsList.innerHTML += `<li>${r.package} - <strong>₱${Number(r.total_income).toLocaleString()}</strong></li>`;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    fetchAccountingData();
    fetchTopPerformers();
});

</script>

@endsection
