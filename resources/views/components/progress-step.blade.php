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

<div class="w-full px-4 sm:px-0 pt-4 pb-5">
    <div class="flex justify-between items-center mb-10 relative mx-auto"> <!-- Constrain width for consistency -->
        
        <!-- Background line (full width) -->
        <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 -z-10"></div>
        
        <!-- Progress line (dynamic %) -->
        <div class="absolute top-1/2 left-0 h-1 bg-primary -z-10 transition-all duration-500 ease-out" 
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
                        w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center 
                        font-bold mb-2 transition-all duration-300
                        {{ $isActive ? 'bg-primary text-white' : 'border-2 border-gray-300 bg-white text-gray-400' }}
                        {{ $isCurrent ? 'ring-4 ring-primary/30 scale-110' : '' }}">
                        @if($isCompleted)
                            ✓
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