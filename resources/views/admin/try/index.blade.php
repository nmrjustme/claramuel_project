<!-- resources/views/admin/booking-requests/index.blade.php -->

@extends('layouts.admin')
@section('title', 'Booking Requests')

@php
    $active = 'log';
@endphp

@section('content')
<div class="min-h-screen p-6">
    <h1 class="text-3xl font-bold mb-4 text-white">Booking Requests</h1>

    <table class="min-w-full bg-white rounded shadow overflow-hidden">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Guest</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Action</th>
            </tr>
        </thead>
        <tbody id="booking-requests-body">
            @foreach ($bookings as $booking)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $booking->id }}</td>
                    <td class="px-4 py-2">{{ $booking->user->firstname }} {{ $booking->user->lastname }}</td>
                    <td class="px-4 py-2">{{ ucfirst($booking->status) }}</td>
                    <td class="px-4 py-2">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">View Request</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    // Enable Pusher logging (for development only)
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        encrypted: true
    });

    var channel = pusher.subscribe('booking-channel');
    channel.bind('new-booking', function(data) {
        console.log("Received booking:", data);
        const tbody = document.getElementById('booking-requests-body');
        
        const row = `
            <tr class="border-b bg-green-100">
                <td class="px-4 py-2">${data.id}</td>
                <td class="px-4 py-2">${data.guest}</td>
                <td class="px-4 py-2">${data.status}</td>
                <td class="px-4 py-2">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">View Request</button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('afterbegin', row);
    });
</script>
@endsection
