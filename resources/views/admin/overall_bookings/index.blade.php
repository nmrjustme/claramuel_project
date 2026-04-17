@extends('layouts.admin')
@section('title', 'Payments')

@section('sidebar')
    <x-sidebar active="overall_bookings" />
@endsection

@section('content')

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary: {
            100: '#fee2e2',
            200: '#fecaca',
            300: '#fca5a5',
            400: '#f87171',
            500: '#ef4444',
            600: '#dc2626',
            700: '#b91c1c',
            800: '#991b1b',
            900: '#7f1d1d',
          }
        }
      }
    }
  }
</script>
<style>
  .calendar-month {
    margin-bottom: 2rem;
  }

  .calendar-month-title {
    background-color: #991b1b;
    color: white;
    padding: 0.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
  }
</style>
<div class="container mx-auto px-4 py-8">
  <div class="text-2xl font-bold text-primary mb-8 dark:text-white">All Bookings Management</div>


  <!-- Filters -->
  <div class="bg-white rounded-lg shadow-md p-6 mb-8 sticky top-0 z-10">
    <div class="flex flex-wrap gap-6 items-end">
      <!-- Year Selector -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
        <select id="year-select"
          class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
          <option>2023</option>
          <option>2024</option>
          <option selected>2025</option>
          <option>2026</option>
        </select>
      </div>

      <!-- Booking Type Filter -->
      <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type</label>
        <select id="type-select"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
          <option value="all">All Types</option>
          <option value="Pool">Pool</option>
          <option value="Park">Park</option>
          <option value="Osaka">Osaka</option>
          <option value="Private Villa">Private Villa</option>
          <option value="Japanese-Inspired">Japanese-Inspired</option>
          <option value="Yokohama">Yokohama</option>
        </select>
      </div>

      <button id="apply-filters"
        class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition-colors">
        Apply Filters
      </button>
    </div>
  </div>

  <!-- Calendar Container -->
  <div id="calendar-container">
    <!-- Calendar months will be generated here -->
  </div>
</div>

