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
        'status'
    ];

    protected $casts = [    
        'scan_count' => 'integer'
    ];

    public function tour_reservation() {
        return $this->hasOne(TourReservation::class, 'id', 'reservation_id');
    }
}
