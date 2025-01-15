<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginModel extends Model
{
    use HasFactory;

    protected $table = 'login_in';

    // public $timestamps = false;


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
