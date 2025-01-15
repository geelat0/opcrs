<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quarter_logs extends Model
{
    use HasFactory;

    protected $table = 'quarter_logs';

    protected $fillable = [
        'indicator_id',
        'Q1_target',
        'Q2_target',
        'Q3_target',
        'Q4_target',
        'created_by',
        'updated_by'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Dynamically add region targets
        foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
            foreach (['Albay', 'Camarines_Sur', 'Camarines_Norte', 'Catanduanes', 'Masbate', 'Sorsogon'] as $region) {
                $this->fillable[] = "{$region}_target_{$quarter}";
            }
        }
    }

    public function indicator()
    {
        return $this->belongsTo(SuccessIndicator::class, 'quarter_logs_id', 'id');
    }
}
