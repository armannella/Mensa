<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['balance'];
    
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2'
        ];
    }

    public function student(){
        return $this->belongsTo(Wallet::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function hasEnoughMoney(float $amount){
        if($this->balance >= $amount) {
            return true;
        }
        return false;
    }
}
