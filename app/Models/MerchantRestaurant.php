<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantRestaurant extends Model
{
    use HasFactory;
    protected $table = 'merchants_restaurant';
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
        'atmosphere',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'merchant_id' => 'integer'
    ];
    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
