@extends('layout.master')

@section('header' , 'Congigs')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <form action="{{ route('admin.configs.update') }}" method="post" class="bg-dark p-4 border border-secondary">
            @csrf
            @method("PUT")
            
            <h4 class="text-warning mb-4 border-bottom pb-2">Lunch Configs</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <x-input name="lunch_start" label="Lunch Start Time" value="{{ $configs['lunch_start']->value ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-input name="lunch_end" label="Lunch End Time" value="{{ $configs['lunch_end']->value ?? '' }}"/>
                </div>
            </div>

            <h4 class="text-warning mb-4 border-bottom pb-2 mt-5">Dinner Time</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <x-input name="dinner_start" label="Dinner Start Time" value="{{ $configs['dinner_start']->value ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-input name="dinner_end" label="Dinner End Time" value="{{ $configs['dinner_end']->value ?? '' }}"/>
                </div>
            </div>

            <h4 class="text-warning mb-4 border-bottom pb-2 mt-5">Reservation Settings</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <x-input name="reserve_time" label="Maximum hours before Reserve" value="{{ $configs['reserve_time']->value ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-input name="daily_sale_reserve_time" label="Daily sale Open Time (minute)" value="{{ $configs['daily_sale_reserve_time']->value ?? '' }}"/>
                </div>
            </div>

            <div class="text-end mt-4">
                <x-button type="submit" color="bgc-green">Save Configs</x-button>
            </div>
        </form>
    </div>
</div>
@endsection