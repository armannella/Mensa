@extends('layout.master')

@section('header' , 'Register')


@section('content')
    <div class="row justify-content-start  mt-5">
        <div class="col-md-9">
            <form action="{{ route('auth.student.register') }}" method="POST">
                @csrf
                
                <x-input name="name" label="Full Name" placeholder="e.g. Arman Khademi" required />
                <x-input type="email" name="email" label="Email Address (only University Email)" placeholder="name@studenti.unime.it" required/>
                <x-input type="number" name="matricola" label="Matricola" placeholder="556026" required/>
                <x-input name="username" label="Codice Fiscale" placeholder="KHDRMN00D18Z224D" required/>
                <x-input type="password" name="password" label="Password" placeholder="Minimum 8 characters" required/>
                <x-input type="password" name="password_confirmation" label="Confirm Password" placeholder="Confirm your password" required/>

                <x-button color="bgc-green">Register !</x-button>
            </form>
        </div>
    </div>
@endsection
