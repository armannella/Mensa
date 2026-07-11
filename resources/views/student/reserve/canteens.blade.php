@extends('layout.master')

@section('header' , 'Canteens')

@section('content')
    <div class="row d-flex justify-content-center  mt-4">     
        <div class="col-md-7">
            <div class="row">
                
                <div>
                    <x-table :headers="['name', 'address' , 'Actions']">
                        @forelse ($canteens as $mensa)
                            <tr>
                                <td>{{$mensa->name}}</td>
                                <td>{{$mensa->address}}</td>
                                <td>
                                    <a href="{{ route('student.reserves.reserve.menus', $mensa->id) }}"> <x-actionbtn color="bgc-pink">View Menus</x-actionbtn> </a>
                                </td>
                            </tr>
                        @empty
                            <td colspan="3" class="text-center py-4 text-white">No Canteen available yet.</td>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>



    
@endsection