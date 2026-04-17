@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-lg border border-darkGray rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent']) }}>
