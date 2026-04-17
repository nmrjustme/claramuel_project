@extends('layouts.admin')
@section('title', 'Incoming Check-ins')

@php
$active = 'arrivals';
@endphp

@section('content')
<div class="min-h-screen px-4 py-4 md:px-6 lg:px-8">
    <div class="flex flex-col justify-between gap-3 mb-6 sm:flex-row sm:items-center">
        <!-- Left: Date + Title -->
        <div>
            <!-- Current Date -->
            <p class="text-xs text-gray-500 md:text-sm">
                {{ now()->format('l, F j, Y') }}
            </p>

            <!-- Page Title -->
            <h1 class="text-xl font-bold text-gray-900 md:text-2xl">Incoming Check-ins</h1>
            <p class="text-xs text-gray-600 md:text-sm">Latest guest arrivals and reservations</p>
        </div>

        <!-- Right: Refresh Button -->
        <div class="flex items-center mt-2 sm:mt-0">
            <button id="refreshBtn" class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                        clip-rule="evenodd" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Two-column layout for Today and Tomorrow -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        <!-- Today -->
        <div>
            <div class="flex items-center mb-3">
                <div class="w-1 h-5 mr-2 bg-green-500 rounded-full"></div>
                <h2 class="text-lg font-semibold text-gray-800 md:text-xl">Today</h2>
                <span id="todayCount"
                    class="flex items-center justify-center w-5 h-5 ml-2 text-xs font-medium text-white bg-green-500 rounded-full">0</span>
            </div>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3" id="todayCheckins">
                <div class="flex justify-center items-center h-40 col-span-full">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600 mx-auto"></div>
                        <p class="mt-2 text-xs text-gray-500">Loading today's check-ins...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tomorrow onwards -->
        <div>
            <div class="flex items-center mb-3">
                <div class="w-1 h-5 mr-2 bg-blue-500 rounded-full"></div>
                <h2 class="text-lg font-semibold text-gray-800 md:text-xl">Tomorrow Onwards</h2>
                <span id="futureCount"
                    class="flex items-center justify-center w-5 h-5 ml-2 text-xs font-medium text-white bg-blue-500 rounded-full">0</span>
            </div>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-1" id="futureCheckins">
                <div class="flex justify-center items-center h-40 col-span-full">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-2 text-xs text-gray-500">Loading upcoming check-ins...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content_js')
<script>
    // Move the function to global scope so it's accessible to the onclick handler
    function openInquiriesPageAndSearch(id) {
        // Store the ID to search for in sessionStorage
        sessionStorage.setItem('searchInquiryId', id);
        
        // Open the inquiries page
        window.location.href = '/admin/bookings';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const todayContainer = document.getElementById('todayCheckins');
        const futureContainer = document.getElementById('futureCheckins');
        const refreshBtn = document.getElementById('refreshBtn');
        const todayCount = document.getElementById('todayCount');
        const futureCount = document.getElementById('futureCount');
        
        function fetchCheckins() {
            todayContainer.innerHTML = loader("Today's check-ins...");
            futureContainer.innerHTML = loader("Upcoming check-ins...");
            
            fetch(`/next/check-in/list/data`)
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
            <div class="flex justify-center items-center h-40 col-span-full">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-gray-600 mx-auto"></div>
                    <p class="mt-2 text-xs text-gray-500">${msg}</p>
                </div>
            </div>`;
        }

        function errorMsg(msg) {
            return `<div class="flex flex-col items-center justify-center py-6 text-gray-500 col-span-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm">${msg}</p>
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
                    <div class="p-4 text-center bg-gray-50 rounded-lg border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-base font-medium text-gray-900">No check-ins today</h3>
                        <p class="mt-1 text-xs text-gray-500">No guests are scheduled to check in today.</p>
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
                    <div class="p-4 text-center bg-gray-50 rounded-lg border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-base font-medium text-gray-900">No upcoming check-ins</h3>
                        <p class="mt-1 text-xs text-gray-500">No guests are scheduled to check in tomorrow or later.</p>
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
            
            if (isFuture) {
                statusClass = 'bg-blue-100 text-blue-800';
                borderClass = 'border-l-blue-500';
            } else {
                statusClass = 'bg-green-100 text-green-800';
                borderClass = 'border-l-green-500';
            }

            return `
            <button onclick="openInquiriesPageAndSearch(${checkin.id})" class="w-full text-left">
                <div class="bg-white hover:bg-gray-50 cursor-pointer rounded-lg border border-gray-200 overflow-hidden transition-all duration-200">
                    <div class="border-l-4 ${borderClass} p-3">
                        
                        <!-- Top Row: Code + Date -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-900">${checkin.reservation_code}</span>
                            <div class="text-right leading-tight">
                                <span class="block text-xs font-medium text-gray-900">${formattedDate}</span>
                                <span class="block text-xs text-gray-500">${formattedTime}</span>
                            </div>
                        </div>
                        
                        <!-- Guest Name -->
                        <h3 class="text-sm font-semibold text-gray-900 truncate mb-2">${checkin.full_name}</h3>
                        
                        <!-- Contact Info -->
                        <div class="space-y-1">
                            <div class="flex items-center text-xs text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="truncate">${checkin.email}</span>
                            </div>
                            <div class="flex items-center text-xs text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="truncate">${checkin.phone}</span>
                            </div>
                        </div>
                    
                    </div>
                </div>
            </button>
            `;
        }

        fetchCheckins();
        refreshBtn.addEventListener('click', fetchCheckins);
        setInterval(fetchCheckins, 60000);
    });
</script>
@endsection