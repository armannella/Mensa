@props(['type' => 'submit', 'color' => 'bgc-green'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'dokme mt-5 mb-3 ' . $color]) }}>
    {{ $slot }}
</button>