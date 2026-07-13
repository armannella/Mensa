@extends('layout.master')

@section('header', 'Menu Statistics')

@section('content')
<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-11">
        
        @error('error')
            <p class="text-white alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
        @enderror

        <div class="mb-4 text-white border-bottom border-secondary pb-3">
            <h4>Statistics for: {{ $menu->date->format('Y-m-d') }} ({{ ucfirst($menu->meal->value) }})</h4>
        </div>

        <div class="row foodscards my-3">
            
            @foreach($foods as $food)
                <x-foodcard :food="$food">
                    <x-slot name="footer">
                        <div class="w-100 border-top border-secondary pt-3 mt-2">
                            
                            
                            <div class="row text-center mb-2">
                                <div class="col-6 mb-3">
                                    <small class="text-white-50 d-block" style="font-size: 0.75rem;">Total Cap.</small>
                                    <span class="fs-5 fw-bold text-white">{{ $food->details->capacity }}</span>
                                </div>
                                <div class="col-6 mb-3">
                                    <small class="text-warning d-block" style="font-size: 0.75rem;">Reserved</small>
                                    <span class="fs-5 fw-bold text-warning">{{ $food->details->reserved }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-white-50 d-block" style="font-size: 0.75rem;">Daily Cap.</small>
                                    <span class="fs-5 fw-bold text-white">{{ $food->details->daily_sale_capacity ?? 0 }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-warning d-block" style="font-size: 0.75rem;">Daily Res.</small>
                                    <span class="fs-5 fw-bold text-warning">{{ $food->details->daily_sale_reserved ?? 0 }}</span>
                                </div>
                            </div>

                            <hr class="border-secondary my-3">

                            @if ($menu->canDailySaleDefined())
                                <form action="{{ route('mensa.menus.storeDaily', $menu->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="food_id" value="{{ $food->id }}">
                                
                                <div class="mb-2">
                                    <input type="number" name="daily_capacity" class="form-control form-control-sm  text-white border-secondary text-center" placeholder="New Daily Cap (e.g. 50)" required min="1">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-sm w-100 py-2" style="background-color: #198754;">Set Capacity</button>
                                </div>
                            </form>
                            @endif
                            
                            
                        </div>
                    </x-slot>
                </x-foodcard>
            @endforeach
            
        </div>

    </div>
</div>
@endsection