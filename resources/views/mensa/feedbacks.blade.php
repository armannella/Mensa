@extends('layout.master')

@section('header', 'Menu Feedbacks')

@section('content')
<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-10">

        <div class="d-flex justify-content-between align-items-center bg-dark p-3 rounded border border-secondary mb-4 shadow-sm">
            <div>
                <h5 class="text-warning m-0">{{ $menu->date->format('Y-m-d') }}</h5>
                <small class="text-white-50">{{ ucfirst($menu->meal->value) }}</small>
            </div>
            <div class="text-end">
                <h3 class="text-white m-0">⭐ {{ $averageRating }} <span class="fs-6 text-white-50">/ 5.0</span></h3>
                <small class="text-white-50">Based on {{ $feedbacks->count() }} reviews</small>
            </div>
        </div>

        @error('error')
            <div class="alert alert-danger border-0 rounded-0 mb-4">{{ $message }}</div>
        @enderror

        
        <div class="card bg-dark border-info shadow-sm mb-4">
            <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold">AI Sentiment Summary</h6>
                
                <form action="{{ route('mensa.menus.feedbacks.ai', $menu->id) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-dark">
                        {{ $aiSummary ? 'Regenerate AI Summary' : 'Generate AI Summary' }}
                    </button>
                </form>
            </div>
            <div class="card-body">
                @if($aiSummary)
                    <p class="text-white m-0" style="line-height: 1.6;">{{ $aiSummary->summary }}</p>
                @else
                    <p class="text-white-50 m-0 text-center">No AI summary generated yet. Click the button to analyze current feedbacks.</p>
                @endif
            </div>
        </div>

        <!-- Feedbacks List -->
        <h5 class="text-white border-bottom border-secondary pb-2 mb-3">Student Comments</h5>
        
        @forelse($feedbacks as $feedback)
            <div class="bg-dark p-3 rounded border border-secondary mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <strong class="text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= $feedback->rating ? '⭐' : '☆' }}
                        @endfor
                    </strong>
                    <small class="text-white-50">{{ $feedback->created_at->diffForHumans() }}</small>
                </div>
                
                <p class="text-white mb-1">"{{ $feedback->comment }}"</p>
            </div>
        @empty
            <div class="text-center bg-dark p-5 rounded border border-secondary text-white-50">
                No feedbacks submitted for this menu yet.
            </div>
        @endforelse

    </div>
</div>
@endsection