<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;
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
        'bypass_days',
        'disabled_days',
        'minimum_pax',
        'description',
        'contact_no',
        'featured_image',
        'images',
        'interests',
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
        'transport_id',
        'organization_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'merchant_id' => 'integer',
        'tour_duration' => 'integer',
        'transport_id' => 'integer',
        'organization_id' => 'integer',
        'price' => 'double',
        'bypass_days' => 'integer',
        'minimum_pax' => 'integer',
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
            $data = Attraction::whereIn('id', $attraction_ids)
                ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $attraction_ids) . ")"))
                ->get();

            if (!empty($data)) {
                return $data;
            }
        }

        return [];
    }

    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function transport() {
        return $this->hasOne(Transport::class, 'id', 'transport_id')->select('id', 'available_seats', 'route', 'capacity', 'tour_assigned_id', 'tour_assignment_ids', 'latitude', 'longitude', 'name', 'current_location', 'next_location', 'previous_location');
    }

    public function tour_provider() {
        return $this->hasOne(MerchantTourProvider::class, 'id', 'tour_provider_id');
    }

    public function timeslots() {
        return $this->hasMany(TourTimeslot::class, 'tour_id');
    } 

    public function feedbacks() {
        return $this->hasMany(TourFeedback::class, 'tour_id');
    }

    public function feedback() {
        return $this->hasOne(TourFeedback::class, 'tour_id');
    }
}
