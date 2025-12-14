@extends('layouts.admin')
@section('title', 'Room Earnings Dashboard')

@php $active = 'earnings'; @endphp

@section('content_css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .min-h-screen {
            position: relative;
        }

        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        /* Chart containers - Crucial for ChartJS responsiveness */
        .chart-box {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .pie-box {
            position: relative;
            height: 250px;
            /* Slightly taller for mobile legends */
            width: 100%;
        }

        @media (min-width: 1024px) {
            .pie-box {
                height: 200px;
            }
        }

        .accounting-card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.75rem;
            background-color: white;
        }

        .accounting-card-header {
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    {{-- Header --}}
    <div class="accounting-card mb-6 border-t-4 border-t-blue-500">
        <div class="accounting-card-header">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                </div>
                <h1 class="text-gray-800 font-bold text-lg md:text-xl">Room Earnings Report</h1>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-4">
        <div class="flex flex-col xl:flex-row justify-between items-end gap-4">

            {{-- Input Grid: 1 col mobile, 2 col tablet, Flex desktop --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap items-end gap-3 w-full">

                <div class="w-full lg:w-48">
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Category</label>
                    <select id="categorySelect"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat) <option value="{{ $cat }}">{{ $cat }}</option> @endforeach
                    </select>
                </div>

                <div class="w-full lg:w-40">
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Period</label>
                    <select id="periodType"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="daily">Daily</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

                {{-- Dynamic Inputs --}}
                <div id="dailyInput" class="hidden w-full lg:w-auto">
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Date</label>
                    <input type="date" id="dateVal"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                </div>
                <div id="monthlyInput" class="w-full lg:w-auto">
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Month</label>
                    <input type="month" id="monthVal"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                </div>
                <div id="yearlyInput" class="hidden w-full lg:w-auto">
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Year</label>
                    <select id="yearVal" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm"></select>
                </div>

                {{-- Apply Button: Full width on mobile --}}
                <button id="btnApply"
                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm mt-2 lg:mt-0">
                    Apply
                </button>
            </div>

            {{-- Export Buttons --}}
            <div class="flex w-full xl:w-auto gap-2 border-t xl:border-t-0 pt-4 xl:pt-0">
                {{-- <a id="lnkCsv" href="#"
                    class="flex-1 xl:flex-none text-center bg-green-600 text-white px-4 py-2.5 rounded-lg text-sm hover:bg-green-700 transition"><i
                        class="fas fa-file-csv mr-1"></i> CSV</a> --}}
                <a id="lnkPdf" href="#"
                    class="flex-1 xl:flex-none text-center bg-red-600 text-white px-4 py-2.5 rounded-lg text-sm hover:bg-red-700 transition shadow-sm">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-500">
            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Earnings</div>
            <div class="text-2xl md:text-3xl font-bold text-gray-800 mt-2" id="statEarnings">₱0.00</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-purple-500">
            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Rooms Booked</div>
            <div class="text-2xl md:text-3xl font-bold text-gray-800 mt-2" id="statBookings">0</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-cyan-500">
            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Occupancy Rate</div>
            <div class="text-2xl md:text-3xl font-bold text-gray-800 mt-2" id="statOccupancy">0%</div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 border-l-4
                    border-l-green-500">
            <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Best Category</div>
            <div id="cardBestCategory">
                <div class="text-lg md:text-xl font-bold text-gray-800 mt-2 line-clamp-2 leading-tight"
                    id="statBestCategory">
                    -
                </div>
            </div>

        </div>
    </div>

    {{-- Main Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-gray-800 font-bold text-sm mb-4">Earnings Trend</h3>
            <div class="chart-box"><canvas id="earningsChart"></canvas></div>
        </div>
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-gray-800 font-bold text-sm mb-4">Revenue by Category</h3>
            <div class="pie-box flex justify-center"><canvas id="categoryChart"></canvas></div>
            {{-- Legend container handles overflow better on mobile --}}
            <div id="categoryLegend" class="mt-4 text-xs space-y-1 max-h-32 overflow-y-auto pr-2 custom-scrollbar"></div>
        </div>
    </div>

    {{-- Cancellation Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-4 md:p-6">
        <h3 class="text-gray-800 font-bold text-lg mb-6 flex items-center gap-2">
            <i class="fas fa-ban text-red-500"></i> Cancellation & Refunds
        </h3>

        {{-- 2x2 Grid on Mobile, 4x1 on Desktop --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-red-50 p-4 rounded-xl text-center md:text-left">
                <div class="text-red-500 text-[10px] md:text-xs font-bold uppercase">Cancelled Bookings</div>
                <div class="text-lg md:text-2xl font-bold text-gray-800 mt-1" id="statCancelled">0</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-xl text-center md:text-left">
                <div class="text-orange-500 text-[10px] md:text-xs font-bold uppercase">Total Refunds</div>
                <div class="text-lg md:text-2xl font-bold text-gray-800 mt-1" id="statRefunds">₱0</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-xl text-center md:text-left">
                <div class="text-blue-500 text-[10px] md:text-xs font-bold uppercase">Full Refunds</div>
                <div class="text-lg md:text-2xl font-bold text-gray-800 mt-1" id="statFullRef">0</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-xl text-center md:text-left">
                <div class="text-purple-500 text-[10px] md:text-xs font-bold uppercase">Partial Refunds</div>
                <div class="text-lg md:text-2xl font-bold text-gray-800 mt-1" id="statPartRef">0</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 mb-8">
            <div>
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 text-center md:text-left">Cancellation Reasons
                </h4>
                <div class="h-64 relative"><canvas id="reasonsChart"></canvas></div>
            </div>
            <div>
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 text-center md:text-left">Refund Trend</h4>
                <div class="h-64 relative"><canvas id="refundTrendChart"></canvas></div>
            </div>
        </div>

        {{-- Table --}}
        <div class="mt-6">
            <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Recent Refunds</h4>
            <div class="overflow-x-auto border rounded-lg">
                <table class="w-full text-xs text-left whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-600 border-b">
                        <tr>
                            <th class="p-3 font-semibold">ID</th>
                            <th class="p-3 font-semibold">Room</th>
                            <th class="p-3 font-semibold">Date</th>
                            <th class="p-3 text-right font-semibold">Amount</th>
                            <th class="p-3 text-center font-semibold">Type</th>
                            <th class="p-3 font-semibold">Reason</th>
                        </tr>
                    </thead>
                    <tbody id="refundTableBody" class="divide-y divide-gray-100 bg-white">
                        {{-- Data injected via JS --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- History Chart --}}
    <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <h3 class="text-gray-800 font-bold text-sm mb-4">Historical Comparison</h3>
        <div class="chart-box"><canvas id="comparisonChart"></canvas></div>
    </div>
@endsection

@section('content_js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const charts = {};

        $(document).ready(function () {
            initFilters();
            loadData();
            $('#periodType').change(toggleFilters);
            $('#btnApply').click(loadData);
        });

        function initFilters() {
            const d = new Date();
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');

            const ySel = $('#yearVal');
            for (let i = y; i >= y - 5; i--) ySel.append(new Option(i, i));

            $('#dateVal').val(`${y}-${m}-${day}`);
            $('#monthVal').val(`${y}-${m}`);
            toggleFilters();
        }

        function toggleFilters() {
            const type = $('#periodType').val();
            // Using jQuery addClass/removeClass with tailwind 'hidden'
            $('#dailyInput, #monthlyInput, #yearlyInput').addClass('hidden');

            if (type === 'daily') $('#dailyInput').removeClass('hidden');
            if (type === 'monthly') $('#monthlyInput').removeClass('hidden');
            if (type === 'yearly') $('#yearlyInput').removeClass('hidden');

            updateLinks();
        }

        function getParams() {
            const type = $('#periodType').val();
            let val = '';
            if (type === 'daily') val = $('#dateVal').val();
            if (type === 'monthly') val = $('#monthVal').val();
            if (type === 'yearly') val = $('#yearVal').val();
            return { period: type, filter_value: val, category: $('#categorySelect').val() };
        }

        function updateLinks() {
            const q = $.param(getParams());
            $('#lnkPdf').attr('href', `{{ route('admin.earnings.export.pdf') }}?${q}`);
        }

        async function loadData() {
            const p = getParams();
            if (!p.filter_value) return alert('Select date');

            const btn = $('#btnApply');
            const originalText = btn.text();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            updateLinks();

            try {
                // 1. Earnings & Main Stats
                const d1 = await $.get("{{ route('admin.earnings.data') }}", p);
                updateStats(d1.stats);
                renderBarChart('earningsChart', d1.labels, d1.earnings, 'Revenue');

                // 2. Category
                const d2 = await $.get("{{ route('admin.earnings.category') }}", p);
                renderPieChart('categoryChart', d2.categoryEarnings);

                // 3. Comparison
                const d3 = await $.get("{{ route('admin.earnings.comparison') }}", p);
                renderLineChart('comparisonChart', d3.labels, d3.data);

                // 4. Cancellations
                const d4 = await $.get("{{ route('admin.earnings.cancellations') }}", p);
                updateCancelStats(d4);

            } catch (e) {
                console.error(e);
                alert('Error loading data');
            } finally {
                btn.prop('disabled', false).text(originalText);
            }
        }

        function updateStats(s) {
            // 1. Update standard stats
            $('#statEarnings').text('₱' + Number(s.totalEarnings).toLocaleString());
            $('#statBookings').text(s.totalBookings);
            $('#statOccupancy').text(s.occupancyRate + '%');

            // 2. Logic to Hide/Show Best Category Card
            const card = $('#cardBestCategory');
            const catText = $('#statBestCategory');

            // We check if bookings exist AND if a category name was returned
            if (s.totalBookings > 0 && s.topCategory) {
                // Data exists: Remove hidden class and update text
                card.removeClass('hidden');
                catText.text(s.topCategory);
                catText.attr('title', s.topCategory);
            } else {
                // No data or 0 Bookings: Add hidden class to make it disappear
                card.addClass('hidden');
                catText.text('-'); // Reset text just in case
            }
        }

        function updateCancelStats(d) {
            $('#statCancelled').text(d.stats.cancelledBookings);
            $('#statRefunds').text('₱' + Number(d.stats.totalRefunds).toLocaleString());
            $('#statFullRef').text(d.stats.fullRefunds);
            $('#statPartRef').text(d.stats.partialRefunds);

            // Reasons Chart
            const reasonsData = d.cancellationReasons || {};
            // Always ensure canvas is clean before drawing
            if (charts['reasonsChart']) {
                charts['reasonsChart'].destroy();
                charts['reasonsChart'] = null; // explicit null
            }

            if (Object.keys(reasonsData).length > 0) {
                renderPieChart('reasonsChart', reasonsData, true); // true = minimal legend
            }

            // Trend Chart
            if (charts.refundTrend) charts.refundTrend.destroy();
            charts.refundTrend = new Chart(document.getElementById('refundTrendChart'), {
                type: 'line',
                data: {
                    labels: d.refundTrends.labels,
                    datasets: [{
                        label: 'Refund Amount',
                        data: d.refundTrends.data,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.3, fill: true, pointRadius: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { ticks: { callback: v => '₱' + v.toLocaleString(), font: { size: 10 } } },
                        x: { ticks: { font: { size: 10 } } }
                    }
                }
            });

            // Table
            const tb = $('#refundTableBody');
            tb.empty();
            if (d.recentRefunds.length === 0) {
                tb.append('<tr><td colspan="6" class="p-6 text-center text-gray-400 text-sm">No refund records found</td></tr>');
            } else {
                d.recentRefunds.forEach(r => {
                    const badgeColor = r.type === 'Full' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';
                    tb.append(`
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="p-3 font-mono text-gray-500">${r.booking_id}</td>
                                                    <td class="p-3 font-medium text-gray-800">${r.room_name}</td>
                                                    <td class="p-3 text-gray-500">${r.refund_date}</td>
                                                    <td class="p-3 text-right font-bold text-red-600">₱${Number(r.amount).toLocaleString()}</td>
                                                    <td class="p-3 text-center"><span class="px-2 py-1 rounded text-[10px] font-bold uppercase ${badgeColor}">${r.type}</span></td>
                                                    <td class="p-3 text-gray-500 truncate max-w-[150px]" title="${r.reason}">${r.reason}</td>
                                                </tr>
                                            `);
                });
            }
        }

        function renderBarChart(id, lbls, data, label) {
            if (charts[id]) charts[id].destroy();
            charts[id] = new Chart(document.getElementById(id), {
                type: 'bar',
                data: { labels: lbls, datasets: [{ label: label, data: data, backgroundColor: '#3b82f6', borderRadius: 4, barPercentage: 0.6 }] },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { x: { ticks: { autoSkip: true, maxRotation: 0 } } } // prevents x-axis crowding
                }
            });
        }

        function renderPieChart(id, dataObj, minimal = false) {
            if (charts[id]) charts[id].destroy();
            charts[id] = new Chart(document.getElementById(id), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(dataObj),
                    datasets: [{
                        data: Object.values(dataObj),
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#6366f1'],
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: minimal ? 'bottom' : 'right',
                            labels: { boxWidth: 10, font: { size: 11 } },
                        }
                    }
                }
            });
        }

        function renderLineChart(id, lbls, datasetsRaw) {
            if (charts[id]) charts[id].destroy();
            const ds = Object.keys(datasetsRaw).map((k, i) => ({
                label: k, data: datasetsRaw[k],
                borderColor: i === 0 ? '#3b82f6' : '#9ca3af',
                borderDash: i > 0 ? [5, 5] : [],
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 4
            }));
            charts[id] = new Chart(document.getElementById(id), {
                type: 'line',
                data: { labels: lbls, datasets: ds },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    interaction: { mode: 'index', intersect: false }
                }
            });
        }
    </script>
@endsection