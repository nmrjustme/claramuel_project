@extends('layouts.admin')
@section('title', 'Incoming Check-ins')

@php
$active = 'arrivals';
@endphp

@section('content')
<div class="min-h-screen px-4 py-6 md:px-6 lg:px-8">
    <div class="flex flex-col justify-between gap-4 mb-8 sm:flex-row sm:items-center">
        <!-- Left: Date + Title -->
        <div>
            <!-- Current Date -->
            <p class="text-sm text-gray-500 md:text-base">
                {{ now()->format('l, F j, Y') }}
            </p>

            <!-- Page Title -->
            <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Incoming Check-ins</h1>
            <p class="text-sm text-gray-600 md:text-base">Latest guest arrivals and reservations</p>
        </div>

        <!-- Right: Refresh Button -->
        <div class="flex items-center">
            <button id="refreshBtn" class="flex items-center text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                        clip-rule="evenodd" />
                </svg>
                Refresh
            </button>
        </div>
    </div>



    <!-- Two-column layout for Last Day and Tomorrow -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
        <!-- Today -->
        <div>
            <div class="flex items-center mb-4">
                <div class="w-1 h-6 mr-2 bg-green-500 rounded-full"></div>
                <h2 class="text-xl font-semibold text-gray-800 md:text-2xl">Today</h2>
                <span id="todayCount"
                    class="flex items-center justify-center w-6 h-6 ml-2 text-xs font-medium text-white bg-green-500 rounded-full">0</span>
            </div>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3" id="todayCheckins">
                <div class="flex justify-center items-center h-64 col-span-full">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto"></div>
                        <p class="mt-2 text-gray-500">Loading today's check-ins...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tomorrow onwards -->
        <div>
            <div class="flex items-center mb-4">
                <div class="w-1 h-6 mr-2 bg-blue-500 rounded-full"></div>
                <h2 class="text-xl font-semibold text-gray-800 md:text-2xl">Tomorrow Onwards</h2>
                <span id="futureCount"
                    class="flex items-center justify-center w-6 h-6 ml-2 text-xs font-medium text-white bg-blue-500 rounded-full">0</span>
            </div>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-1" id="futureCheckins">
                <div class="flex justify-center items-center h-64 col-span-full">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-2 text-gray-500">Loading upcoming check-ins...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@section('content_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const todayContainer = document.getElementById('todayCheckins');
        const futureContainer = document.getElementById('futureCheckins');
        const refreshBtn = document.getElementById('refreshBtn');
        const todayCount = document.getElementById('todayCount');
        const futureCount = document.getElementById('futureCount');
        
        function fetchCheckins() {
            todayContainer.innerHTML = loader("Today's check-ins...");
            futureContainer.innerHTML = loader("Upcoming check-ins...");
            
            fetch('{{ route("checkins.data") }}')
                .then(res => res.json())
                .then(data => {
                    renderCheckins(data);
                })
                .catch(() => {
                    todayContainer.innerHTML = errorMsg("Error loading today's check-ins");
                    futureContainer.innerHTML = errorMsg("Error loading future check-ins");
                });
        }
        
        function loader(msg) {
            return `
            <div class="flex justify-center items-center h-64 col-span-full">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">${msg}</p>
                </div>
            </div>`;
        }

        function errorMsg(msg) {
            return `<div class="flex flex-col items-center justify-center py-10 text-gray-500 col-span-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg">${msg}</p>
            </div>`;
        }

        function renderCheckins(data) {
            // Update counts
            todayCount.textContent = data.today.length;
            futureCount.textContent = data.future.length;
            
            // Today
            let todayHtml = "";
            if (data.today.length > 0) {
                data.today.forEach(checkin => {
                    todayHtml += buildCard(checkin);
                });
            } else {
                todayHtml = `
                <div class="col-span-full">
                    <div class="p-6 text-center bg-gray-50 rounded-lg border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-3 text-lg font-medium text-gray-900">No check-ins today</h3>
                        <p class="mt-1 text-gray-500">No guests are scheduled to check in today.</p>
                    </div>
                </div>`;
            }
            todayContainer.innerHTML = todayHtml;

            // Future
            let futureHtml = "";
            if (data.future.length > 0) {
                data.future.forEach(checkin => {
                    futureHtml += buildCard(checkin, false, true);
                });
            } else {
                futureHtml = `
                <div class="col-span-full">
                    <div class="p-6 text-center bg-gray-50 rounded-lg border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-3 text-lg font-medium text-gray-900">No upcoming check-ins</h3>
                        <p class="mt-1 text-gray-500">No guests are scheduled to check in tomorrow or later.</p>
                    </div>
                </div>`;
            }
            futureContainer.innerHTML = futureHtml;
        }

        function buildCard(checkin, isNoShow = false, isFuture = false) {
            const checkinDate = new Date(checkin.checkin_date);
            
            const formattedDate = checkinDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            const formattedTime = checkinDate.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            
            // Determine status and styling
            let statusText = '';
            let statusClass = '';
            let borderClass = '';
            
            if (isNoShow) {
                statusText = 'No Show';
                statusClass = 'bg-gray-100 text-gray-800';
                borderClass = 'border-l-gray-500';
            } else if (isFuture) {
                statusText = 'Upcoming';
                statusClass = 'bg-blue-100 text-blue-800';
                borderClass = 'border-l-blue-500';
            } else {
                statusText = 'Today';
                statusClass = 'bg-green-100 text-green-800';
                borderClass = 'border-l-green-500';
            }

            return `
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="border-l-4 ${borderClass} p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                ${statusText}
                            </span>
                            <span class="block mt-2 text-sm font-semibold text-gray-900">${checkin.reservation_code}</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-sm font-medium text-gray-900">${formattedDate}</span>
                            <span class="block text-sm text-gray-500">${formattedTime}</span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">${checkin.full_name}</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-gray-600 truncate">${checkin.email}</span>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm text-gray-600">${checkin.phone}</span>
                        </div>
                    </div>
                </div>
            </div>`;
        }

        fetchCheckins();
        refreshBtn.addEventListener('click', fetchCheckins);
        setInterval(fetchCheckins, 60000);
    });
</script>
@endsection