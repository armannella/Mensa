@extends('layout.master')

@section('header', 'Foods')

@section('content')

<div class="row justify-content-center mt-4">
    <div class="col-md-10">
        
        @error('error')
            <p class="text-white alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
        @enderror

        <form action="{{ route('student.reserves.reserve.storeDaily', [$menu->canteen_id, $menu->id]) }}" method="post" id="reservation-form">
            @csrf

            <div class="foods">
                @foreach ($categories as $category)
                    @if($foods->where('category_id', $category->id)->count() > 0)
                        <div class="type-name my-4">
                            <h2 class="text-white">{{ $category->name }} <span class="fs-6 text-white-50">({{ number_format($category->price, 2) }} €)</span></h2>
                        </div>
                        <hr class="border-secondary">
                        
                        <div class="row category-row">
                            @foreach ($foods as $food)
                                @if ($food->category_id == $category->id)
                                
                                    
                                    <x-foodcard :food="$food">
                                        <x-slot name="footer">
                                            <div class="w-100 border-top border-secondary mt-2">
                                                <div class="bgc-purple p-2 text-center text-white">
                                                    Capacity: {{ $food->details->daily_sale_capacity - $food->details->daily_sale_reserved}}
                                                </div>
                                                
                                                <div class="p-2 text-center select-indicator text-white-50 small" style="transition: 0.2s;">
                                                    Click to Select
                                                </div>
                                                
                                                
                                                <input type="radio" 
                                                        id="food-{{ $food->id }}" 
                                                        name="foods[{{ $category->id }}]" 
                                                        value="{{ $food->id }}" 
                                                        data-price="{{ $category->price }}"
                                                        class="d-none food-selector">
                                            </div>
                                        </x-slot>
                                    </x-foodcard>
                                    
                                @endif   
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
            
            
            <div class="card bg-dark border-secondary mt-5 mb-5 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center p-4">
                    <div class="mb-3 mb-md-0 text-center text-md-start">
                        <h4 class="text-white m-0">Subtotal: <span id="subtotal-price">0.00</span> €</h4>
                        <h5 class="text-success m-0 mt-2">
                            Final Price (<span id="discount-label">0</span>% off): 
                            <span id="final-price">0.00</span> €
                        </h5>
                    </div>
                    <x-button color="bgc-green" class="btn-lg px-5 py-3">Confirm & Pay</x-button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const discountRate = parseFloat("{{ auth()->user()->student->discountPlan?->percentage ?? 0 }}") || 0;
        
        document.getElementById('discount-label').textContent = discountRate;
        const subtotalDisplay = document.getElementById('subtotal-price');
        const finalPriceDisplay = document.getElementById('final-price');

        
        function updateLivePrice() {
            let subTotal = 0;
            
            document.querySelectorAll('.food-selector:checked').forEach(radio => {
                subTotal += parseFloat(radio.dataset.price || 0);
            });
            
            
            const discountAmount = (subTotal * discountRate) / 100;
            const finalPrice = subTotal - discountAmount;
            
            subtotalDisplay.textContent = subTotal.toFixed(2);
            finalPriceDisplay.textContent = finalPrice.toFixed(2);
        }

        
        document.querySelectorAll('.foodcard').forEach(card => {
            card.style.cursor = 'pointer'; 
            
            card.addEventListener('click', function(e) {
                
                if(e.target.tagName.toLowerCase() === 'input') return;

                const categoryRow = this.closest('.category-row');
                const radio = this.querySelector('.food-selector');
                const indicator = this.querySelector('.select-indicator');

                if (radio.dataset.wasChecked === "true") {
                    
                    radio.checked = false;
                    radio.dataset.wasChecked = "false";
                    
                    this.classList.remove('border', 'border-success', 'border-3');
                    if (indicator) {
                        indicator.textContent = "Click to Select";
                        indicator.classList.remove('text-success', 'fw-bold');
                        indicator.classList.add('text-white-50');
                    }
                } else {
                    
                    categoryRow.querySelectorAll('.food-selector').forEach(r => {
                        r.checked = false;
                        r.dataset.wasChecked = "false";
                    });
                    categoryRow.querySelectorAll('.foodcard').forEach(c => {
                        c.classList.remove('border', 'border-success', 'border-3');
                        const ind = c.querySelector('.select-indicator');
                        if (ind) {
                            ind.textContent = "Click to Select";
                            ind.classList.remove('text-success', 'fw-bold');
                            ind.classList.add('text-white-50');
                        }
                    });
                    
                    
                    radio.checked = true;
                    radio.dataset.wasChecked = "true";
                    
                    this.classList.add('border', 'border-success', 'border-3');
                    if (indicator) {
                        indicator.textContent = "Selected ✓";
                        indicator.classList.add('text-success', 'fw-bold');
                        indicator.classList.remove('text-white-50');
                    }
                }

                
                updateLivePrice();
            });
        }); 
    });
</script>
@endsection