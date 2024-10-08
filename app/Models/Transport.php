<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;
    protected $table = 'transports';
    protected $fillable = [
        'route',
        'available_seats',
        'capacity',
        'duration',
        'transport_provider_id',
        'operator_id',
        'tour_assignment_ids',
        'tour_assigned_id',
        'hub_id',
        'latitude',
        'longitude',
        'name',
        'type',
        'description',
        'contact_email',
        'operating_hours',
        'travel_cards',
        'price',
        'arrival_date',
        'departure_date',
        'icon',
        'current_location',
        'next_location',
        'previous_location',
        'is_cancellable',
        'is_refundable',
        'is_active',
        'is_tracking',
        'current_tracking_token'
    ];

    protected $casts = [
        'available_seats' => 'integer',
        'capacity' => 'integer',
        'duration' => 'integer',
        'transport_provider_id' => 'integer',
        'operator_id' => 'integer',
        'tour_assigned_id' => 'integer',
        'hub_id' => 'integer',
        'price' => 'integer',
        'is_cancellable' => 'integer',
        'is_refundable' => 'integer',
        'is_active' => 'integer'
    ];

    protected $hidden = ['created_at', 'updated_at'];


    public function getTourAssignmentIdsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function assigned_tour() {
        return $this->hasOne(Tour::class, 'id', 'tour_assigned_id')->select('id', 'attractions_assignments_ids');
    }

    public function next_tour_attraction() {
        return $this->hasOne(Attraction::class, 'id', 'next_location');
    }

    public function current_tour_attraction() {
        return $this->hasOne(Attraction::class, 'id', 'current_location');
    }

    public function previous_tour_attraction() {
        return $this->hasOne(Attraction::class, 'id', 'previous_location');
    }
}
