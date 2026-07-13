@extends('layout.master')

@section('header', 'Reserve Details')

@section('content')
<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-11">
        
        @error('error')
            <p class="text-white alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
        @enderror

        <div class="mb-4 text-warning border-bottom border-secondary pb-3 d-flex justify-content-between">
            <h4 class="p-3 bgc-purple">Reserve for: {{ $reserve->menu->date->format('Y-m-d') }} ({{ ucfirst($reserve->menu->meal->value) }})</h4>
            <h4 class="p-3 bgc-purple">Reserved at: {{ $reserve->created_at->format('Y-m-d H:i') }}</h4>
        </div>

        <div class="row foodscards my-3">
            
            @foreach($foods as $food)
                <x-foodcard :food="$food">
                    <x-slot name="footer">
                        <button type="button" class="btn btn-success btn-sm w-100 py-2" style="background-color: #198754;">{{ $food->category->name }}</button>
                    </x-slot>
                </x-foodcard>
            @endforeach
            
        </div>

    </div>
</div>
@endsection