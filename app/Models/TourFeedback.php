<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourFeedback extends Model
{
    use HasFactory;
    protected $table = "tour_feedbacks";
    protected $fillable = ['customer_id', 'tour_id', 'message', 'category_one_rate', 'category_two_rate', 'category_three_rate', 'total_rate'];

    public function customer() {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function tour() {
        return $this->belongsTo(Tour::class,'tour_id');
    }
}
