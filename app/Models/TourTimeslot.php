<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourTimeslot extends Model
{
    use HasFactory;
    protected $table = 'tour_timeslots';
    protected $fillable = ['tour_id', 'start_time', 'end_time'];

    protected $hidden = ['created_at','updated_at'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    public function tour() {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
