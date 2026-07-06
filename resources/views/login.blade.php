@extends('layout.master')

@section('header' , 'Login')


@section('content')
    <div class="row justify-content-start  mt-5">
        <div class="col-md-9">
            <form action="{{ route('auth.login') }}" method="POST">
                @csrf
                
                <x-input name="username" label="username" placeholder="KHDRMN00D18Z224D" required/>
                <x-input type="password" name="password" label="Password" placeholder="Enter you password" required/>

                <x-button color="bgc-green">Login !</x-button>
            </form>
        </div>
    </div>
@endsection
