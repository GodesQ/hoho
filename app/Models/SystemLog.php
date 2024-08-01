<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    use HasFactory;
    protected $table = "system_logs";
    protected $fillable = ['user_id', 'action', 'model', 'context'];

    public function admin() : BelongsTo {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
