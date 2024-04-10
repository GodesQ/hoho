<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayoverTourReservationDetail extends Model
{
    use HasFactory;
    protected $table = "layover_tour_reservation_details";
    protected $fiilable = [
        "reservation_id",
        "arrival_datetime",
        "flight_to",
        "departure_datetime",
        "flight_from",,
        "passport_number",
        "special_instruction",
    ];
}
