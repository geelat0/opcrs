<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organizational extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'org_otc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organizational_outcome',
        'status',
        'created_by',
    ];

    public function successIndicators()
    {
        return $this->hasMany(SuccessIndicator::class, 'org_id');
    }

}
