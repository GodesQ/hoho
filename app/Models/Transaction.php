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
        'payment_amount',
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
        'aqwire_transactionId',
        'aqwire_referenceId',
        'aqwire_paymentMethodCode',
        'aqwire_totalAmount'
    ];

    protected $casts = [
        'transaction_by_id' => 'integer',
        'payment_amount' => 'integer',
    ];

    public function user() {
        return $this->hasOne(User::class, 'id', 'transaction_by_id');
    }
}
