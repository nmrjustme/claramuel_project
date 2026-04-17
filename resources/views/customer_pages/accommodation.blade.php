<div class="bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4 py-8">
        <!-- Room Facilities Section -->
        <div class="mt-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($roomFacilities as $room)
                    <div class="dark:bg-gray-700 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden flex flex-col h-full">
                        @if($room->images->first())
                            <img 
                                class="w-full h-52 object-cover" 
                                src="{{ asset('imgs/facility_img/' . $room->images->first()->image) }}" 
                                alt="{{ $room->name }} facility image"
                                loading="lazy"
                                width="400"
                                height="208"
                            />
                        @endif
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="mb-3 text-xl md:text-2xl dark:text-gray-100 font-bold text-gray-900">
                                {{ $room->name }}
                            </h3>
                            <p class="mb-4 font-normal dark:text-gray-300 text-gray-700 flex-grow line-clamp-3">
                                {{ Str::limit($room->description, 120) }}
                            </p>
                            
                            <div class="mt-auto space-y-3">
                                <!-- Room Amenities -->
                                <div class="flex flex-col gap-3 text-sm text-gray-700 dark:text-gray-200">
                                    <!-- Bed Information -->
                                    <div class="flex items-center gap-2">
                                        <svg class="w-6 h-6 flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <title>Bed Icon</title>
                                            <path d="M2.535 11A3.981 3.981 0 0 0 2 13v4a1 1 0 0 0 1 1h2v1a1 1 0 1 0 2 0v-1h10v1a1 1 0 1 0 2 0v-1h2a1 1 0 0 0 1-1v-4c0-.729-.195-1.412-.535-2H2.535ZM20 9V8a4 4 0 0 0-4-4h-3v5h7Zm-9-5H8a4 4 0 0 0-4 4v1h7V4Z"/>
                                        </svg>
                                        <span>{{ $room->bed_number }} {{ Str::plural('Bed', $room->bed_number) }}</span>
                                    </div>
                            
                                    <!-- Guest Capacity -->
                                    <div class="flex items-center gap-2">
                                        <svg class="w-6 h-6 flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <title>Guest Icon</title>
                                            <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $room->pax }} {{ Str::plural('Guest', $room->pax) }}</span>
                                    </div>
                                    
                                </div>
                            </div>

                            <!-- View Deal Button -->
                            <a 
                                href="{{ route('facility.deal', ['id' => $room->id]) }}" 
                                class="mt-4 self-end"
                                aria-label="View deal for {{ $room->name }}"
                            >
                                <x-primary-button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-6 rounded transition-colors duration-200">
                                    View Deal
                                </x-primary-button>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-center col-span-4 text-gray-500 dark:text-gray-400 py-8">
                        Currently no rooms available. Please check back later.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</div>