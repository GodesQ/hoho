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
        'stock',
        'other_images',
        'is_best_seller',
        'is_active'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'merchant_id' => 'integer',
        'price' => 'double',
        'stock' => 'integer',
        'is_best_seller' => 'integer',
        'is_active' => 'integer',
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class, "merchant_id");
    }
}
