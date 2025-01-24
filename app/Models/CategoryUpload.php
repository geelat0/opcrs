<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryUpload extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'category_upload';

}
