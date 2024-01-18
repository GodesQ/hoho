<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    protected $table = 'referrals';

    protected $fillable = [
        'referral_name',
        'referral_code',
        'merchant_id',
        'qrcode',
        'commision'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'merchant_id' => 'integer',
        'commision' => 'integer'
    ];

}
