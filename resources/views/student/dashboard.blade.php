@extends('layout.master')

@section('header' , 'Dashboard')

@section('content')

<div class="row justify-content-center text-center mt-4">
        <div class="col-md-10">
            <div class="row justify-content-center mt-5 pt-5">

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('student.documents.index')}}" color="bgc-cyan" icon="bi-mortarboard" title="Scholarship" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('student.reserves.reserve.canteens')}}" color="bgc-green" icon="bi-fork-knife" title="Reserve Meal" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{ route('student.reserves.all')}}" color="bgc-purple" icon="bi-clock-history" title="Reserves History" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{ route('student.wallet.index')}}" color="bgc-lime" icon="bi-wallet2" title="Wallet" />
                </div>

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{ route('student.profile.show')}}" color="bgc-pink" icon="bi-person-circle" title="Profile" />
                </div>

                    <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('logout')}}" color="bgc-red" icon="bi-box-arrow-right" title="Logout" />
                </div>
                
            </div>
        </div>
</div>

@endsection