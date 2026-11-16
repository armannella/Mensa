<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferOtp extends Model
{
    protected $fillable = ['user_id', 'to_matricola', 'amount', 'code_hash', 'expires_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public static function hashCode(string $code): string
    {
        return hash_hmac('sha256', $code, config('app.key'));
    }
}
