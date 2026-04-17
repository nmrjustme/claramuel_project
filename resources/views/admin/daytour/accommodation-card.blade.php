{{-- resources/views/admin/daytour/accommodation-card.blade.php --}}

@foreach($cottages as $cottage)
    @php
        $status = strtolower($cottage->display_status ?? 'available');

        $baseCardClasses = 'rounded-xl border shadow-sm transition-all duration-300 hover:shadow-lg flex flex-col justify-between overflow-hidden';

        $statusStyles = [
            'occupied' => [
                'card' => 'bg-red-500 border-red-600 text-white',
                'text' => 'text-white',
                'secondary' => 'text-white/80',
                'button' => 'bg-red-600 hover:bg-red-700 text-white',
                'details' => 'bg-red-600/10',
                'badge' => 'bg-red-600/20 text-red-200 border-red-400',
            ],
            'maintenance' => [
                'card' => 'bg-yellow-400 border-yellow-500 text-white',
                'text' => 'text-white',
                'secondary' => 'text-white/80',
                'button' => 'bg-yellow-500 hover:bg-yellow-600 text-white',
                'details' => 'bg-yellow-100/30',
                'badge' => 'bg-yellow-500/20 text-yellow-200 border-yellow-400',
            ],
            'cleaning' => [
                'card' => 'bg-blue-600 border-blue-700 text-white',
                'text' => 'text-white',
                'secondary' => 'text-white/80',
                'button' => 'bg-blue-700 hover:bg-blue-800 text-white',
                'details' => 'bg-blue-100/30',
                'badge' => 'bg-blue-600/20 text-blue-200 border-blue-400',
            ],
            'available' => [
                'card' => 'bg-white border-gray-200 text-gray-900',
                'text' => 'text-gray-900',
                'secondary' => 'text-gray-500',
                'button' => 'bg-gray-100 hover:bg-gray-200 text-gray-700',
                'details' => 'bg-white/50',
                'badge' => 'bg-green-100 text-green-700 border-green-300',
            ],
        ];

        $styles = $statusStyles[$status] ?? $statusStyles['available'];
    @endphp

    <div x-data="{ open: false, status: '{{ $status }}' }" class="{{ $baseCardClasses }} {{ $styles['card'] }}">
        {{-- Header --}}
        <div class="p-5 text-center space-y-2">
            <h4 class="text-lg font-semibold {{ $styles['text'] }} truncate" title="{{ $cottage->name }}">
                {{ $cottage->name }}
            </h4>
            <p class="text-sm {{ $styles['secondary'] }}">
                ₱{{ number_format($cottage->price, 2) }}
            </p>
            <div class="flex items-center justify-center gap-2">
                <i class="fas fa-circle text-xs" :class="{
                    'text-red-300': status === 'occupied',
                    'text-yellow-300': status === 'maintenance',
                    'text-blue-300': status === 'cleaning',
                    'text-green-400': status === 'available'
                }"></i>
                <span class="text-sm font-semibold {{ $styles['text'] }}">{{ ucfirst($status) }}</span>
            </div>
        </div>

        {{-- Quantity Input --}}
        <div class="p-5 border-t border-gray-200/50 flex justify-between items-center">
            <input type="number" name="accommodations[{{ $cottage->id }}]" min="0" value="0"
                data-price="{{ $cottage->price }}"
                class="w-20 px-3 py-2 text-center rounded-lg border-2 border-gray-100 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all duration-200">
            <span class="text-xs {{ $styles['secondary'] }}">
                Available: {{ $cottage->available ?? $cottage->quantity }}
            </span>
        </div>

        {{-- Details Toggle --}}
        @if(($cottage->bookings && $cottage->bookings->count()) || ($cottage->units && count($cottage->units)))
            <button
                @click="open = !open"
                class="flex w-full items-center justify-center gap-2 border-t border-gray-200/50 py-3 text-sm font-semibold transition-colors {{ $styles['button'] }}"
                :aria-expanded="open.toString()"
                aria-controls="accommodation-{{ $cottage->id }}-details"
                type="button"
            >
                <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                <span x-show="!open" x-cloak>View Details ({{ ($cottage->bookings->count() ?? 0) + count($cottage->units ?? []) }} items)</span>
                <span x-show="open" x-cloak>Hide Details</span>
            </button>

            {{-- Expanded Details --}}
            <div
                x-show="open"
                x-transition:enter="transition-all duration-300 ease-in-out"
                x-transition:enter-start="max-h-0 opacity-0"
                x-transition:enter-end="max-h-96 opacity-100"
                x-transition:leave="transition-all duration-200 ease-out"
                x-transition:leave-start="max-h-96 opacity-100"
                x-transition:leave-end="max-h-0 opacity-0"
                id="accommodation-{{ $cottage->id }}-details"
                class="border-t border-gray-200/50 overflow-hidden {{ $styles['details'] }} {{ $styles['secondary'] }}"
            >
                <div class="p-4 space-y-4 max-h-96 overflow-y-auto">
                    {{-- Units --}}
                    @if($cottage->units && count($cottage->units))
                        <section>
                            <h5 class="font-semibold {{ $styles['text'] }} mb-3 flex items-center gap-2 border-b border-gray-200/50 pb-2">
                                <i class="fas fa-bed text-sm"></i> Units
                            </h5>
                            <ul class="space-y-2">
                                @foreach($cottage->units as $unit)
                                    <li class="flex justify-between items-center py-1 rounded-md px-2 {{ $status !== 'available' ? 'bg-white/10' : 'hover:bg-gray-50' }}">
                                        <span class="text-sm truncate" title="{{ $unit['name'] }}">{{ $unit['name'] }}</span>
                                        <span class="px-2 py-1 rounded-full text-xs {{ $unit['status'] === 'Occupied' ? 'text-red-600' : 'text-green-600' }} font-semibold border {{ $styles['badge'] }}">
                                            <i class="fas {{ $unit['status'] === 'Occupied' ? 'fa-lock' : 'fa-unlock' }} mr-1"></i>
                                            {{ $unit['status'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    {{-- Bookings --}}
                    @if($cottage->bookings && $cottage->bookings->count())
                        <section>
                            <h5 class="font-semibold {{ $styles['text'] }} mb-3 flex items-center gap-2 border-b border-gray-200/50 pb-2">
                                <i class="fas fa-calendar-check text-sm"></i> Bookings ({{ $cottage->bookings->count() }})
                            </h5>
                            <ul class="space-y-3">
                                @foreach($cottage->bookings as $booking)
                                    <li class="bg-white/10 rounded-lg p-3 border border-gray-200/30 {{ $status !== 'available' ? 'backdrop-blur-sm' : '' }}">
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                            {{-- Booking Info --}}
                                            <div class="flex-1 space-y-1">
                                                <p class="font-medium {{ $styles['text'] }} truncate" title="{{ $booking['customer'] }}">
                                                    {{ $booking['customer'] }}
                                                </p>
                                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-users"></i> Qty: {{ $booking['quantity'] }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::parse($booking['date'])->format('M d, Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- Check-in / Check-out Buttons --}}
                                            @if(!empty($booking['booking_id']))
                                                <div class="flex flex-col gap-2 self-start sm:self-auto">
                                                    @if($booking['status'] !== 'checked_in' && $booking['status'] !== 'checked_out')
                                                        <form action="{{ route('admin.daytour.checkin', $booking['booking_id']) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-green-600 text-white rounded-md hover:bg-green-700">
                                                                Check-in
                                                            </button>
                                                        </form>
                                                    @elseif($booking['status'] === 'checked_in')
                                                        <form action="{{ route('admin.daytour.checkout', $booking['booking_id']) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                                Check-out
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-gray-400 italic">Completed</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endforeach
