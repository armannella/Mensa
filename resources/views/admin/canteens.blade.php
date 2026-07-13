@extends('layout.master')

@section('header' , 'Canteens')

@section('content')


    <div class="row d-flex justify-content-between  mt-4">    
        <div class="col-md-4 ">
            <div class="row ">
                <div>
                    <form action="{{ route('admin.canteens.store') }}" method="post">
                        
                        @csrf
                
                        <x-input name="name" label="Canteen Name" placeholder="e.g. Papardo" required />
                        <x-input type="email" name="email" label="Email Address" placeholder="name@unime.it" required/>
                        <x-input type="text" name="address" label="address" placeholder="viale garibaldi" required/>
                        <x-input name="username" label="Username" placeholder="papardo" required/>
                        <x-input type="password" name="password" label="Password" placeholder="Minimum 8 characters" required/>
                        <x-input type="password" name="password_confirmation" label="Confirm Password" placeholder="Confirm your password" required/>

                        <x-button color="bgc-green">Register !</x-button>
            
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="row">
                <div>
                    <x-table :headers="['name', 'address' ,'username', 'Actions']">
                        @forelse ($canteens as $mensa)
                            <tr>
                                <td>{{$mensa->name}}</td>
                                <td>{{$mensa->address}}</td>
                                <td>{{$mensa->user->username}}</td>
                                
                                <td>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-info text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#editInfoModal{{$mensa->id}}">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#editPasswordModal{{$mensa->id}}">
                                            Password
                                        </button>
                                    </div>

                                    
                                    <x-modal id="editInfoModal{{$mensa->id}}" title="Edit Canteen Info">
                                        <form action="{{ route('admin.canteens.update', $mensa->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <x-input name="name" label="Canteen Name" value="{{ $mensa->name }}" required />
                                            <x-input type="email" name="email" label="Email Address" value="{{ $mensa->user->email }}" required/>
                                            <x-input type="text" name="address" label="Address" value="{{ $mensa->address }}" required/>
                                            <x-input name="username" label="Username" value="{{ $mensa->user->username }}" required/>
                                            
                                            <div class="d-flex justify-content-end mt-4">
                                                <x-button color="bg-info text-dark">Save Changes</x-button>
                                            </div>
                                        </form>
                                    </x-modal>

                        
                                    <x-modal id="editPasswordModal{{$mensa->id}}" title="Change Password for {{ $mensa->name }}">
                                        <form action="{{ route('admin.canteens.password', $mensa->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <x-input type="password" name="password" label="New Password" required/>
                                            <x-input type="password" name="password_confirmation" label="Confirm New Password" required/>
                                            
                                            <div class="d-flex justify-content-end mt-4">
                                                <x-button color="bg-warning text-dark">Update Password</x-button>
                                            </div>
                                        </form>
                                    </x-modal>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-white">No Canteen available yet.</td>
                            </tr>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>

@endsection