<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    protected $table = 'announcements';
    
    protected $fillable = [
        'type',
        'name',
        'announcement_image',
        'message',
        'is_active',
        'is_important'
    ];

    protected $casts = [
        'is_active' => 'integer',
        'is_important' => 'integer'
    ];
}
