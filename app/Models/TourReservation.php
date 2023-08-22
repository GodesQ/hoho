<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourReservation extends Model
{
    use HasFactory;
    protected $table = 'tour_reservations';
    protected $fillable = [
        'tour_id',
        'type',
        'amount',
        'reserved_user_id',
        'passenger_ids',
        'reference_code',
        'order_transaction_id',
        'start_date',
        'end_date',
        'status',
        'number_of_pass',
        'ticket_pass'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'reserved_user_id');
    }

    public function tour() {
        return $this->hasOne(Tour::class, 'id', 'tour_id');
    }
}
