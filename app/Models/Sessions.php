<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    use HasFactory;
    protected $table = 'sessions';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
