@extends('layout.master')

@section('header', 'Daily Sale Foods')

@section('content')

<div class="row justify-content-center mt-4">
    <div class="col-md-10">
        
        <div class="alert bg-dark border border-warning text-white d-flex justify-content-between align-items-center rounded-0 mb-4 p-3">
            <div>
                <strong class="text-warning">Live Yield Pricing Active:</strong> 
                <span class="small text-white-50">Prices drop dynamically based on remaining time and each food's real-time stock surplus.</span>
            </div>
            <span class="badge bg-warning text-dark">Price Lock: 2 Mins</span>
        </div>

        @error('error')
            <p class="alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
        @enderror

        <form action="{{ route('student.reserves.reserve.storeDaily', [$menu->canteen_id, $menu->id]) }}" method="post" id="reservation-form">
            @csrf

            <div class="foods">
                @foreach ($categories as $category)
                    @if($foods->where('category_id', $category->id)->count() > 0)
                        <div class="type-name my-4">
                            <h2 class="text-white">
                                {{ $category->name }} 
                                <span class="fs-6 text-white-50">(Base Price: {{ number_format($category->price, 2) }} €)</span>
                            </h2>
                        </div>
                        <hr class="border-secondary">
                        
                        <div class="row category-row">
                            @foreach ($foods as $food)
                                @if ($food->category_id == $category->id)
                                
                                    <x-foodcard :food="$food">
                                        <x-slot name="footer">
                                            <div class="w-100 border-top border-secondary mt-2">
                                                
                                                <div class="bg-dark p-2 text-center border-bottom border-secondary d-flex justify-content-around align-items-center">
                                                    <div>
                                                        <span class="text-white-50 text-decoration-line-through small me-1">
                                                            {{ number_format($food->flash_quote['base_price'], 2) }} €
                                                        </span>
                                                        <span class="text-success fw-bold fs-5">
                                                            {{ number_format($food->flash_quote['price'], 2) }} €
                                                        </span>
                                                    </div>
                                                    <span class="badge bg-danger">
                                                        -{{ number_format($food->flash_quote['discount'], 1) }}% Off
                                                    </span>
                                                </div>

                                                <div class="bgc-purple p-2 text-center text-white">
                                                    Capacity: {{ $food->details->daily_sale_capacity - $food->details->daily_sale_reserved }} / {{ $food->details->daily_sale_capacity }}
                                                </div>
                                                
                                                <div class="p-2 text-center select-indicator text-white-50 small" style="transition: 0.2s;">
                                                    Click to Select
                                                </div>
                                                
                                                <input type="radio" 
                                                       id="food-{{ $food->id }}" 
                                                       name="foods[{{ $category->id }}]" 
                                                       value="{{ $food->id }}" 
                                                       data-price="{{ $food->flash_quote['price'] }}"
                                                       data-base-price="{{ $food->flash_quote['base_price'] }}"
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
                        <div class="text-white-50 small mb-1">
                            Original Base Total: <span id="base-total-price" class="text-decoration-line-through">0.00</span> €
                        </div>
                        <h4 class="text-white m-0">Flash Sale Subtotal: <span id="subtotal-price" class="text-warning">0.00</span> €</h4>
                        <h5 class="text-success m-0 mt-2">
                            Final Price (<span id="discount-label">0</span>% Scholarship off): 
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
        const baseTotalDisplay = document.getElementById('base-total-price');
        const subtotalDisplay = document.getElementById('subtotal-price');
        const finalPriceDisplay = document.getElementById('final-price');

        function updateLivePrice() {
            let baseTotal = 0;
            let subTotal = 0;
            
            document.querySelectorAll('.food-selector:checked').forEach(radio => {
                baseTotal += parseFloat(radio.dataset.basePrice || 0);
                subTotal += parseFloat(radio.dataset.price || 0);
            });
            
            const discountAmount = (subTotal * discountRate) / 100;
            const finalPrice = subTotal - discountAmount;
            
            baseTotalDisplay.textContent = baseTotal.toFixed(2);
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