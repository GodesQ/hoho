<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantReservation extends Model
{
    use HasFactory;
    protected $table = 'restaurant_reservations';
    protected $fillable = [
        'reserved_user_id',
        'merchant_id',
        'seats',
        'reservation_date',
        'reservation_time',
        'food_ids',
        'status',
        'approved_date',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function merchant() : BelongsTo {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function reserved_user() : BelongsTo {
        return $this->belongsTo(User::class, 'reserved_user_id');
    }
}
