<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelTaxPayment extends Model
{
    use HasFactory;
    protected $table = 'travel_tax_payments';
    protected $fillable = [
        "transaction_id",
        "user_id",
        "ar_number",
        "transaction_number",
        "reference_number",
        "transaction_time",
        "currency",
        "amount",
        "processing_fee",
        "discount",
        "total_amount",
        "payment_method",
        "payment_time",
        "status",
        "is_sent_api",
        "created_by",
        "created_by_role"
    ];

    public function passengers()
    {
        return $this->hasMany(TravelTaxPassenger::class, 'payment_id');
    }

    public function primary_passenger()
    {
        return $this->hasOne(TravelTaxPassenger::class, 'payment_id')->where('passenger_type', 'primary');
    }

    public function payor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
