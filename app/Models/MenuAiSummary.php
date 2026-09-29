<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAiSummary extends Model
{
    use HasFactory;
    
    protected $fillable = ['menu_id', 'summary'];

    public function menu() {
        return $this->belongsTo(Menu::class);
    }
}
