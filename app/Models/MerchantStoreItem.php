<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantStoreItem extends Model
{
    use HasFactory;
    protected $table = 'store_items';
    protected $fillable = [
        'merchant_store_id',
        'featured_image',
        'name',
        'type',
        'price',
        'category',
        'unit_of_measure',
        'is_tracked',
        'description',
        'is_cancellable',
        'is_refundable',
        'status',
    ];
}
