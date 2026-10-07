@extends('layout.master')

@section('header', 'Foods')

@section('content')

<div class="row justify-content-center mt-4">
    <div class="col-md-10">
        
        @error('error')
            <p class="alert alert-danger border-0 rounded-0 my-3" style="font-size: 14px;">{{ $message }}</p>
        @enderror

        <form action="{{ route('student.reserves.reserve.store', [$menu->canteen_id, $menu->id]) }}" method="post" id="reservation-form">
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

            
                                    <x-foodcard :food="$food" class="{{ $food->details->isCapacityFinished() ? 'opacity-75 border-warning' : '' }}">
                                        <x-slot name="footer">

                                            @if ($food->details->isCapacityFinished())
                                                @php
                                                    $waitList = $food->details->hasStudentWaitList();
                                                @endphp
                                                
                                                <div class="w-100 border-top border-warning mt-2 pt-2">
                                                    @if ($waitList)
                                                        
                                                        <div class="bg-warning text-dark p-2 text-center small fw-bold mb-2 rounded-1">
                                                            Position: {{ $waitList->positionInWaitList() }} / {{ $food->details->getWaitListSize() }}
                                                        </div>
                                                        <a href="{{ route('student.waitlist.remove', [$menu->canteen_id, $menu->id, $food->id, $waitList->id]) }}" 
                                                           class="btn btn-danger w-100 btn-sm waitlist-action-btn">
                                                            <i class="fas fa-times"></i> Leave WaitList
                                                        </a>
                                                    @else

                                                        <div class="bg-secondary text-white-50 p-2 text-center small mb-2 rounded-1">
                                                            <i class="fas fa-users"></i> Queue Size: {{ $food->details->getWaitListSize() }}
                                                        </div>
                                                        <a href="{{ route('student.waitlist.add', [$menu->canteen_id, $menu->id, $food->id]) }}" 
                                                           class="btn btn-warning w-100 btn-sm waitlist-action-btn fw-bold text-dark">
                                                            <i class="fas fa-hourglass-half"></i> Join Waitlist
                                                        </a>
                                                    @endif
                                                </div>

                                            @else
                                                {{-- ظرفیت موجود است (رزرو عادی) --}}
                                                <div class="w-100 border-top border-secondary mt-2">
                                                    <div class="bgc-purple p-2 text-center text-white rounded-1 mb-1">
                                                        Capacity: {{ $food->details->capacity - $food->details->reserved }}
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
                                            @endif
                                            
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
            const radio = card.querySelector('.food-selector');
            
            // اگر کارت رادیوباتن ندارد (یعنی ظرفیت پر است)، اصلاً رویداد کلیک برای سلکت شدن روی آن نگذار!
            if (!radio) return;

            card.style.cursor = 'pointer'; 
            
            card.addEventListener('click', function(e) {
                // جلوگیری از تداخل کلیک وقتی کاربر روی لینک‌ها یا اینپوت‌ها می‌زند
                if(e.target.tagName.toLowerCase() === 'input' || e.target.tagName.toLowerCase() === 'a' || e.target.closest('a')) return;

                const categoryRow = this.closest('.category-row');
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
                        // فقط کارت‌هایی که رادیوباتن دارند را ریست کن
                        if(c.querySelector('.food-selector')) {
                            c.classList.remove('border', 'border-success', 'border-3');
                            const ind = c.querySelector('.select-indicator');
                            if (ind) {
                                ind.textContent = "Click to Select";
                                ind.classList.remove('text-success', 'fw-bold');
                                ind.classList.add('text-white-50');
                            }
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

        // لودینگ برای دکمه‌های صف انتظار
        document.querySelectorAll('.waitlist-action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait...';
                this.classList.add('disabled');
            });
        });
    });
</script>
@endsection