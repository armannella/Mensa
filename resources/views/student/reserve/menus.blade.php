@extends('layout.master')

@section('header' , 'Menus')

@section('content')

    <div class="row d-flex justify-content-center  mt-4">     
        <div class="col-md-7">
            <div class="row">
                
                <div>
                    <x-table :headers="['Date', 'Meal' , 'Actions']">
                        @forelse ($menus as $menu)
                            <tr>
                                <td>{{$menu->date->format('Y-m-d')}}</td>
                                <td>{{$menu->meal->value}}</td>
                                <td>
                                    <div class="d-flex justify-content-center column-gap-2">
                                        @if ($menu->canBeReserved())
                                            <a href="{{ route('student.reserves.reserve.Normalmenu',[$menu->canteen_id, $menu->id]) }}"> <x-actionbtn color="bgc-pink">Reserve Meal</x-actionbtn> </a>
                                        @endif

                                        @if ($menu->canBeDailyReserved())
                                            <a href="{{ route('student.reserves.reserve.dailymenu',[$menu->canteen_id, $menu->id]) }}"> <x-actionbtn color="bgc-orange">Daily Reserve Meal</x-actionbtn> </a>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <td colspan="3" class="text-center py-4 text-white">No Menu available yet.</td>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>



    
@endsection