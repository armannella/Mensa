<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('ledger:seal')]
#[Description('Add hash chain to legacy transactions (run once)')]
class LedgerSeal extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (Wallet::all() as $wallet) {
            $txs = Transaction::where('wallet_id', $wallet->id)->orderBy('id')->get();

            if ($txs->whereNotNull('hash')->isNotEmpty()) {
                $this->warn("Wallet {$wallet->id} already sealed, skipped.");
                continue;
            }

            $prev = Transaction::GENESIS;
            foreach ($txs as $t) {
                $hash = Transaction::computeHash($t, $prev);
                DB::table('transactions')->where('id', $t->id)
                    ->update(['prev_hash' => $prev, 'hash' => $hash]);
                $prev = $hash;
            }
            $this->info("Wallet {$wallet->id}: {$txs->count()} transactions sealed.");
        }
    }
}
