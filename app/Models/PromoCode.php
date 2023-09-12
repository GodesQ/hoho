<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;
    protected $table = 'promo_codes';
    protected $fillable = ['name', 'code', 'is_need_requirement', 'description'];

    protected $casts = ['is_need_requirement' => 'integer'];
}
