<?php

namespace App\Console\Commands;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('ledger:verify')]
#[Description('Verify the integrity of every wallet ledger')]
class LedgerVerify extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $problems = 0;
        foreach(Wallet::all() as $wallet){
            $prev = Transaction::GENESIS ;
            $sum = 0.00 ;

            $transactions = Transaction::where('wallet_id' , $wallet->id)->orderBy('id' , 'asc')->get();
            foreach($transactions as $transaction){
                if($transaction->prev_hash != $prev){
                    $this->error("Wallet {$wallet->id}, transaction #{$transaction->id}: chain link broken (a row was deleted, inserted or reordered)");
                    $problems += 1;
                }
                if(!hash_equals(Transaction::computeHash($transaction , $transaction->prev_hash ?? ''), $transaction->hash)){
                    $this->error("Wallet {$wallet->id}, transaction #{$transaction->id}: content was modified (hash mismatch)");
                    $problems += 1;
                }

                $prev = $transaction->hash ;

                if ($transaction->status == TransactionStatus::SUCCESS) {
                    if($transaction->type == TransactionType::INCOME){
                        $sum+= (float) $transaction->amount;
                    }
                    else{
                        $sum-= (float) $transaction->amount;
                    }
                }
            }

            if (abs($sum - (float) $wallet->balance) > 0.005) {
                $this->error("Wallet {$wallet->id}: balance {$wallet->balance} does not match ledger sum " . number_format($sum, 2));
                $problems+=1;
            }
        }

        $problems === 0 ? $this->info('Ledger OK: no tampering detected.') : $this->error("$problems problem(s) found.");
        if($problems== 0){
            $this->info('Ledger OK: no tampering detected');
        }
        else{
            $this->error("$problems problems found");
        }

        return $problems === 0 ? self::SUCCESS : self::FAILURE;
    }
}
