@extends('layout.master')

@section('header' , 'Dashboard')

@section('content')

<div class="row justify-content-center text-center mt-4">
        <div class="col-md-10">
            <div class="row justify-content-center mt-5 pt-5">

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('student.documents.index')}}" color="bgc-green" icon="bi-person-plus" title="Scholarship" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('student.reserves.reserve.canteens')}}" color="bgc-green" icon="bi-person-plus" title="Reserve Meal" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('logout')}}" color="bgc-red" icon="bi-person-plus" title="Logout" />
                </div>

                
                
            </div>

        </div>

@endsection