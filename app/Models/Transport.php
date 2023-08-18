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
}
