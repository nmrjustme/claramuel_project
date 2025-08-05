<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Live Bookings</h1>
        <div class="relative">
            <button id="notificationsButton" class="p-2 rounded-full bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($notificationCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    {{ $notificationCount }}
                </span>
                @endif
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody id="booking-list" class="bg-white divide-y divide-gray-200">
                    @foreach($bookings as $booking)
                    <tr id="booking-{{ $booking->id }}" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $booking->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                    {{ strtoupper(substr($booking->user->firstname, 0, 1)) }}{{ strtoupper(substr($booking->user->lastname, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->user->firstname }} {{ $booking->user->lastname }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $booking->created_at->format('M d, Y') }}</div>
                            <div class="text-gray-400">{{ $booking->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- New Booking Notification Toast -->
    <div id="newBookingToast" class="hidden fixed bottom-4 right-4 w-80 bg-white rounded-lg shadow-lg border-l-4 border-indigo-500 z-50">
        <!-- Toast content remains the same -->
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Close toast function
            window.closeToast = function() {
                document.getElementById('newBookingToast').classList.add('hidden');
            };
            
            // Listen for Livewire browser event
            window.addEventListener('new-booking-notification', function(e) {
                const booking = e.detail.booking;
                
                // Update UI
                document.getElementById('toastMessage').textContent = 
                    `${booking.firstname} ${booking.lastname} made a new booking`;
                
                const now = new Date();
                document.getElementById('toastTime').textContent = 
                    now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                
                document.getElementById('newBookingToast').classList.remove('hidden');
                setTimeout(closeToast, 5000);
            });
        });
    </script>
    @endpush
</div>