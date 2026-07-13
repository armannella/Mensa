@extends('layout.master')

@section('header', 'Delivery Station')

@section('content')

        @error('error')
            <p class="text-white alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
        @enderror

<div class="row justify-content-between mt-4 mb-5">
    <div class="col-md-3">

        <div class="bgc-indigo text-warning p-4 rounded shadow d-flex flex-column justify-content-center align-items-center" style="min-width: 250px;">
                <h5 class="text-white mb-3 w-100 text-center border-bottom border-secondary pb-2">Reserve Details</h5>
                <p class="mb-2 fs-5 fw-bold">{{$menu->date->format('Y-m-d')}}</p>
                <p class="mb-2 fs-5 fw-bold">{{$menu->meal->value}}</p>
                <p class="mb-0 fs-5 fw-bold">{{$menu->canteen->name}}</p>
            </div>
        <hr>
       <div>
            <form action="{{ route('mensa.delivery.store' , $menu->id) }}" method="post">
                @csrf
                <x-input name="barcode" label="Barcode" placeholder="Barcode" required/>
                <x-button color="bgc-green">Delivere !</x-button>
            </form>
        </div>
        
        <hr>
        
    </div>   
    <div class="col-md-8">
        @if($reserve)
            @php
                $foods = $reserve->foods()->get();
            @endphp
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-white m-0">Student's Order</h4>
                <span class="badge bg-success fs-6">Delivered</span>
            </div>

            <div class="row foodscards my-3">
                @foreach($foods as $food)
                    <x-foodcard :food="$food">
                        <x-slot name="footer">
                            <button type="button" class="btn btn-success  w-100 py-2 disabled" style="opacity: 1;">
                                {{ $food->category->name }}
                            </button>
                        </x-slot>
                    </x-foodcard>
                @endforeach
            </div>
        @else
            
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white-50 border border-secondary rounded" style="background-color: #2c2c2c; min-height: 300px;">
                <h5>Ready to Scan</h5>
                <p class="small">Scan a student's barcode to see their meal details.</p>
            </div>
        @endif
    </div>   
    
</div>


@endsection