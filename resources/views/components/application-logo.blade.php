@props(['size' => 'default', 'src' => 'imgs/main-logo.png'])

@php
    $sizes = [
        'small' => 'max-h-8 w-auto',   // Small logo
        'default' => 'max-h-12 w-full', // Default size
        'large' => 'max-h-16 w-auto',  // Large logo
    ];
@endphp

<span id="logo" class="hidden sm:inline text-2xl font-bold text-indigo-600">
    <a href="{{ route('index') }}">
        <img class="col-span-2 object-contain lg:col-span-1 {{ $sizes[$size] ?? $sizes['default'] }}"
        src="{{ url($src) }}"
        alt="Logo" width="158" height="48">
    </a>
</span>
