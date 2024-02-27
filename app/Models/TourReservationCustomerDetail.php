<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourReservationCustomerDetail extends Model
{
    use HasFactory;

    protected $table = "tour_reservation_customer_details";

    protected $fillable = [
        "tour_reservation_id",
        "firstname",
        "lastname",
        "email",
        "contact_no",
        "address",
    ];
}
