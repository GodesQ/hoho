<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
        'reference_no',
        'or_no',
        'transaction_by_id',
        'sub_amount',
        'total_additional_charges',
        'total_discount',
        'transaction_type',
        'payment_amount',
        'total_amount',
        'type',
        'additional_charges',
        'payment_status',
        'resolution_status',
        'payment_details',
        'payment_url',
        'order_date',
        'transaction_date',
        'payment_date',
        'remarks',
        'payment_provider_fee',
        'aqwire_transactionId',
        'aqwire_referenceId',
        'aqwire_paymentMethodCode',
        'aqwire_totalAmount'
    ];

    protected $casts = [
        'transaction_by_id' => 'integer',
        'payment_amount' => 'integer',
        'total_amount' => 'double',
        'sub_amount' => 'double',
        'total_additional_charges' => 'double',
        'total_discount' => 'double',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'transaction_by_id');
    }

    public function items()
    {
        if ($this->transaction_type == 'book_tour')
        {
            return $this->hasMany(TourReservation::class, 'order_transaction_id');
        } else
        {
            return $this->hasMany(TourReservation::class, 'order_transaction_id');
        }
    }
}
