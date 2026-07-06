@extends('layout.master')

@section('header' , 'Dashboard')

@section('content')

<div class="row justify-content-center text-center mt-4">
        <div class="col-md-10">
            <div class="row justify-content-center mt-5 pt-5">

                <div class="col-md-4 col-6 mb-3">
                    <x-tile href="{{route('student.documents.index')}}" color="bgc-green" icon="bi-person-plus" title="Scholarship" />
                </div>

                
                
            </div>

        </div>

@endsection