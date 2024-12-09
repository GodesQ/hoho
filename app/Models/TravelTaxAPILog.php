<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelTaxAPILog extends Model
{
    use HasFactory;
    protected $table = "travel_tax_api_logs";
    protected $fillable = ['travel_tax_id', 'status_code', 'response', 'date_of_submission'];
}
