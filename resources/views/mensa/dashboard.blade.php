@extends('layout.master')

@section('header' , 'Dashboard')

@section('content')

<div class="row justify-content-center text-center mt-4">
        <div class="col-md-10">
            <div class="row justify-content-start mt-5 pt-5">

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('mensa.foods.index')}}" color="bgc-cyan" icon="bi-fork-knife" title="Foods" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('mensa.menus.create')}}" color="bgc-green" icon="bi-calendar-plus" title="Add Menu" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('mensa.menus.showAll')}}" color="bgc-purple" icon="bi-calendar-week" title="All Menus" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('logout')}}" color="bgc-red" icon="bi-box-arrow-right" title="Logout" />
                </div>
                
            </div>
        </div>
</div>

@endsection