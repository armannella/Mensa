@extends('layout.master')

@section('header', 'Add Menu')

@section('content')

<div class="row justify-content-center mt-4">
    <div class="col-md-10">
        <form action="{{ route('mensa.menus.store')}}" method="post">
            @csrf
            <div class="d-flex justify-content-between column-gap-3">
                <div class="w-100">
                    <x-input type="date" label="Menu Date" name="date" id="date" required/>
                </div>
                <div class="w-100">
                    <label for="meal" class="form-label mt-3">Meal :</label>
                    <select class="form-select" name="meal" id="meal" required>
                        @foreach ($meals as $meal)
                            <option class="text-dark" value="{{ $meal->value }}">{{ $meal->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="foods">
                @foreach ($categories as $category)
                    <div class="type-name my-4">
                        <h1>{{ $category->name }}</h1>
                    </div>
                    <hr>
                    <div class="row foodscards my-3">
                
                        @foreach ($foods as $food)
                            @if ($food->category_id == $category->id)
                                <x-foodcard :food="$food">
                                    <x-slot name="footer">
                                        <x-input type="number" name="foods[{{ $food->id }}][quantity]" id="food-{{ $food->id }}" min="0" placeholder="Quantity" class="quantity-input"/>
                                    </x-slot>
                                </x-foodcard>
                            @endif   
                        @endforeach

                
                    </div>
                @endforeach
            </div>
            <x-button color="bgc-green">ADD Menu!</x-button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            const card = this.closest('.foodcard');
            
            if (this.value > 0) {
                card.classList.add('is-selected');
            } else {
                card.classList.remove('is-selected');
            }
        });
    }); 
</script>
@endsection