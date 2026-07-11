@props(['food', 'isSelected' => false])

<div class="col-md-4 my-3">
    <div @class(['foodcard', 'is-selected' => $isSelected]) data-id="{{ $food->id }}" id="food-card-{{ $food->id }}">
        <div class="foodimage"> 
            <img src="{{ asset('storage/' . $food->image_path) }}" alt="{{ $food->name }}">
        </div>
        <div class="foodname my-2">{{ $food->name }}</div>
        <div class="fooddetails">{{ $food->ingredients }}</div>
        
        @if(isset($footer))
            <div class="foodfooter mt-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>