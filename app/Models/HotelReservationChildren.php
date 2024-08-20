<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelReservationChildren extends Model
{
    use HasFactory;
    protected $table = "hotel_reservation_children";
    protected $fillable = ["reservation_id", "age"];
}
