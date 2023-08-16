<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attraction extends Model
{
    use HasFactory;
    protected $table = 'attractions';
    protected $fillable = [
        'name',
        'attraction_provider',
        'featured_image',
        'images',
        'contact_no',
        'description',
        'interests',
        'youtube_id',
        'product_categories',
        'price',
        'operating_hours',
        'is_cancellable',
        'is_refundable',
        'status',
    ];
}
