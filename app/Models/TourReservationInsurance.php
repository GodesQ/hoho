<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourReservationInsurance extends Model
{
    use HasFactory;
    protected $table = "tour_reservation_insurances";
    protected $fillable = [
        "reservation_id",
        "insurance_id",
        "type_of_plan",
        "total_insurance_amount",
        "number_of_pax"
    ];
}
