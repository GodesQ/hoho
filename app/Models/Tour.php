<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;
    protected $table = 'tours';
    protected $fillable = [
        'capacity',
        'under_age_limit',
        'over_age_limit',
        'tour_provider_id',
        'package_tour',
        'name',
        'type',
        'description',
        'contact_no',
        'featured_image',
        'images',
        'operating_hours',
        'is_cancellable',
        'is_refundable',
        'status',
        'links',
        'minimum_capacity',
        'tour_itinerary',
        'tour_inclusions',
        'attractions_assignments_ids'
    ];
}
