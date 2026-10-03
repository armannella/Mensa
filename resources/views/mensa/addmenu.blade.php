@extends('layout.master')

@section('header', 'Add Menu')

@section('content')

@error('error')
            <p class=" alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
@enderror

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
                <div>
                    <button type="button" id="btn-predict" class="btn btn-warning mt-5 w-100 h-50" style="min-width: 130px; height: 38px;">
                        <i class="fas fa-magic"></i> AI Suggest
                    </button>
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
                                        <div class="ai-suggestion-box d-none mb-2 p-2 border border-warning rounded" 
                                             style="background-color: #2c2c2c; cursor: pointer; transition: 0.2s;" 
                                             data-food-id="{{ $food->id }}" 
                                             title="Click to apply">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-warning small fw-bold">AI Suggests:</span>
                                                <span class="badge bg-warning text-dark predicted-val fs-6">0</span>
                                            </div>
                                            <div class="text-white-50 mt-1 suggestion-info" style="font-size: 0.7rem; line-height: 1.2;"></div>
                                        </div>

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
    document.getElementById('btn-predict').addEventListener('click', function() {
            const dateVal = document.getElementById('date').value;
            const mealVal = document.getElementById('meal').value;

            if (!dateVal || !mealVal) {
                alert('Please select Menu Date and Meal first!');
                return;
            }

            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Loading...';
            btn.disabled = true;

            fetch('{{ route("mensa.menus.predictCapacity") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    date: dateVal,
                    meal: mealVal
                })
            })
            .then(response => response.json())
            .then(data => {
                // پیدا کردن تمام باکس‌های پیشنهاد و جاگذاری اعداد
                document.querySelectorAll('.ai-suggestion-box').forEach(box => {
                    const foodId = box.dataset.foodId;
                    if (data[foodId]) {
                        box.querySelector('.predicted-val').textContent = data[foodId].capacity;
                        box.querySelector('.suggestion-info').textContent = data[foodId].info;
                        box.classList.remove('d-none'); // نمایش باکس
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load predictions. Check network or console.');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

        // با کلیک روی باکس زرد، عدد پیش‌بینی‌شده اتوماتیک می‌ره توی اینپوت!
        document.querySelectorAll('.ai-suggestion-box').forEach(box => {
            box.addEventListener('click', function() {
                const val = this.querySelector('.predicted-val').textContent;
                const inputId = 'food-' + this.dataset.foodId;
                const input = document.getElementById(inputId);
                
                input.value = val;
                input.dispatchEvent(new Event('input')); // این خط باعث می‌شه کلاس is-selected خودت هم فعال بشه
            });
        });
</script>
@endsection