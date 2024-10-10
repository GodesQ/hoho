<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourReservation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tour_reservations';

    protected $fillable = [
        'tour_id',
        'type',
        'total_additional_charges',
        'discount',
        'sub_amount',
        'amount',
        'reserved_user_id',
        'passenger_ids',
        'reference_code',
        'order_transaction_id',
        'start_date',
        'end_date',
        'status',
        'number_of_pass',
        'ticket_pass',
        'payment_method',
        'referral_merchant_id',
        'referral_code',
        'promo_code',
        'requirement_file_path',
        'discount_amount',
        'has_insurance',
        'created_by',
        'created_user_type',
        'updated_by'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'tour_id' => 'integer',
        'total_additional_charges' => 'double',
        'discount' => 'double',
        'sub_amount' => 'double',
        'amount' => 'double',
        'number_of_pass' => 'integer',
        'order_transaction_id' => 'integer',
        'reserved_user_id' => 'integer',
        'discount_amount' => 'double'
    ];

    protected $appends = ['passengers'];

    public function user()
    {
        return $this->belongsTo(User::class, 'reserved_user_id');
    }

    public function customer_details()
    {
        return $this->hasOne(TourReservationCustomerDetail::class, 'tour_reservation_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'order_transaction_id');
    }

    public function tour()
    {
        return $this->hasOne(Tour::class, 'id', 'tour_id')->select('id', 'name', 'featured_image', 'capacity', 'type', 'price', 'bracket_price_one', 'bracket_price_two', 'bracket_price_three');
    }

    public function reservation_codes()
    {
        return $this->hasMany(ReservationUserCode::class, 'reservation_id')->select('id', 'reservation_id', 'code', 'scan_count', 'start_datetime', 'end_datetime', 'status');
    }

    public function referral()
    {
        return $this->hasOne(Referral::class, 'referral_code', 'referral_code');
    }

    public function feedbacks()
    {
        return $this->hasMany(TourFeedback::class, 'reservation_id');
    }

    public function feedback()
    {
        return $this->hasOne(TourFeedback::class, 'reservation_id');
    }

    public function reservation_insurance()
    {
        return $this->hasOne(TourReservationInsurance::class, 'reservation_id');
    }

    public function getPassengersAttribute()
    {
        $passenger_ids = $this->passenger_ids ? json_decode($this->passenger_ids, true) : null; // Passing true as the second argument to get an associative array

        if (is_array($passenger_ids) && ! empty($passenger_ids)) {
            $data = User::select('id', 'email', 'username', 'firstname', 'lastname')->whereIn('id', $passenger_ids)
                ->get()
                ->toArray();

            if (! empty($data)) {
                return $data;
            }
        }

        return [];
    }
}
