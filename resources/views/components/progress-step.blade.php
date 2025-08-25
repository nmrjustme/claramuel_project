@props([
    'currentStep' => 1,
    'steps' => []
])

@php
    $totalSteps = count($steps);
    $progressPercentage = $totalSteps > 1 ? 
        (($currentStep - 1) / ($totalSteps - 1)) * 100 : 
        0;
@endphp

<div class="w-full px-4 sm:px-6 pt-6 pb-6">
    <div class="relative flex justify-between items-center mb-10 mx-auto max-w-5xl">

        <!-- Background line -->
        <div class="absolute top-1/2 left-0 right-0 h-0.5 sm:h-1 bg-gray-200 -z-10"></div>

        <!-- Progress line -->
        <div class="absolute top-1/2 left-0 h-0.5 sm:h-1 bg-primary -z-10 transition-all duration-500 ease-in-out"
             style="width: {{ $progressPercentage }}%"></div>

        @foreach ($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isCompleted = $currentStep > $stepNumber;
                $isActive = $currentStep >= $stepNumber;
                $isCurrent = $currentStep === $stepNumber;
                $positionPercentage = ($index / ($totalSteps - 1)) * 100;
            @endphp

            <div class="absolute left-0 transform -translate-x-1/2 -translate-y-1/2"
                 style="left: {{ $positionPercentage }}%; top: 50%;">
                <div class="flex flex-col items-center max-w-[80px] sm:max-w-[100px] text-center">

                    <!-- Circle -->
                    <div class="
                        w-7 h-7 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center
                        font-semibold text-xs sm:text-sm md:text-base mb-1 sm:mb-2 transition-all duration-300
                        {{ $isActive ? 'bg-primary text-white shadow-lg' : 'border-2 border-gray-300 bg-white text-gray-400' }}
                        {{ $isCurrent ? 'ring-2 sm:ring-4 ring-primary/30 scale-110' : '' }}">
                        
                        @if($isCompleted)
                            <span class="text-base sm:text-lg">✓</span>
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>

                    <!-- Label -->
                    <span class="text-[9px] sm:text-xs md:text-sm font-medium leading-tight break-words
                        {{ $isActive ? 'text-primary' : 'text-gray-500' }}">
                        {{ $step['label'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

</div>
