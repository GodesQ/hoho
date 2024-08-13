<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelReservation extends Model
{
    use HasFactory;
    protected $table = "hotel_reservations";
    protected $fillable = [
        "reference_number", 
        "transaction_id", 
        "reserved_user_id", 
        "room_id",
        "number_of_pax",
        "checkin_date",
        "checkout_date",
        "adult_quantity",
        "children_quantity",
        "status", 
        "payment_status", 
        "approved_date"
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'reserved_user_id' => 'integer',
        'room_id' => 'integer',
        'number_of_pax' => 'integer',
    ];

    protected $dates = ['checkin_date', 'checkout_date', 'approved_date'];

    public function reserved_user() : BelongsTo {
        return $this->belongsTo(User::class, 'reserved_user_id');
    }

    public function room() : BelongsTo {
        return $this->belongsTo(Room::class,'room_id');
    }

    public function transaction() : BelongsTo {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function children_age() : HasMany {
        return $this->hasMany(HotelReservationChildren::class, 'reservation_id')->select('reservation_id', 'age');
    }

}
