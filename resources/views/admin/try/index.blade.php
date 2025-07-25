@extends('layouts.admin')
@section('title', 'Booking Requests')

@php
    $active = 'log';
@endphp

@section('content')
<div class="min-h-screen p-6">
    <h1 class="text-3xl font-bold mb-4 text-white">Booking Requests</h1>

    <table class="min-w-full bg-white rounded-lg overflow-hidden shadow">
        <thead class="bg-gray-200">
            <tr>
                <th class="text-left p-3">ID</th>
                <th class="text-left p-3">Guest</th>
                <th class="text-left p-3">Status</th>
                <th class="text-left p-3">Action</th>
            </tr>
        </thead>
        <tbody id="booking-table-body">
            @foreach($bookings as $booking)
            <tr class="border-b">
                <td class="p-3">{{ $booking->id }}</td>
                <td class="p-3">{{ $booking->user->firstname }} {{ $booking->user->lastname }}</td>
                <td class="p-3">{{ ucfirst($booking->status) }}</td>
                <td class="p-3">
                    <button class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">View Request</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('content_js')
<!-- Include Pusher and Echo -->
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
    // Initialize Echo with CSRF token
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config('broadcasting.connections.pusher.key') }}',
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        forceTLS: true,
        auth: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }
    });

    // Listen to the channel
    Echo.channel('booking-channel')
        .listen('.NewBookingRequest', (e) => {  // Note the dot prefix for private channels
            const booking = e.booking;

            // Basic Validation
            if (!booking || !booking.id || !booking.status || !booking.user) {
                console.warn('❌ Invalid booking data received:', e);
                return;
            }

            // Proceed if valid
            const row = `
                <tr class="border-b">
                    <td class="p-3">${booking.id}</td>
                    <td class="p-3">${booking.user.firstname || ''} ${booking.user.lastname || ''}</td>
                    <td class="p-3">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</td>
                    <td class="p-3">
                        <button class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">View Request</button>
                    </td>
                </tr>
            `;

            document.getElementById('booking-table-body').insertAdjacentHTML('afterbegin', row);
        });
</script>
@endsection