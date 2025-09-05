@extends('layouts.admin')
@php
$active = 'monitoring';
@endphp
@section('title', 'Room Monitoring')

@section('content')
<div class="container mx-auto px-6 py-8">
      <h2 class="text-2xl font-semibold mb-6">Room Monitoring (Today)</h2>

      <!-- Room grid -->
      <div id="room-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
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
                  div.className = `p-6 rounded-lg shadow-md text-center text-white ${isOccupied ? 'bg-red-500' : 'bg-green-500'}`;
                  div.innerHTML = `
                        <h3 class="text-lg font-bold">${facility.name}</h3>
                        <p class="text-sm italic">${facility.category ?? 'No Category'}</p>
                        <p class="mt-2">${isOccupied ? 'Occupied' : 'Available'}</p>
                        ${isOccupied ? `<p class="text-sm mt-1">(Until ${unavailable[0].checkout_date})</p>` : ""}
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