<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['title' , 'description' , 'is_required'];
    
    public function documents(){
        return $this->hasMany(Document::class);
    }
}
