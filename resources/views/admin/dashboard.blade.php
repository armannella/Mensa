@extends('layout.master')

@section('header' , 'Dashboard')

@section('content')

<div class="row justify-content-center text-center mt-4">
        <div class="col-md-10">
            <div class="row justify-content-start mt-5 pt-5">
                
                <div class="col-md-3 col-6 mb-4">
                    <x-tile href="{{route('admin.discounts.index')}}" color="bgc-pink" icon="bi-percent" title="Discount Plans" />
                </div>

                
                <div class="col-md-3 col-6 mb-4">
                    <x-tile href="{{route('admin.documents.index')}}" color="bgc-cyan" icon="bi-file-earmark-text" title="Documents" />
                </div>

                
                <div class="col-md-3 col-6 mb-4">
                    <x-tile href="{{route('admin.scholarships.index')}}" color="bgc-green" icon="bi-mortarboard" title="Scholarship Apps" />
                </div>

                
                <div class="col-md-3 col-6 mb-4">
                    <x-tile href="{{route('admin.canteens.index')}}" color="bgc-purple" icon="bi-shop" title="Canteens" />
                </div>

               
                <div class="col-md-3 col-6 mb-4">
                    <x-tile href="{{route('admin.categories.index')}}" color="bgc-lime" icon="bi-grid" title="Categories" />
                </div>

                
                <div class="col-md-3 col-6 mb-4">
                    <x-tile href="{{route('admin.configs.index')}}" color="bg-secondary" icon="bi-gear" title="Configs" />
                </div>
                
                
                <div class="col-md-3 col-6 mb-4">
                    <x-tile href="{{route('logout')}}" color="bgc-red" icon="bi-box-arrow-right" title="Logout" />
                </div>
            </div>
        </div>

@endsection