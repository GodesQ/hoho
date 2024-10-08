<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationUserCode extends Model
{
    use HasFactory;
    protected $table = 'reservation_user_codes';

    protected $fillable = [
        'reservation_id',
        'code',
        'scan_count',
        'start_datetime',
        'end_datetime',
        'status',
        'current_hub',
        'current_attraction'
    ];

    protected $casts = [    
        'reservation_id' => 'integer',
        'scan_count' => 'integer',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'current_hub' => 'integer',
        'current_attraction' => 'integer'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function tour_reservation() {
        return $this->hasOne(TourReservation::class, 'id', 'reservation_id');
    }
}
