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
        'price',
        'bracket_price_one',
        'bracket_price_two',
        'bracket_price_three',
        'attractions_assignments_ids',
        'start_date_duration',
        'end_date_duration',
        'tour_duration',
        'transport_id'
    ];

    protected $casts = [
        'merchant_id' => 'integer',
        'tour_duration' => 'integer',
        'transport_id' => 'integer',
        'price' => 'double',
        'bracket_price_one' => 'double',
        'bracket_price_two' => 'double',
        'bracket_price_three' => 'double',
        'is_cancellable' => 'integer',
        'is_refundable' => 'integer',
        'under_age_limit' => 'integer',
        'over_age_limit' => 'integer',
        'tour_provider_id' => 'integer',
        'minimum_capacity' => 'integer',
        'status' => 'integer',
        'capacity' => 'integer'
    ];

    protected $appends = ['attractions'];

    public function getAttractionsAttribute() {
        $attraction_ids = json_decode($this->attractions_assignments_ids, true); // Passing true as the second argument to get an associative array

        if (is_array($attraction_ids) && !empty($attraction_ids)) {
            $data = Attraction::select('id', 'name', 'address', 'latitude', 'longitude', 'featured_image', 'youtube_id')->whereIn('id', $attraction_ids)
                ->get()
                ->toArray();
            if (!empty($data)) {
                return $data;
            }
        }
    }

    public function transport() {
        return $this->hasOne(Transport::class, 'id', 'transport_id')->select('id', 'route', 'capacity', 'tour_assigned_id', 'tour_assignment_ids', 'latitude', 'longitude', 'name', 'current_location', 'next_location', 'previous_location');
    }

    public function tour_provider() {
        return $this->hasOne(MerchantTourProvider::class, 'id', 'tour_provider_id');
    }
}
