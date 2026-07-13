@extends('layout.master')

@section('header', 'Leave Feedback')

@section('content')
<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-6">
        
        <div class="card bg-dark border-secondary shadow">
            <div class="card-header bgc-purple text-white">
                <h5 class="m-0">How was your meal?</h5>
            </div>
            
            <div class="card-body">
                <p class="text-white-50 mb-4">
                    Menu Date: <strong>{{ $reserve->menu->date->format('Y-m-d') }}</strong> <br>
                    Canteen: <strong>{{ $reserve->menu->canteen->name }}</strong>
                </p>

                <form action="{{ route('student.reserves.feedback.store', $reserve->id) }}" method="POST">
                    @csrf
                    
                    
                    <div class="mb-4">
                        <label class="form-label text-white d-block mb-3">Rate your meal (1 to 5 Stars):</label>
                        <div class="d-flex gap-3">
                            @for($i = 1; $i <= 5; $i++)
                                <div>
                                    <input type="radio" class="btn-check" name="rating" id="rating-{{ $i }}" value="{{ $i }}" required>
                                    <label class="btn btn-outline-warning" for="rating-{{ $i }}">{{ $i }} ⭐</label>
                                </div>
                            @endfor
                        </div>
                        @error('rating')
                            <small class="text-danger mt-2 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="comment" class="form-label text-white">Write your feedback:</label>
                        <textarea class="form-control bg-dark text-white border-secondary" name="comment" id="comment" rows="4" placeholder="Tell us what you liked or what could be improved..." required></textarea>
                        @error('comment')
                            <small class="text-danger mt-2 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <x-button color="bgc-green">Submit Feedback</x-button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>
@endsection