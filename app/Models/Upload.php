<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'upload';
    

    public function upload_category()
    {
        return $this->belongsTo(CategoryUpload::class, 'upload_category_id', 'id');
    }
}
