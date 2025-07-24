@extends('layouts.admins')
@section('sidebar')
    <x-sidebars active="dashboard" />
@endsection

@section('content')
<style>
    .room-selection {
        transition: all 0.3s ease;
    }
    .room-selection:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .room-selection.selected {
        border-color: #1E40AF;
        background-color: #EFF6FF;
    }
    .breakfast-option {
        transition: all 0.2s ease;
    }
    .breakfast-option:hover {
        background-color: #F3F4F6;
    }
    .breakfast-option.selected {
        background-color: #DBEAFE;
        border-color: #1E40AF;
    }
</style>

<!-- Top Navigation -->
<div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
    <div class="flex items-center">
        <button class="md:hidden text-gray-500 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="ml-4 text-xl font-semibold text-gray-800">Dashboard</h1>
    </div>
    <div class="flex items-center">
        <div class="relative">
            <button class="flex items-center p-1 text-gray-400 rounded-full hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </button>
            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
        </div>
        <div class="ml-4">
            <div class="relative">
                <button class="flex items-center focus:outline-none">
                    <img class="w-8 h-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Admin User">
                    <span class="ml-2 text-sm font-medium text-gray-700">Admin</span>
                    <svg class="ml-1 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Content -->
<div class="flex-1 overflow-auto p-4">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Today's Bookings</p>
                    <p class="text-xl font-semibold text-gray-900">12</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Occupancy Rate</p>
                    <p class="text-xl font-semibold text-gray-900">78%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Today's Revenue</p>
                    <p class="text-xl font-semibold text-gray-900">$3,450</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Inquiries</p>
                    <p class="text-xl font-semibold text-gray-900">5</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings and Occupancy Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Recent Bookings -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Bookings</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rooms</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BK-20230618-001</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-07-15 - 2023-07-20</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 rooms</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BK-20230618-002</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jane Smith</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-07-18 - 2023-07-22</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1 room</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BK-20230617-001</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Robert Johnson</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-07-20 - 2023-07-25</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3 rooms</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BK-20230616-001</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Emily Davis</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-07-12 - 2023-07-15</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1 room</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Checked Out</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BK-20230615-001</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Michael Wilson</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-07-10 - 2023-07-14</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 rooms</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Occupancy Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Occupancy</h3>
            <div class="h-64 flex items-center justify-center">
                <div class="w-full h-48 bg-gray-100 rounded flex items-end">
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:65%"></div>
                        <span class="text-xs text-gray-500 mt-1">1</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:72%"></div>
                        <span class="text-xs text-gray-500 mt-1">2</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:68%"></div>
                        <span class="text-xs text-gray-500 mt-1">3</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:75%"></div>
                        <span class="text-xs text-gray-500 mt-1">4</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:80%"></div>
                        <span class="text-xs text-gray-500 mt-1">5</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:82%"></div>
                        <span class="text-xs text-gray-500 mt-1">6</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:78%"></div>
                        <span class="text-xs text-gray-500 mt-1">7</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:85%"></div>
                        <span class="text-xs text-gray-500 mt-1">8</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:88%"></div>
                        <span class="text-xs text-gray-500 mt-1">9</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:90%"></div>
                        <span class="text-xs text-gray-500 mt-1">10</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:92%"></div>
                        <span class="text-xs text-gray-500 mt-1">11</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:85%"></div>
                        <span class="text-xs text-gray-500 mt-1">12</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:80%"></div>
                        <span class="text-xs text-gray-500 mt-1">13</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:82%"></div>
                        <span class="text-xs text-gray-500 mt-1">14</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-6 bg-blue-500 rounded-t" style="height:78%"></div>
                        <span class="text-xs text-gray-500 mt-1">15</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center text-sm text-gray-500">
                Current month occupancy trend
            </div>
        </div>
    </div>

    <!-- Upcoming Arrivals and Departures -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Upcoming Arrivals -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-blue-50">
                <h3 class="text-lg font-medium text-gray-900">Today's Arrivals</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Sarah Miller</p>
                            <p class="text-sm text-gray-500">Room: 205 (Deluxe)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">2:00 PM</p>
                            <p class="text-xs text-gray-500">Confirmed</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">David Brown</p>
                            <p class="text-sm text-gray-500">Room: 312 (Standard)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">3:30 PM</p>
                            <p class="text-xs text-gray-500">Confirmed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Departures -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-blue-50">
                <h3 class="text-lg font-medium text-gray-900">Today's Departures</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Lisa Taylor</p>
                            <p class="text-sm text-gray-500">Room: 107 (Standard)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">11:00 AM</p>
                            <p class="text-xs text-gray-500">Checked Out</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">James Wilson</p>
                            <p class="text-sm text-gray-500">Room: 215 (Deluxe)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">12:00 PM</p>
                            <p class="text-xs text-gray-500">Pending Checkout</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
