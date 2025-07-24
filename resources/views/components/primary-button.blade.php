<button {{ $attributes->merge(['type' => 'submit', 'class' => 'mt-auto bg-red-500 hover:bg-red-600 text-white font-semibold font-semibold py-2 px-6 rounded w-fit self-end']) }}>
    {{ $slot }}
</button>
