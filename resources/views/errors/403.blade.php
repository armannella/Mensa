@extends('layout.master')

@section('header', 'Access Denied')

@section('content')
<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-6 text-center">
        
        <div class="card bg-dark border-danger shadow-lg mt-5">
            <div class="card-body p-5">
                
                <h1 class="display-1 text-danger fw-bold mb-0">403</h1>
                <h4 class="text-white-50 mb-4">Forbidden</h4>
                
                <hr class="border-secondary mb-4">

                <div class="alert alert-danger border-0 rounded text-dark fw-bold fs-5 mb-4">
                    {{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}
                </div>
                
            </div>
        </div>

    </div>
</div>
@endsection