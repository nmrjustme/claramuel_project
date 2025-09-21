{{-- resources/views/admin/daytour/facility-card.blade.php --}}

@php
    $status = strtolower($facility->display_status);

    // Base card styles
    $baseCardClasses = 'rounded-xl border shadow-sm transition-shadow duration-300 hover:shadow-lg flex flex-col justify-between';

    // Status-based styles
    $statusStyles = [
        'occupied' => [
            'card' => 'bg-red-600 border-red-600 text-white',
            'text' => 'text-white',
            'secondary' => 'text-red-200',
            'button' => 'bg-red-700 hover:bg-red-600 text-white',
            'details' => 'bg-red-600',
        ],
        'maintenance' => [
            'card' => 'bg-yellow-400 border-yellow-400 text-white',
            'text' => 'text-white',
            'secondary' => 'text-yellow-100',
            'button' => 'bg-yellow-500 hover:bg-yellow-400 text-white',
            'details' => 'bg-yellow-400',
        ],
        'cleaning' => [
            'card' => 'bg-blue-600 border-blue-600 text-white',
            'text' => 'text-white',
            'secondary' => 'text-blue-200',
            'button' => 'bg-blue-700 hover:bg-blue-600 text-white',
            'details' => 'bg-blue-600',
        ],
        'available' => [
            'card' => 'bg-white border-gray-200 text-gray-900',
            'text' => 'text-gray-900',
            'secondary' => 'text-gray-500',
            'button' => 'bg-gray-100 hover:bg-gray-200 text-gray-700',
            'details' => 'bg-white',
        ],
    ];

    $styles = $statusStyles[$status] ?? $statusStyles['available'];
@endphp

<div x-data="{ open: false }" class="{{ $baseCardClasses }} {{ $styles['card'] }}" role="region" aria-labelledby="facility-{{ $facility->id }}-name">
    <div class="p-5 text-center space-y-2">
        <h4 id="facility-{{ $facility->id }}-name" class="text-lg font-semibold {{ $styles['text'] }} truncate" title="{{ $facility->name }}">
            {{ $facility->name }}
        </h4>
        <p class="text-xs font-medium uppercase tracking-wide {{ $styles['secondary'] }}">
            {{ $facility->category }}
        </p>

        <p class="mt-1 text-sm font-semibold {{ $styles['text'] }} flex items-center justify-center gap-2">
            <i class="fas fa-circle text-xs" :class="{
                'text-red-300': '{{ $status }}' === 'occupied',
                'text-yellow-300': '{{ $status }}' === 'maintenance',
                'text-blue-300': '{{ $status }}' === 'cleaning',
                'text-green-400': '{{ $status }}' === 'available',
            }"></i>
            {{ ucfirst($status) }}
        </p>
    </div>

    @if(($facility->bookings && $facility->bookings->count()) || ($facility->units && count($facility->units)))
        <button
            @click="open = !open"
            class="flex w-full items-center justify-center gap-2 rounded-b-xl border-t border-gray-300 py-2 text-xs font-semibold transition {{ $styles['button'] }}"
            aria-expanded="false"
            :aria-expanded="open.toString()"
            aria-controls="facility-{{ $facility->id }}-details"
            type="button"
        >
            <span x-show="!open" x-cloak><i class="fas fa-chevron-down"></i> View Details</span>
            <span x-show="open" x-cloak><i class="fas fa-chevron-up"></i> Hide Details</span>
        </button>

        <div
            x-show="open"
            x-transition
            id="facility-{{ $facility->id }}-details"
            class="border-t border-gray-300 p-4 text-xs {{ $styles['details'] }} {{ $styles['secondary'] }} space-y-4"
            x-cloak
        >
            @if($facility->units && count($facility->units))
                <div>
                    <h5 class="font-semibold {{ $styles['text'] }} mb-2 border-b border-gray-300 pb-1">Units</h5>
                    <ul class="space-y-1">
                        @foreach($facility->units as $unit)
                            @php
                                $unitStatus = strtolower($unit['status']);
                                $unitStatusClass = 'text-green-600 font-semibold';
                                if ($unitStatus === 'occupied') {
                                    $unitStatusClass = $status === 'available' ? 'text-red-600 font-semibold' : 'text-white font-semibold';
                                } elseif ($status !== 'available') {
                                    $unitStatusClass = 'text-green-400 font-semibold';
                                }
                            @endphp
                            <li class="flex justify-between">
                                <span>{{ $unit['name'] }}</span>
                                <span class="{{ $unitStatusClass }}">{{ ucfirst($unit['status']) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($facility->bookings && $facility->bookings->count())
                <div>
                    <h5 class="font-semibold {{ $styles['text'] }} mb-2 border-b border-gray-300 pb-1">Bookings</h5>
                    <ul class="space-y-2">
                        @foreach($facility->bookings as $booking)
                            <li>
                                <p class="font-medium {{ $styles['text'] }}">{{ $booking['customer'] }}</p>
                                <p>Qty: {{ $booking['quantity'] }} | Date: {{ $booking['date'] }} | Status: <span class="capitalize">{{ $booking['status'] }}</span></p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>