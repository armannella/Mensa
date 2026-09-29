@extends('layout.master')

@section('header', 'All Menus')

@section('content')
<div class="row d-flex justify-content-center mt-4">     
    <div class="col-md-10">
        
        <div class="d-flex justify-content-between mb-3 align-items-center">
            <h4 class="text-white m-0">All Menus</h4>
            <a href="{{ route('mensa.menus.create') }}">
                <x-button color="bgc-green">+ Add New Menu</x-button>
            </a>
        </div>

        <x-table :headers="['Date', 'Meal', 'Status', 'Actions']">
            @forelse ($menus as $menu)
                <tr>
                    <td>{{ $menu->date->format('Y-m-d') }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($menu->meal->value) }}</span></td>
                    
                    <td>
                        @if($menu->date->isToday())
                            <span class="badge bg-success">Today</span>
                        @elseif($menu->date->isPast())
                            <span class="badge bg-danger">Finished</span>
                        @else
                            <span class="badge bg-info text-dark">Upcoming</span>
                        @endif
                    </td>
                    
                    <td>
                        <div class="d-flex justify-content-center column-gap-2">
                            
                            
                            <a href="{{ route('mensa.menus.show', $menu->id) }}"> 
                                <x-actionbtn color="bgc-blue">Details</x-actionbtn> 
                            </a>

                            
                            @if($menu->canBeServed())
                                <a href="{{ route('mensa.delivery.show', $menu->id) }}"> 
                                    <x-actionbtn color="bgc-orange">Delivery Station</x-actionbtn> 
                                </a>
                            @endif

                            
                            @if($menu->canSeeFeedbacks())
                                <a href="{{ route('mensa.menus.feedbacks.show', $menu->id) }}"> 
                                    <x-actionbtn color="bgc-pink">Feedbacks</x-actionbtn> 
                                </a>
                            @endif

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-white">No menus defined yet.</td>
                </tr>
            @endforelse
        </x-table>
        
        <!-- Pagination Links -->
        <div class="mt-3">
            {{ $menus->links() }}
        </div>
    </div>
</div>
@endsection