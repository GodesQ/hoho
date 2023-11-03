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
        'interest_ids',
        'youtube_id',
        'product_category_ids',
        'price',
        'operating_hours',
        'organization_id',
        'address',
        'latitude',
        'longitude',
        'is_cancellable',
        'is_refundable',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'attraction_provider' => 'integer',
        'is_cancellable' => 'integer',
        'is_refundable' => 'integer',
        'is_featured' => 'integer',
        'status' => 'integer'
    ];

    public function organization() {
        return $this->hasOne(Organization::class, 'id', 'organization_id');
    }
}
