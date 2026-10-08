<?php

namespace App\Http\Controllers\Student;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Notifications\TransferCreditNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $student = Auth::user()->student ;
        $transactions = $student->wallet->transactions()->latest()->paginate(10);
        return view('student.wallet' , compact('student' , 'transactions'));
        
    }

    public function chargeWallet(Request $request){
        $request->validate(['amount' => ['required' , 'numeric', 'min:0', 'max:50', 'decimal:0,2']]);
        $student = Auth::user()->student ;
        DB::transaction(function() use ($student , $request){
                $student->wallet->balance += $request->amount ;
                $student->wallet->save();
                $student->wallet->transactions()->create(['type' => TransactionType::INCOME , 'amount' => $request->amount , 'status' => TransactionStatus::SUCCESS , 'note' => 'You charged your account baby']);
        });
        return back()->with('success' , 'you successfully charged your account');
    }

    public function transferMoney(Request $request){
        $request->validate(['amount' => ['required' , 'numeric', 'min:0', 'max:50', 'decimal:0,2'] , 
                            'matricola'=> ['required' , 'string' , 'exists:students,matricola' , 'not_in:'.Auth::user()->student->matricola]]);
        $sender = Auth::user()->student ;
        $reciever = Student::query()->where('matricola' , $request->matricola)->first();

        if(!$sender->wallet->hasEnoughMoney($request->amount)){
            return back()->withErrors(['amount' => 'you dont have enough money in your wallet baby']);
        }
        DB::transaction(function() use ($sender , $reciever , $request){

                $sender->wallet->balance -= $request->amount ;
                $sender->wallet->save();
                $sender->wallet->transactions()->create(['type' => TransactionType::OUTCOME , 'amount' => $request->amount , 'status' => TransactionStatus::SUCCESS , 'note' => 'You transfered money to a student' , 'ref_id' => $reciever->matricola]);
                $reciever->wallet->balance += $request->amount ;
                $reciever->wallet->save();
                $reciever->wallet->transactions()->create(['type' => TransactionType::INCOME , 'amount' => $request->amount , 'status' => TransactionStatus::SUCCESS , 'note' => 'You recieved money from a student' , 'ref_id' => $sender->matricola]);
        });

        $reciever->user->notify(new TransferCreditNotification($sender , $reciever ,$request->amount ));

        return back()->with('success' , 'you transfered money Successfully');
    }
}
