@props(['href' => '#', 'color' => 'bgc-blue', 'icon' => 'bi-app', 'title'])

<a href="{{ $href }}" class="tile {{ $color }} position-relative">
    <i class="bi {{ $icon }} mb-2"></i>
    <h3 class="m-0 fw-light text-capitalize">{{ $title }}</h3>
    
    {{ $slot }}
</a>