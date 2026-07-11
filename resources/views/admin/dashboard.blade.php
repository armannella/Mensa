@extends('layout.master')

@section('header' , 'Dashboard')

@section('content')

<div class="row justify-content-center text-center mt-4">
        <div class="col-md-10">
            <div class="row justify-content-center mt-5 pt-5">
                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('admin.discounts.index')}}" color="bgc-green" icon="bi-person-plus" title="Discount Plans" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('admin.documents.index')}}" color="bgc-cyan" icon="bi-person-plus" title="Documents" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('admin.scholarships.index')}}" color="bgc-red" icon="bi-person-plus" title="Scholarship Apps" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('admin.canteens.index')}}" color="bgc-purple" icon="bi-person-plus" title="Canteens" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('admin.categories.index')}}" color="bgc-lime" icon="bi-person-plus" title="Categories" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('admin.configs.index')}}" color="bgc-pink" icon="bi-person-plus" title="Configs" />
                </div>
                
                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('logout')}}" color="bgc-red" icon="bi-person-plus" title="Logout" />
                </div>
            </div>
        </div>

@endsection