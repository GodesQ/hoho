<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantTourProvider extends Model
{
    use HasFactory;
    protected $table = 'merchants_tour_providers';
    protected $fillable = [
        'merchant_id',
        'images',
        'payment_options',
        'tags',
        'links',
        'interests',
        'contact_number',
        'contact_email',
        'location',
        'business_hours',
        'address',
    ];

    protected $casts = [
        'merchant_id' => 'integer'
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
