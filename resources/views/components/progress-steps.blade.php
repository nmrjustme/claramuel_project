@props(['currentStep' => 1, 'progress' => '0%', 'steps' => []])

<div class="flex justify-between items-center mb-10 relative">
    <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 -z-10"></div>
    <div class="absolute top-1/2 left-0 h-1 bg-primary -z-10" id="progress-bar" style="width: {{ $progress }}"></div>

    @foreach ($steps as $index => $step)
        @php
            $stepNumber = $index + 1;
            $isActive = $currentStep > $index;
            $isCurrent = $currentStep === $stepNumber;
        @endphp

        <div class="flex flex-col items-center progress-step {{ $isCurrent ? 'active' : '' }}">
            <div class="
                w-10 h-10 rounded-full
                {{ $isActive ? 'bg-primary text-white' : 'border-2 border-gray-300 bg-white text-gray-400' }}
                flex items-center justify-center font-bold mb-2">
                {{ $stepNumber }}
            </div>
            <span class="text-sm font-medium text-center    
                {{ $isActive ? 'text-primary' : 'text-gray-500' }}">
                {{ $step['label'] }}
            </span>
        </div>
    @endforeach
</div>
