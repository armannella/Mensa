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
use App\Models\TransferOtp;
use App\Services\TelegramService;

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

    public function transferMoney(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0', 'max:50', 'decimal:0,2'],
            'matricola' => ['required', 'string', 'exists:students,matricola', 'not_in:' . Auth::user()->student->matricola],
        ]);

        $user = Auth::user();

        if (!$user->telegram_chat_id) {
            return back()->withErrors(['amount' => 'Link your Telegram account in Profile first.']);
        }
        if (!$user->student->wallet->hasEnoughMoney($request->amount)) {
            return back()->withErrors(['amount' => 'you dont have enough money in your wallet baby']);
        }

        
        TransferOtp::where('user_id', $user->id)->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp = TransferOtp::create([
            'user_id' => $user->id,
            'to_matricola' => $request->matricola,
            'amount' => $request->amount,
            'code_hash' => TransferOtp::hashCode($code),
            'expires_at' => now()->addMinutes(5),
        ]);

        $sent = $telegram->send(
            $user->telegram_chat_id,
            "Transfer code: {$code}\nAmount: {$request->amount} EUR\nTo matricola: {$request->matricola}\nValid for 5 minutes. Never share this code."
        );

        if (!$sent) {
            $otp->delete();
            return back()->withErrors(['amount' => 'Could not send the Telegram message. Try again.']);
        }

        session(['transfer_otp_id' => $otp->id]);
        return redirect()->route('student.wallet.transfer.confirm');
    }


    private function currentOtp(): ?TransferOtp
    {
        return TransferOtp::where('id', session('transfer_otp_id'))
            ->where('user_id', Auth::id())
            ->whereNull('used_at')
            ->first();
    }

    public function showConfirm()
    {
        $otp = $this->currentOtp();
        if (!$otp) {
            return redirect()->route('student.wallet.index')->withErrors(['amount' => 'No pending transfer.']);
        }
        return view('student.transfer-confirm', compact('otp'));
    }

    public function confirmTransfer(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $otp = $this->currentOtp();
        if (!$otp) {
            return redirect()->route('student.wallet.index')->withErrors(['amount' => 'No pending transfer. Start again.']);
        }

        if ($otp->expires_at->isPast() || $otp->attempts >= 3) {
            $otp->delete();
            return redirect()->route('student.wallet.index')->withErrors(['amount' => 'Code expired or too many attempts. Start again.']);
        }

        if (!hash_equals($otp->code_hash, TransferOtp::hashCode($request->code))) {
            $otp->increment('attempts');
            return back()->withErrors(['code' => 'Wrong code. Attempts left: ' . (3 - $otp->attempts)]);
        }

        
        $sender = Auth::user()->student;
        $reciever = Student::where('matricola', $otp->to_matricola)->first();
        $amount = (float) $otp->amount;

        $done = DB::transaction(function () use ($otp, $sender, $reciever, $amount) {
            
            $consumed = TransferOtp::where('id', $otp->id)->whereNull('used_at')->update(['used_at' => now()]);
            if (!$consumed || !$reciever) {
                return false;
            }

            $sender->wallet->refresh();
            if (!$sender->wallet->hasEnoughMoney($amount)) {
                return false;
            }

            $sender->wallet->balance -= $amount;
            $sender->wallet->save();
            $sender->wallet->transactions()->create(['type' => TransactionType::OUTCOME, 'amount' => $amount, 'status' => TransactionStatus::SUCCESS, 'note' => 'You transfered money to a student', 'ref_id' => $reciever->matricola]);
            $reciever->wallet->balance += $amount;
            $reciever->wallet->save();
            $reciever->wallet->transactions()->create(['type' => TransactionType::INCOME, 'amount' => $amount, 'status' => TransactionStatus::SUCCESS, 'note' => 'You recieved money from a student', 'ref_id' => $sender->matricola]);
            return true;
        });

        session()->forget('transfer_otp_id');

        if (!$done) {
            return redirect()->route('student.wallet.index')->withErrors(['amount' => 'Transfer failed (not enough balance or code already used).']);
        }

        $reciever->user->notify(new TransferCreditNotification($sender, $reciever, $amount));
        return redirect()->route('student.wallet.index')->with('success', 'you transfered money Successfully');
    }
}
