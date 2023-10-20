<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTourBadge extends Model
{
    use HasFactory;
    protected $table = 'users_tour_badges';
    protected $fillable = ['user_id', 'tour_reservation_id', 'badge_id', 'status'];

    protected $casts = [
        'user_id'=> 'integer',
        'tour_reservation_id' => 'integer',
        'badge_id' => 'integer',
    ];

    public function tour_badge() {
        return $this->hasOne(TourBadge::class, 'id', 'badge_id');
    }
}
