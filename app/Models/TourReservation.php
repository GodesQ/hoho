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

    protected $casts = [
        'tour_id' => 'integer',
        'amount' => 'integer',
        'number_of_pass' => 'integer',
        'order_transaction_id' => 'integer'
    ];

    protected $appends = ['passengers'];

    public function user() {
        return $this->belongsTo(User::class, 'reserved_user_id');
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class, 'order_transaction_id');
    }

    public function tour() {
        return $this->hasOne(Tour::class, 'id', 'tour_id');
    }

    public function getPassengersAttribute() {
        $passenger_ids = json_decode($this->passenger_ids, true); // Passing true as the second argument to get an associative array

        if (is_array($passenger_ids) && !empty($passenger_ids)) {
            $data = User::select('id', 'email', 'username', 'firstname', 'lastname')->whereIn('id', $passenger_ids)
                ->get()
                ->toArray();
            if (!empty($data)) {
                return $data;
            }
        }
    }
}
