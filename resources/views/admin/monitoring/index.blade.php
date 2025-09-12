@extends('layouts.admin')
@php
$active = 'monitoring';
@endphp
@section('title', 'Room Monitoring')

@section('content')
<div class="container mx-auto px-6 py-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Room Monitoring (Today)</h2>
            <span class="mt-2 sm:mt-0 text-gray-600 font-medium">
                  {{ now()->format('F d, Y') }}
            </span>
      </div>

      <!-- Room grid -->
      <div id="room-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <!-- JS will render here -->
      </div>
</div>

<script>
      async function fetchRooms() {
            try {
                  const response = await fetch("{{ route('monitor.room.data') }}", {
                        headers: {
                              "X-Requested-With": "XMLHttpRequest",
                              "Content-Type": "application/json",
                              "Accept": "application/json",
                              "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                  });

                  if (!response.ok) throw new Error("Network error");

                  const data = await response.json();
                  renderRooms(data);
            } catch (err) {
                  console.error("Fetch error:", err);
            }
      }

      function renderRooms(data) {
            const grid = document.getElementById("room-grid");
            grid.innerHTML = ""; // clear old content

            data.facilities.forEach(facility => {
                  const unavailable = data.unavailableDates[facility.id] || [];
                  const today = data.today;

                  const isOccupied = unavailable.some(date =>
                        today >= date.checkin_date && today < date.checkout_date
                  );

                  const div = document.createElement("div");
                  div.className = `
                        p-3 rounded-lg shadow text-center text-white text-sm
                        ${isOccupied ? 'bg-red-500' : 'bg-green-500'}
                  `;
                  div.innerHTML = `
                        <h3 class="font-semibold">${facility.name}</h3>
                        <p class="text-xs italic text-gray-100">${facility.category ?? 'No Category'}</p>
                        <p class="mt-1 font-medium">${isOccupied ? 'Occupied' : 'Available'}</p>
                  `;

                  grid.appendChild(div);
            });
      }

      // Initial load
      fetchRooms();

      // Auto refresh every 5s
      setInterval(fetchRooms, 5000);
</script>
@endsection
