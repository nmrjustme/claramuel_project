@extends('layouts.admin')

@section('title', 'Bookings')

@section('content_css')
<!-- Optional: add custom CSS for cards if needed -->
<style>
      .booking-card {
            transition: transform 0.2s;
      }

      .booking-card:hover {
            transform: translateY(-4px);
      }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
      <h1 class="text-2xl font-bold mb-6">Bookings</h1>

      <div id="booking-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Booking cards injected by AJAX -->
            <div class="col-span-full text-center text-gray-500" id="loading-message">Loading bookings...</div>
      </div>
</div>
@endsection

@section('content_js')
<script>
      document.addEventListener("DOMContentLoaded", function() {
            fetchBookings();
            });

            function fetchBookings() {
            fetch('{{ route("guest.details.list") }}', {
                  headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                  }
            })
            .then(response => response.json())
            .then(data => {
                  const container = document.getElementById('booking-list');
                  container.innerHTML = ''; // Clear loading message

                  if(data.data.length === 0) {
                        container.innerHTML = '<p class="col-span-full text-center text-gray-500">No bookings found.</p>';
                        return;
                  }
                  
                  data.data.forEach(booking => {
                        const details = booking.details.map(d => `
                        <p class="text-sm text-gray-600">Check-in: ${d.checkin ?? 'N/A'}</p>
                        <p class="text-sm text-gray-600">Check-out: ${d.checkout ?? 'N/A'}</p>
                        `).join('');

                        container.innerHTML += `
                        <div class="booking-card p-4 rounded-lg shadow-md bg-white border hover:shadow-lg">
                              <h3 class="font-bold text-lg mb-2">${booking.user.firstname} ${booking.user.lastname}</h3>
                              <p class="text-sm text-gray-700">Phone: ${booking.user.phone || 'N/A'}</p>
                              <p class="text-sm text-gray-700 mb-2">Email: ${booking.user.email || 'N/A'}</p>
                              ${details}
                        </div>
                        `;
                  });
            })
            .catch(err => {
                  console.error(err);
                  const container = document.getElementById('booking-list');
                  container.innerHTML = '<p class="col-span-full text-center text-red-500">Failed to load bookings.</p>';
            });
            }
</script>
@endsection