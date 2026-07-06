@props(['name', 'label', 'type' => 'text', 'value' => '', 'placeholder' => '' , 'id' => null])
@php 
    $inputId = $id ?? $name; 
@endphp
<label for="{{ $inputId }}" class="form-label mt-3">{{ $label }} :</label>
<input 
    type="{{ $type }}" 
    name="{{ $name }}" 
    id="{{ $inputId }}" 
    value="{{ old($name, $value) }}" 
    placeholder="{{ $placeholder }}" 
    {{ $attributes->merge(['class' => 'form-control mb-2']) }}
>

@error($name)
    <p class="text-warning m-0 mt-1" style="font-size: 14px;">* {{ $message }}</p>
@enderror