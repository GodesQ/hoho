<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourBadge extends Model
{
    use HasFactory;
    protected $fillable = [
        'tour_id',
        'badge_name',
        'badge_code',
        'badge_img',
        'location',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'tour_id'=> 'integer',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function tour() {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
