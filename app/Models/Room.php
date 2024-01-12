<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $table = "rooms";
    protected $fillable = [
        "merchant_id",
        "room_name",
        "image",
        "price",
        "available_pax",
        "amenities",
        "description",
        "other_images",
        "product_categories",
        "is_cancellable",
        "is_refundable",
        "is_active",
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class, "merchant_id");
    }
}
