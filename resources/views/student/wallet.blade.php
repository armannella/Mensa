@extends('layout.master')

@section('header', 'My Wallet')

@section('content')
<div class="row mt-4 justify-content-center">
    

    <div class="col-md-4">
      
        <div class="card shadow-sm mb-4" style="background-color: #2c2c2c; border: 1px solid #444;">
            <div class="card-body text-center py-4">
                <h5 class="text-white-50 mb-3">Current Balance</h5>
                <h2 class="text-success fw-bold m-0">
                    {{ number_format($balance ?? 0) }} 
                    <span class="fs-6 text-white-50">Euro</span>
                </h2>
            </div>
        </div>

        <div class="card shadow-sm mb-4" style="background-color: #2c2c2c; border: 1px solid #444;">
            <div class="card-body">
                <h5 class="card-title text-white border-bottom border-secondary pb-2 mb-3">Charge Wallet</h5>
                <form action="{{ route('student.wallet.charge') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label text-white-50">Amount (Euro)</label>
                        <input type="number" min="0" step="0.01" max="50" name="amount" id="amount" class="form-control bg-dark text-white border-secondary" required placeholder="e.g. 5.00">
                    </div>
                    <div class="d-grid">
                        <x-button color="bgc-green">Charge Now!</x-button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mb-4" style="background-color: #2c2c2c; border: 1px solid #444;">
            <div class="card-body">
                <h5 class="card-title text-white border-bottom border-secondary pb-2 mb-3">Transfer Money</h5>
                <form action="{{ route('student.wallet.transfer') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="student_id" class="form-label text-white-50">Destination Student ID</label>
                        <input type="text" name="student_id" id="student_id" class="form-control bg-dark text-white border-secondary" required placeholder="Enter Student ID...">
                    </div>
                    <div class="mb-3">
                        <label for="transfer_amount" class="form-label text-white-50">Amount (Euro)</label>
                        <input type="number" name="amount" id="transfer_amount" class="form-control bg-dark text-white border-secondary" required placeholder="e.g. 20000">
                    </div>
                    <div class="d-grid">
                        <x-button color="bgc-orange">Transfer</x-button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white m-0">Transaction History</h4>
        </div>
        
        <x-table :headers="['Date', 'Type', 'Amount', 'Description']">
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    
                    
                    <td>
                        @if($transaction->type === 'deposit')
                            <span class="badge bg-success">Deposit</span>
                        @elseif($transaction->type === 'withdrawal')
                            <span class="badge bg-danger">Withdrawal</span>
                        @else
                            <span class="badge bg-info text-dark">Transfer</span>
                        @endif
                    </td>
                    
                    
                    <td class="{{ in_array($transaction->type, ['deposit', 'transfer_in']) ? 'text-success' : 'text-danger' }}">
                        {{ in_array($transaction->type, ['deposit', 'transfer_in']) ? '+' : '-' }}
                        {{ number_format($transaction->amount) }}
                    </td>
                    
                    <td>{{ $transaction->description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-white">No transactions found yet.</td>
                </tr>
            @endforelse
        </x-table>
    </div>

</div>
@endsection