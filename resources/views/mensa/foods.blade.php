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

        <div class="col-md-7">
            <div class="row">
                

                <div>
                    <x-table :headers="['index','Name', 'Category', 'ingredients', 'Actions']">
                        @forelse ($foods as $food)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$food->name}}</td>
                                <td>{{$food->category->name}}</td>
                                <td>{{$food->ingredients}}</td>
                                <td>
                                    {{-- <div class="d-flex justify-content-center column-gap-2">
                                    
                                        <form action="{{ route('', $food->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this food?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-actionbtn type="submit" color="bgc-red">Delete</x-actionbtn>
                                        </form>
                                    <div> --}}
                                </td>
                            </tr>
                        @empty
                            <td colspan="4" class="text-center py-4 text-white">No food available yet.</td>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>

@endsection