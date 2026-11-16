<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public const GENESIS = '0000000000000000000000000000000000000000000000000000000000000000';

    protected $fillable = ['type','amount','status','ref_id','note'];
    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'amount' => 'decimal:2'
        ];
    }

    protected static function booted(): void
    {
        
        static::creating(function (Transaction $t) {
            $t->updateTimestamps();
            $t->amount = round((float) $t->amount, 2); 

            $last = static::where('wallet_id', $t->wallet_id)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $t->prev_hash = $last?->hash ?? self::GENESIS;
            $t->hash = self::computeHash($t, $t->prev_hash);
        });

        static::updating(fn () => throw new \LogicException('Ledger is append-only.'));
        static::deleting(fn () => throw new \LogicException('Ledger is append-only.'));
    }

    public static function computeHash(self $t, string $prevHash): string
    {
        $payload = json_encode([
            $t->wallet_id,
            $t->type->value,
            number_format((float) $t->amount, 2, '.', ''),
            $t->status->value,
            (string) ($t->ref_id ?? ''),
            $t->note ?? '',
            $t->created_at->format('Y-m-d H:i:s'),
            $prevHash,
        ]);

        return hash_hmac('sha256', $payload, config('app.ledger_key'));
    }

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}
