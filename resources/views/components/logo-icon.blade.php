@props(['size' => 'default', 'src' => 'imgs/logo.png'])

@php
    $responsiveSizes = [
        'small' => 'max-h-6 sm:max-h-8',          // Small logo for small to sm screens
        'default' => 'max-h-8 sm:max-h-10 md:max-h-12', // Default size for base up to md
        'large' => 'max-h-10 sm:max-h-16 md:max-h-24',  // Large logo scaling up
        'xl' => 'max-h-12 sm:max-h-20 md:max-h-32',     // Extra large for larger screens
    ];
@endphp

<a href="{{ route('index') }}">
    <img src="{{ url($src) }}" class="{{ $responsiveSizes[$size] ?? $responsiveSizes['default'] }} w-auto" />
</a>
