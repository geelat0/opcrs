<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadLogs extends Model
{
    use HasFactory;

    protected $table = 'upload_logs';


    public function upload(){
        return $this->belongsTo(Upload::class, 'upload_id', 'id')->withTrashed();
    }
}
