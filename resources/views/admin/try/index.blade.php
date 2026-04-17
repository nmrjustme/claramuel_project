@extends('layouts.admin')
@section('title', 'Booking Requests')

@php $active = 'log'; @endphp

@section('content')
<table id="booking-list" class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach($bookings as $booking)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->user->firstname }} {{ $booking->user->lastname }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('content_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    Echo.channel('booking-log-channel')
        .listen('.new-booking-log', (e) => {
            const booking = e.booking;
            
            const row = `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.user.firstname} ${booking.user.lastname}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.status}</td>
                </tr>
            `;

            document.querySelector('#booking-list tbody').insertAdjacentHTML('afterbegin', row);
        });
});
</script>
@endsection
