<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourFeedback extends Model
{
    use HasFactory;
    protected $table = "tour_feedbacks";
    protected $fillable = ['customer_id', 'reservation_id', 'tour_id', 'message', 'category_one_rate', 'category_two_rate', 'category_three_rate', 'category_four_rate', 'category_five_rate', 'category_six_rate', 'total_rate'];

    protected $casts = [
        'customer_id' => 'integer',
        'reservation_id' => 'integer',
        'tour_id' => 'integer',
        'category_one_rate' => 'integer',
        'category_two_rate' => 'integer',
        'category_three_rate' => 'integer',
        'category_four_rate' => 'integer',
        'category_five_rate' => 'integer',
        'category_six_rate' => 'integer',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function customer() {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function tour() {
        return $this->belongsTo(Tour::class,'tour_id')->select('id', 'name', 'type', 'capacity', 'featured_image');
    }

    public function tour_reservation() {
        return $this->belongsTo(TourReservation::class, 'reservation_id');
    }
}
