<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourUnavailableDate extends Model
{
    use HasFactory;
    protected $table = "tour_unavailable_dates";
    protected $fillable = ['unavailable_date', 'reason'];
}
