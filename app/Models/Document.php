<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    
    protected $fillable = ['file_path' , 'document_type_id'];
    public function documentType(){
        return $this->belongsTo(DocumentType::class);
    }
    public function student(){
        return $this->belongsTo(Student::class);
    }
}
