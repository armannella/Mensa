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
                                    Edit
                                </td>
                            </tr>
                        @empty
                            <td colspan="4" class="text-center py-4 text-white">No Canteen available yet.</td>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>



    
@endsection