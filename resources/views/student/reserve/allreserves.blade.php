@extends('layout.master')

@section('header', 'My Reservations')

@section('content')
<div class="row d-flex justify-content-center mt-4">     
    <div class="col-md-10">

        @error('error')
            <p class="text-white alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
        @enderror
        
    

        <x-table :headers="['Date', 'Mensa', 'Meal', 'Price', 'Status' , 'Reserved Time', 'Actions']">
            @forelse ($reserves as $reserve)
                <tr>
                    <td>{{ $reserve->menu->date->format('Y-m-d') }}</td>
                    <td>{{ $reserve->menu->canteen->name }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($reserve->menu->meal->value) }}</span></td>
                    <td>{{ $reserve->price }} €</td>
                    
                    <td>
                        @if($reserve->status->value === 'delivered')
                            <span class="badge bg-success">Delivered</span>
                        @elseif ($reserve->status->value === 'missed')
                            <span class="badge bg-danger">Missed</span>
                        @else
                            <span class="badge bg-warning text-dark">Upcoming</span>
                        @endif


                        
                    </td>
                    <td>{{ $reserve->created_at}}</td>
                    <td>
                        <div class="d-flex justify-content-center column-gap-2">
                            
                            <a href="{{ route('student.reserves.details', $reserve->id) }}"> 
                                <x-actionbtn color="bgc-blue">Details</x-actionbtn> 
                            </a>

                            
                            @if ($reserve->menu->canBeServed() && $reserve->status->value != 'delivered')
                                <a href="{{ route('student.reserves.delivere', $reserve->id) }}"> 
                                    <x-actionbtn color="bgc-green">Get Barcode</x-actionbtn> 
                                </a>
                            @endif

                            
                            @if ($reserve->canBeCancelled() && $reserve->status->value != 'delivered')
                                <form action="{{ route('student.reserves.cancel', $reserve->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this reservation? Money will be refunded.');">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <x-actionbtn type="submit" color="bgc-red">Cancel</x-actionbtn>
                                    
                                </form>
                            @endif

                            @if ($reserve->status->value === 'delivered' && $reserve->canBeReviewed())
                                <a href="{{ route('student.reserves.feedback.show', $reserve->id) }}"> 
                                    <x-actionbtn color="bgc-orange">Feedback</x-actionbtn> 
                                </a>
                            @endif

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-white">No reservations found.</td>
                </tr>
            @endforelse
        </x-table>
    </div>
</div>
@endsection