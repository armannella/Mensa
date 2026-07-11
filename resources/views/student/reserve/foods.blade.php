@extends('layout.master')

@section('header', 'Foods')

@section('content')

<div class="row justify-content-center mt-4">
    <div class="col-md-10">
        
        <form action="{{ route('student.reserves.reserve.store',[$menu->canteen_id, $menu->id]) }}" method="post">
            @csrf

            <div class="foods">
                @foreach ($categories as $category)
                    <div class="type-name my-4">
                        <h1>{{ $category->name }}</h1>
                    </div>
                   <hr>
                    <div class="row foodscards my-3 category-row">
                
                        @foreach ($foods as $food)
                            @if ($food->category_id == $category->id)
                            <label for="food-{{ $food->id }}" class="w-100" style="cursor: pointer;">
                                <x-foodcard :food="$food">
                                    <x-slot name="footer">
                                        <div class="w-100 border border-white border-1">
                                            <div class="bgc-purple p-3">
                                               Capacity : {{ $food->details->capacity - $food->details->reserved }}
                                            </div>
                                            
                                            <div>
                                                <input type="radio" 
                                                        id="food-{{ $food->id }}" 
                                                        name="foods[{{ $category->id }}]" 
                                                        value="{{ $food->id }}" 
                                                        class="d-none food-selector">
                                            </div>
                                        </div>
                                    </x-slot>
                                </x-foodcard>
                            @endif   
                        @endforeach

                    </div>
                @endforeach
            </div>
            
            <x-button color="bgc-green">Reserve</x-button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.food-selector').forEach(input => {

        input.addEventListener('click', function(e) {
            const categoryRow = this.closest('.category-row');
            const selectedCard = this.closest('.foodcard');

           
            if (this.dataset.wasChecked === "true") {

                this.checked = false;
                this.dataset.wasChecked = "false";
                
                selectedCard.classList.remove('is-selected', 'border-success');
            } else {
                
                
                categoryRow.querySelectorAll('.food-selector').forEach(radio => {
                    radio.dataset.wasChecked = "false";
                });
                categoryRow.querySelectorAll('.foodcard').forEach(card => {
                    card.classList.remove('is-selected', 'border-success');
                });
                
                this.dataset.wasChecked = "true";
                selectedCard.classList.add('is-selected', 'border-success');
            }
        });
    }); 
</script>
@endsection