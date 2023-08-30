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
        'capacity',
        'duration',
        'transport_provider_id',
        'operator_id',
        'tour_assignment_ids',
        'tour_assigned_id',
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
    ];

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
