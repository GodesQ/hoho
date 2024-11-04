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
        "number_of_rooms",
        "amenities",
        "description",
        "other_images",
        "product_categories",
        "is_cancellable",
        "is_refundable",
        "is_active",
    ];

    protected $casts = [
        "merchant_id" => "integer",
        "is_cancellable" => "integer",
        "is_active" => "integer",
        "price" => "double",
        "available_pax" => "integer"
    ];

    protected $hidden = ['created_at', 'updated_at'];
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, "merchant_id");
    }
}
