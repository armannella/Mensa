<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['type','amount','status','ref_id','note'];
    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'price' => 'decimal:2'
        ];
    }

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}
