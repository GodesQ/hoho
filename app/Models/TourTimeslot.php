<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourTimeslot extends Model
{
    use HasFactory;
    protected $table = 'tour_timeslots';
    protected $fillable = ['tour_id', 'start_time', 'end_time'];

    public function tour() {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
