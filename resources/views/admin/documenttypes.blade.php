@extends('layout.master')

@section('header' , 'Documents')

@section('content')
    <div class="row d-flex justify-content-between  mt-4">
        <div class="col-md-4 ">
            <div class="row ">
                <div>
                    <form action="{{ route('admin.documents.store') }}" method="post">
                        @csrf
                        <x-input name="title" label="title of Document" placeholder="e.g ISEE" required/>
                        <x-input name="description" label="description" placeholder="optional"/>
                        <div class="form-check form-switch mt-3 mb-2">
                            <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="yes">
                            <label class="form-check-label" for="is_required">is Required ?</label>
                        </div>
                        <x-button color="bgc-green">ADD !</x-button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="row">
                

                <div>
                    <x-table :headers="['Title', 'Description' ,'is_required', 'Actions']">
                        @forelse ($documentTypes as $type)
                            <tr>
                                <td>{{$type->title}}</td>
                                <td>{{$type->description}}</td>
                                @if ($type->is_required)
                                    <td>Yes</td>
                                @else
                                    <td>No</td>
                                @endif
                                
                                <td>
                                    <div class="d-flex justify-content-center column-gap-2">                                   
                                        <form action="{{ route('admin.documents.delete', $type->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-actionbtn type="submit" color="bgc-red">Delete</x-actionbtn>
                                        </form>
                                    <div>
                                </td>
                            </tr>
                        @empty
                            <td colspan="4" class="text-center py-4 text-white">No Document Type available yet.</td>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>



    
@endsection