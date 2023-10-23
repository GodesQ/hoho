<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantHotelRoom extends Model
{
    use HasFactory;
    protected $table = 'hotel_rooms';
    protected $fillable = [
        'room_name',
        'room_type',
        'description',
        'location_of_room',
        'room_rate_per_night',
        'is_cancellable',
        'is_refundable',
        'thumbnail_image',
        'product_categories',
    ];
}
