{{-- resources/views/admin/daytour/facility-card.blade.php --}}

@php
    $status = strtolower($facility->display_status);

    $baseCardClasses = 'rounded-xl border shadow-sm transition-all duration-300 hover:shadow-lg flex flex-col justify-between overflow-hidden';

    $statusStyles = [
        'occupied' => [
            // MODIFIED: Switched to light red background with dark red text for less strain.
            'card' => 'bg-red-50 border-red-300 text-red-800', 
            'text' => 'text-red-800', // Changed to dark text
            'secondary' => 'text-red-500', // Changed to muted dark red
            'button' => 'bg-red-600 hover:bg-red-700 text-white', // Kept button dark for action
            'details' => 'bg-red-100/30', // Adjusted details background to be light
            'badge' => 'bg-red-100 text-red-700 border-red-300', // Adjusted badge for light background
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
        'unavailable' => [
            'card' => 'bg-red-100 border-gray-300 text-gray-500',
            'text' => 'text-gray-600',
            'secondary' => 'text-gray-400',
            'button' => 'bg-red-200 hover:bg-red-300 text-gray-600',
            'details' => 'bg-gray-200/30',
            'badge' => 'bg-gray-200 text-gray-600 border-gray-300',
            'li_hover' => 'hover:bg-gray-100',
            'booking_card' => 'bg-gray-100/20 border-gray-200/50',
        ],
    ];

    // If the selected date is in the past, override status to "unavailable"
    $isPastDate = \Carbon\Carbon::parse($date)->lt(now()->startOfDay());
    if ($isPastDate) {
        $status = 'unavailable';
    }
    

    $styles = $statusStyles[$status] ?? $statusStyles['available'];
@endphp

<div x-data="{ open: false }" 
      class="{{ $baseCardClasses }} {{ $styles['card'] }}" 
      role="region" 
      aria-labelledby="facility-{{ $facility->id }}-name"
      x-cloak>
    
    {{-- Header --}}
    <div class="p-5 text-center space-y-2">
        <h4 id="facility-{{ $facility->id }}-name" 
            class="text-lg font-semibold {{ $styles['text'] }} truncate"
            title="{{ $facility->name }}">
            {{ $facility->name }}
        </h4>
        <p class="text-xs font-medium uppercase tracking-wide {{ $styles['secondary'] }}">
            {{ $facility->category }}
        </p>
        <div class="flex items-center justify-center gap-2">
            {{-- Adjusted dot colors to be consistent with the new card background --}}
            <i class="fas fa-circle text-xs" :class="{
                // Use a slightly darker color for the dot now that the background is light
                'text-red-500': '{{ $status }}' === 'occupied', 
                'text-yellow-300': '{{ $status }}' === 'maintenance',
                'text-blue-300': '{{ $status }}' === 'cleaning',
                'text-green-400': '{{ $status }}' === 'available',
                'text-red-300': '{{ $status }}' === 'unavailable',
            }"></i>
            <span class="text-sm font-semibold {{ $styles['text'] }}">{{ ucfirst($status) }}</span>
        </div>
    </div>

    {{-- Details Toggle --}}
    @if(($facility->bookings && $facility->bookings->count()) || ($facility->units && count($facility->units)))
        <button
            @click="open = !open"
            class="flex w-full items-center justify-center gap-2 border-t border-gray-200/50 py-3 text-sm font-semibold transition-colors {{ $styles['button'] }}"
            :aria-expanded="open.toString()"
            aria-controls="facility-{{ $facility->id }}-details"
            type="button"
            aria-label="Toggle {{ $facility->name }} details"
        >
            <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            <span x-show="!open" x-cloak>View Details ({{ ($facility->bookings->count() ?? 0) + count($facility->units ?? []) }} items)</span>
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
            id="facility-{{ $facility->id }}-details"
            class="border-t border-gray-200/50 overflow-hidden {{ $styles['details'] }} {{ $styles['secondary'] }}"
        >
            <div class="p-4 space-y-4 max-h-96 overflow-y-auto">
                {{-- Units --}}
                @if($facility->units && count($facility->units))
                    <section aria-labelledby="units-heading-{{ $facility->id }}">
                        <h5 id="units-heading-{{ $facility->id }}" class="font-semibold {{ $styles['text'] }} mb-3 flex items-center gap-2 border-b border-gray-200/50 pb-2">
                            <i class="fas fa-bed text-sm"></i> Units
                        </h5>
                        <ul class="space-y-2">
                            @foreach($facility->units as $unit)
                                @php
                                    $unitStatus = strtolower($unit['status']);
                                    // Adjusted unit status class logic for the new light 'occupied' card design
                                    $unitStatusClass = 'text-green-600 font-semibold';
                                    if ($unitStatus === 'occupied') {
                                        // On the light red background, we want the occupied units to stand out but remain legible
                                        $unitStatusClass = $status === 'available' ? 'text-red-600 font-semibold' : 'text-red-600 font-semibold';
                                    } elseif ($status !== 'available') {
                                        $unitStatusClass = 'text-green-600 font-semibold';
                                    }
                                @endphp
                                <li class="flex justify-between items-center py-1 rounded-md px-2 {{ $status !== 'available' ? 'bg-white/10' : 'hover:bg-gray-50' }}">
                                    <span class="text-sm truncate flex-1 min-w-0 pr-2" title="{{ $unit['name'] }}">{{ $unit['name'] }}</span>
                                    {{-- Use the facility's badge style for consistency --}}
                                    <span class="px-2 py-1 rounded-full text-xs {{ $unitStatusClass }} border {{ $styles['badge'] }} whitespace-nowrap flex-shrink-0">
                                        <i class="fas {{ $unitStatus === 'occupied' ? 'fa-lock' : 'fa-unlock' }} mr-1"></i>
                                        {{ ucfirst($unit['status']) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Bookings --}}
                @if($facility->bookings && $facility->bookings->count())
                    <section aria-labelledby="bookings-heading-{{ $facility->id }}">
                        <h5 id="bookings-heading-{{ $facility->id }}" class="font-semibold {{ $styles['text'] }} mb-3 flex items-center gap-2 border-b border-gray-200/50 pb-2">
                            <i class="fas fa-calendar-check text-sm"></i> Bookings ({{ $facility->bookings->count() }})
                        </h5>
                        <ul class="space-y-3">
                            @foreach($facility->bookings as $booking)
                                {{-- Use white background for legibility on light status cards, but with a slight opacity for the colored card statuses --}}
                                <li class="bg-white/90 rounded-lg p-3 border border-gray-200/80 shadow-sm">
                                    <div class="flex flex-col gap-3">
                                        {{-- Booking Info --}}
                                        <div class="flex-1 space-y-2 min-w-0">
                                            <p class="font-medium text-gray-900 truncate" title="{{ $booking['customer'] }}">
                                                {{ $booking['customer'] }}
                                            </p>
                                            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                                <span class="flex items-center gap-1 whitespace-nowrap">
                                                    <i class="fas fa-house"></i> Qty: {{ $booking['quantity'] }}
                                                </span>
                                                <span class="flex items-center gap-1 whitespace-nowrap">
                                                    <i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::parse($booking['date'])->format('M d, Y') }}
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-xs whitespace-nowrap text-gray-500">Status:</span>
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold capitalize whitespace-nowrap flex-shrink-0
                                                    @if($booking['status'] === 'checked_in') bg-green-100 text-green-800 border border-green-300
                                                    @elseif($booking['status'] === 'checked_out') bg-gray-100 text-gray-800 border border-gray-300
                                                    @else bg-yellow-100 text-yellow-800 border border-yellow-300
                                                    @endif">
                                                    <i class="fas 
                                                         @if($booking['status'] === 'checked_in') fa-check-circle
                                                         @elseif($booking['status'] === 'checked_out') fa-clock
                                                         @else fa-clock
                                                         @endif
                                                     "></i>
                                                     {{ str_replace('_', ' ', $booking['status']) }}
                                                </span>
                                            </div>
                                            {{-- Timeline --}}
                                            <div class="flex flex-wrap items-center gap-3 text-xs mt-1 pt-1 border-t border-gray-200/30">
                                                @if(!empty($booking['checked_in_at']))
                                                    <span class="flex items-center gap-1 text-green-500 whitespace-nowrap">
                                                        <i class="fas fa-sign-in-alt"></i>
                                                        In: {{ \Carbon\Carbon::parse($booking['checked_in_at'])->format('g:i A') }}
                                                    </span>
                                                @endif
                                                @if(!empty($booking['checked_out_at']))
                                                    <span class="flex items-center gap-1 text-gray-500 whitespace-nowrap">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                        Out: {{ \Carbon\Carbon::parse($booking['checked_out_at'])->format('g:i A') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        @if(!empty($booking['booking_id']))
                                            @php
                                                $isTodayOrLater = \Carbon\Carbon::today()->gte(\Carbon\Carbon::parse($booking['date']));
                                            @endphp

                                            @if($isTodayOrLater)
                                                <div class="flex flex-col gap-2 self-start w-full sm:w-auto">
                                                    @if($booking['status'] !== 'checked_in' && $booking['status'] !== 'checked_out')
                                                        {{-- Show Check-in only if today or later --}}
                                                        <form action="{{ route('admin.daytour.checkin', $booking['booking_id']) }}" method="POST" class="w-full">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-semibold bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 min-w-[100px]"
                                                                    onclick="return confirm('Confirm check-in for {{ $booking['customer'] }}?')"
                                                                    aria-label="Check in {{ $booking['customer'] }}">
                                                                <i class="fas fa-sign-in-alt"></i> Check-in
                                                            </button>
                                                        </form>
                                                    @elseif($booking['status'] === 'checked_in')
                                                        {{-- Show Check-out only if today or later --}}
                                                        <form action="{{ route('admin.daytour.checkout', $booking['booking_id']) }}" method="POST" class="w-full">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-semibold bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[100px]"
                                                                    onclick="return confirm('Confirm check-out for {{ $booking['customer'] }}?')"
                                                                    aria-label="Check out {{ $booking['customer'] }}">
                                                                <i class="fas fa-sign-out-alt"></i> Check-out
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-gray-400 italic text-center sm:text-left">Completed</span>
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Hide buttons until reservation date --}}
                                                <span class="text-xs text-gray-400 italic">Actions available on {{ \Carbon\Carbon::parse($booking['date'])->format('M d, Y') }}</span>
                                            @endif
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