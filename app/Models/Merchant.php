<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;
    protected $table = 'merchants';
    protected $fillable = [
        'name',
        'type',
        'featured_image',
        'nature_of_business',
        'organization_id',
        'code',
        'description',
        'address',
        'latitude',
        'longitude',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'integer',
        'organization_id' => 'integer'
    ];

    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function hotels() {
        return $this->hasMany(MerchantHotel::class, 'merchant_id');
    }

    public function stores() {
        return $this->hasMany(MerchantStore::class, 'merchant_id');
    }

    public function store_info() {
        return $this->hasOne(MerchantStore::class, 'merchant_id');
    }

    public function restaurant_info() {
        return $this->hasOne(MerchantRestaurant::class, 'merchant_id');
    }

    public function hotel_info() {
        return $this->hasOne(MerchantHotel::class, 'merchant_id');
    }

}
