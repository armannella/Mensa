@extends('layout.master')

@section('pagetitle', 'Mensa')
@section('header', "Welcome")

@section('content')
    <div class="row justify-content-center text-center mt-4">
        <div class="col-md-10">
            

            <div class="row justify-content-center mt-5 pt-5">
                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{ route('auth.student.showRegisterForm') }}" color="bgc-green" icon="bi-person-plus" title="Register" />
                </div>
                
                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{ route('login') }}" color="bgc-purple" icon="bi-box-arrow-in-right" title="Login" />
                </div>
                
                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="https://www.linkedin.com/in/armannella/" color="bgc-red" icon="bi-linkedin" title="About Me" />
                </div>
            </div>

        </div>
    </div>
@endsection