@extends('layout.master')

@section('header', 'Delivere Reserve')

@section('content')
<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-11">
        
        @error('error')
            <p class="text-white alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
        @enderror

        <div class="d-flex flex-column flex-md-row justify-content-center align-items-stretch gap-4 my-4">
            
            
            <div class="text-center bg-white p-4 rounded shadow d-flex align-items-center justify-content-center">
                <svg id="my-barcode"></svg>
            </div>
            
           
            <div class="bgc-indigo text-warning p-4 rounded shadow d-flex flex-column justify-content-center align-items-center" style="min-width: 250px;">
                <h5 class="text-white mb-3 w-100 text-center border-bottom border-secondary pb-2">Reserve Details</h5>
                <p class="mb-2 fs-5 fw-bold">{{$menu->date->format('Y-m-d')}}</p>
                <p class="mb-2 fs-5 fw-bold">{{$menu->meal->value}}</p>
                <p class="mb-0 fs-5 fw-bold">{{$menu->canteen->name}}</p>
            </div>
            
        </div>
        
        <hr class="border-secondary">

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

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const barcodeNumber = "{{$barcode}}";

        JsBarcode("#my-barcode", barcodeNumber, {
            format: "CODE128",     
            lineColor: "#000000",  
            width: 3,              
            height: 100,           
            displayValue: true,    
            fontSize: 20,          
            margin: 10             
        });

    });
</script>
@endsection