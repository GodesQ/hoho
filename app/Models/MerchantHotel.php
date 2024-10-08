<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantHotel extends Model
{
    use HasFactory;
    protected $table = 'merchants_hotel';
    protected $fillable = [
        'merchant_id',
        'brochure',
        'images',
        'payment_options',
        'tags',
        'links',
        'interests',
        'contact_email',
        'contact_number',
        'location',
        'business_hours',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'merchant_id' => 'integer'
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
