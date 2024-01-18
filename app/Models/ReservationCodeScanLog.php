<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationCodeScanLog extends Model
{
    use HasFactory;
    protected $table = "reservation_code_scan_logs";
    protected $fillable = ['reservation_code_id', 'scan_datetime', 'scan_type'];
    protected $hidden = ['created_at', 'updated_at'];

}
