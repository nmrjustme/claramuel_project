@props([
    'currentStep' => 1,
    'steps' => []
])

@php
    // Calculate progress percentage dynamically based on steps
    $totalSteps = count($steps);
    $progressPercentage = $totalSteps > 1 ? 
        (($currentStep - 1) / ($totalSteps - 1)) * 100 : 
        0;
@endphp

<div class="w-full sm:px-6 pt-6 pb-6">
    <div class="flex justify-between items-center mb-10 relative mx-auto"> <!-- Same width behavior as original -->
        
        <!-- Background line (full width) -->
        <div class="absolute top-1/2 left-0 right-0 h-0.5 sm:h-1 bg-gray-200 -z-10"></div>
        
        <!-- Progress line (dynamic %) -->
        <div class="absolute top-1/2 left-0 h-0.5 sm:h-1 bg-primary -z-10 transition-all duration-500 ease-out" 
             style="width: {{ $progressPercentage }}%"></div>
        
        @foreach ($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isCompleted = $currentStep > $stepNumber;
                $isActive = $currentStep >= $stepNumber;
                $isCurrent = $currentStep === $stepNumber;
                
                // Equal spacing calculation
                $positionPercentage = ($index / ($totalSteps - 1)) * 100;
            @endphp

            <!-- Step Marker (positioned absolutely for perfect alignment) -->
            <div class="absolute left-0 transform -translate-x-1/2 -translate-y-1/2"
                 style="left: {{ $positionPercentage }}%; top: 50%;">
                <div class="flex flex-col items-center">
                    <!-- Circle -->
                    <div class="
                        w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center 
                        font-medium sm:font-bold text-sm sm:text-base mb-1 sm:mb-2 transition-all duration-300
                        {{ $isActive ? 'bg-primary text-white' : 'border-2 border-gray-300 bg-white text-gray-400' }}
                        {{ $isCurrent ? 'ring-2 sm:ring-4 ring-primary/30 scale-110' : '' }}">
                        @if($isCompleted)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>
                    
                    <!-- Label -->
                    <span class="text-xs sm:text-sm font-medium text-center whitespace-nowrap
                        {{ $isActive ? 'text-primary' : 'text-gray-500' }}">
                        {{ $step['label'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>