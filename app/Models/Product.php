<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $fillable = [
        'merchant_id',
        'name',
        'image', 
        'description',
        'price',
        'quantity',
        'other_images',
        'is_active'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'merchant_id' => 'integer',
        'price' => 'double',
        'quantity' => 'integer'
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class, "merchant_id");
    }
}
