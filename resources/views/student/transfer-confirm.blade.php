@extends('layout.master')

@section('header', 'Confirm Transfer')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card bg-dark border-secondary p-4">
            <p class="text-white">We sent a 6-digit code to your Telegram.</p>
            <p class="text-warning fs-5">Send {{ $otp->amount }} € to matricola {{ $otp->to_matricola }}</p>
            <form action="{{ route('student.wallet.transfer.verify') }}" method="POST">
                @csrf
                <x-input name="code" label="Code" placeholder="123456" maxlength="6" required />
                <x-button color="bgc-green">Confirm Transfer</x-button>
            </form>
        </div>
    </div>
</div>
@endsection