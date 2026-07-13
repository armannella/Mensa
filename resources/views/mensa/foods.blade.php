@extends('layout.master')

@section('header' , 'Foods')

@section('content')
    <div class="row d-flex justify-content-between  mt-4">
        <div class="col-md-4 ">
            <div class="row ">
                <div>
                    <form action="{{ route('mensa.foods.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <x-input name="name" label="Food Name" placeholder="Food name" required/>
                        <label for="category" class="form-label mt-3">Category :</label>
                        <select class="form-select" name="category_id" id="category" required>
                            @foreach ($categories as $category)
                                <option class="text-dark" value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input name="ingredients" label="ingredients" placeholder="ingredients" required/>
                        <label for="image" class="form-label mt-3">Image :</label>
                        <input type="file" id="image" name="image" class="form-control form-control rounded-0" required>
                        <x-button color="bgc-green">ADD !</x-button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row foodscards my-3">
            
            @foreach($foods as $food)
                <x-foodcard :food="$food">
                    <x-slot name="footer">
                        <button type="button" class="btn btn-success btn-sm w-100 py-2" style="background-color: #198754;">{{ $food->category->name }}</button>
                        <form action="{{ route('mensa.foods.destroy' , $food->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100 py-2">Delete</button>
                        </form>
                    </x-slot>
                </x-foodcard>
            @endforeach
            
            </div>
        </div>
    </div>

@endsection