<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelTaxPassenger extends Model
{
    use HasFactory;
    protected $table = "travel_tax_passengers";
    protected $fillable = [
        "payment_id",
        "firstname",
        "lastname",
        "middlename",
        "suffix",
        "passport_number",
        "ticket_number",
        "class",
        "mobile_number",
        "email_address",
        "destination",
        "departure_date",
        "passenger_type",
        "amount",
    ];

    public function payment() {
        return $this->belongsTo(TravelTaxPayment::class, "payment_id");
    }
}
