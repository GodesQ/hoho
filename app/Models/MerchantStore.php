<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantStore extends Model
{
    use HasFactory;
    protected $table = 'merchants_store';
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
    protected $hidden = ['created_at', 'updated_at'];
    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
