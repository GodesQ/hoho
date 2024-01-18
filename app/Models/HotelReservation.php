<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelReservation extends Model
{
    use HasFactory;
    protected $table = "hotel_reservations";
    protected $fillable = ["reserved_user_id", "room_id", "number_of_pax", "reservation_date", "reservation_time", "status", "approved_date"];
    protected $hidden = ['created_at', 'updated_at'];

}
