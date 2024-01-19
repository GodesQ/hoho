<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelReservation extends Model
{
    use HasFactory;
    protected $table = "hotel_reservations";
    protected $fillable = ["reserved_user_id", "room_id", "number_of_pax", "reservation_date", "reservation_time", "status", "approved_date"];
    protected $hidden = ['created_at', 'updated_at'];

    public function reserved_user() : BelongsTo {
        return $this->belongsTo(User::class, 'reserved_user_id');
    }

    public function room() : BelongsTo {
        return $this->belongsTo(Room::class,'room_id');
    }

}