<!-- Booking Modal -->
<div id="booking-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] overflow-y-auto">
    <div class="flex justify-between items-center border-b p-4 bg-primary-700 text-white rounded-t-lg">
      <h3 id="modal-date" class="text-xl font-semibold"></h3>
      <button id="close-modal" class="text-white hover:text-primary-200">
        <i class="fas fa-times"></i> × <!-- Added × symbol here -->
      </button>
    </div>
    <div id="modal-content" class="p-4">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody id="booking-records" class="bg-white divide-y divide-gray-200">
            <!-- Booking records will be inserted here -->
          </tbody>
        </table>
      </div>
      <p id="no-bookings" class="text-center text-gray-500 py-4 hidden">No bookings for this date.</p>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Current date
    const currentDate = new Date();
    let currentYear = currentDate.getFullYear();
    let selectedType = 'all';
    let bookingsData = {};

    // Set initial values in selects
    document.getElementById('year-select').value = currentYear;

    // Generate calendar
    fetchBookingsAndGenerateCalendar(currentYear);

    // Apply filters button
    document.getElementById('apply-filters').addEventListener('click', function () {
        currentYear = parseInt(document.getElementById('year-select').value);
        selectedType = document.getElementById('type-select').value;
        fetchBookingsAndGenerateCalendar(currentYear);
    });

    // Close modal button
    document.getElementById('close-modal').addEventListener('click', function () {
        document.getElementById('booking-modal').classList.add('hidden');
    });

    // Function to fetch bookings and generate calendar
    function fetchBookingsAndGenerateCalendar(year) {
        fetch(`/admin/bookings/get-bookings?year=${year}`)
            .then(response => response.json())
            .then(data => {
                bookingsData = data;
                generateYearCalendar(year);
            })
            .catch(error => {
                console.error('Error fetching bookings:', error);
                bookingsData = {};
                generateYearCalendar(year);
            });
    }

    // Generate calendar for entire year
    function generateYearCalendar(year) {
        const calendarContainer = document.getElementById('calendar-container');
        calendarContainer.innerHTML = '';

        for (let month = 0; month < 12; month++) {
            const monthContainer = document.createElement('div');
            monthContainer.className = 'calendar-month bg-white rounded-lg shadow-md overflow-hidden mb-8';

            // Month title
            const monthTitle = document.createElement('div');
            monthTitle.className = 'calendar-month-title text-center font-bold text-lg';
            monthTitle.textContent = new Date(year, month, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            monthContainer.appendChild(monthTitle);

            // Calendar grid container
            const calendarGrid = document.createElement('div');

            // Calendar Header
            const calendarHeader = document.createElement('div');
            calendarHeader.className = 'grid grid-cols-7 bg-primary-700 text-white text-center font-semibold py-2';
            ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
                const dayElement = document.createElement('div');
                dayElement.textContent = day;
                calendarHeader.appendChild(dayElement);
            });
            calendarGrid.appendChild(calendarHeader);

            // Calendar Body
            const calendarBody = document.createElement('div');
            calendarBody.className = 'grid grid-cols-7 gap-1 p-2';
            calendarBody.id = `calendar-body-${month}`;
            calendarGrid.appendChild(calendarBody);

            monthContainer.appendChild(calendarGrid);
            calendarContainer.appendChild(monthContainer);

            // Generate month calendar
            generateMonthCalendar(month, year);
        }
    }

    // Generate calendar for a single month
    function generateMonthCalendar(month, year) {
        const calendarBody = document.getElementById(`calendar-body-${month}`);
        calendarBody.innerHTML = '';

        // Get first day of month and total days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Get days from previous month
        const prevMonthDays = new Date(year, month, 0).getDate();

        // Create date cells
        let date = 1;
        let nextMonthDate = 1;

        for (let i = 0; i < 6; i++) {
            // Stop if we've rendered all days
            if (date > daysInMonth && i > 0) break;

            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('div');
                cell.className = 'h-24 p-1 border border-gray-200 overflow-hidden';

                if (i === 0 && j < firstDay) {
                    // Previous month
                    const prevDate = prevMonthDays - (firstDay - j - 1);
                    cell.innerHTML = `<div class="text-right text-gray-400">${prevDate}</div>`;
                    cell.classList.add('bg-gray-50');
                } else if (date > daysInMonth) {
                    // Next month
                    cell.innerHTML = `<div class="text-right text-gray-400">${nextMonthDate}</div>`;
                    cell.classList.add('bg-gray-50');
                    nextMonthDate++;
                } else {
                    // Current month
                    const cellDate = new Date(year, month, date);
                    const today = new Date();
                    const isToday = date === today.getDate() &&
                        month === today.getMonth() &&
                        year === today.getFullYear();

                    // Create cell content
                    const cellContent = document.createElement('div');
                    cellContent.className = 'h-full flex flex-col';

                    // Date number
                    const dateNumber = document.createElement('div');
                    dateNumber.className = 'text-right font-medium';
                    if (isToday) {
                        dateNumber.className += ' bg-primary-600 text-white rounded-full w-6 h-6 flex items-center justify-center ml-auto';
                    }
                    dateNumber.textContent = date;
                    cellContent.appendChild(dateNumber);

                    // Add click event
                    cellContent.addEventListener('click', function () {
                        openModal(cellDate);
                    });

                    // Add hover effect
                    cellContent.classList.add('hover:bg-primary-50', 'cursor-pointer', 'p-1', 'rounded');

                    // Get bookings for this date
                    const dateString = cellDate.toISOString().split('T')[0];
                    const dateBookings = bookingsData[dateString] || null;

                    // Check if bookings exist
                    if (dateBookings === null) {
                        const noRecordIndicator = document.createElement('div');
                        noRecordIndicator.className = 'text-xs text-gray-500 mt-1';
                        noRecordIndicator.textContent = 'no record';
                        cellContent.appendChild(noRecordIndicator);
                    } else {
                        // Filter by selected type
                        const filteredBookings = selectedType === 'all'
                            ? dateBookings
                            : dateBookings.filter(booking => booking.type === selectedType);

                        // Add booking indicators if they exist
                        if (filteredBookings && filteredBookings.length > 0) {
                            filteredBookings.slice(0, 3).forEach(booking => {
                                const bookingIndicator = document.createElement('div');
                                bookingIndicator.className = 'text-xs truncate px-1 py-0.5 mt-1 rounded bg-primary-100 text-primary-800';
                                bookingIndicator.textContent = `${booking.type || 'no record'} - ${(booking.client || 'no record').split(' ')[0]}`;
                                cellContent.appendChild(bookingIndicator);
                            });

                            // Show "+ more" if there are additional bookings
                            if (filteredBookings.length > 3) {
                                const moreIndicator = document.createElement('div');
                                moreIndicator.className = 'text-xs text-primary-600 mt-1';
                                moreIndicator.textContent = `+${filteredBookings.length - 3} more`;
                                cellContent.appendChild(moreIndicator);
                            }
                        } else {
                            const noRecordIndicator = document.createElement('div');
                            noRecordIndicator.className = 'text-xs text-gray-500 mt-1';
                            noRecordIndicator.textContent = 'no record';
                            cellContent.appendChild(noRecordIndicator);
                        }
                    }

                    cell.appendChild(cellContent);
                    date++;
                }

                calendarBody.appendChild(cell);
            }
        }
    }

    // Open modal with bookings for selected date
    function openModal(date) {
        const modal = document.getElementById('booking-modal');
        const modalDate = document.getElementById('modal-date');
        const bookingRecords = document.getElementById('booking-records');
        const noBookings = document.getElementById('no-bookings');

        // Format date for display
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        modalDate.textContent = date.toLocaleDateString('en-US', options);

        // Get bookings for this date
        const dateString = date.toISOString().split('T')[0];
        const dateBookings = bookingsData[dateString] || null;

        // Clear previous records
        bookingRecords.innerHTML = '';
        noBookings.classList.add('hidden');

        if (dateBookings === null) {
            noBookings.textContent = 'no record';
            noBookings.classList.remove('hidden');
        } else {
            // Filter by selected type
            const filteredBookings = selectedType === 'all'
                ? dateBookings
                : dateBookings.filter(booking => booking.type === selectedType);

            if (filteredBookings.length === 0) {
                noBookings.textContent = 'no record';
                noBookings.classList.remove('hidden');
            } else {
                filteredBookings.forEach(booking => {
                    const row = document.createElement('tr');

                    // Status color coding
                    let statusColor = 'bg-blue-100 text-blue-800';
                    if (booking.status === 'Confirmed') statusColor = 'bg-green-100 text-green-800';
                    if (booking.status === 'Cancelled') statusColor = 'bg-red-100 text-red-800';
                    if (booking.status === 'Completed') statusColor = 'bg-purple-100 text-purple-800';

                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${booking.client || 'no record'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${booking.type || 'no record'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${booking.check_in || 'no record'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${booking.check_out || 'no record'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${booking.contact || 'no record'}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">
                                ${booking.status || 'no record'}
                            </span>
                        </td>
                    `;
                    bookingRecords.appendChild(row);
                });
            }
        }

        // Show modal
        modal.classList.remove('hidden');
    }
});
</script>
@endsection