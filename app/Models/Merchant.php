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
        'is_active'
    ];

    public function hotels() {
        return $this->hasMany(MerchantHotel::class, 'merchant_id');
    }

    public function stores() {
        return $this->hasMany(MerchantHotel::class, 'merchant_id');
    }

}
