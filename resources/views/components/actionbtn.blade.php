@props(['color', 'type' => 'button'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'text-white rounded-0 py-1 px-2 border-0 ' . $color]) }}>
    {{ $slot }}
</button>