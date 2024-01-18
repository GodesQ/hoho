<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;
    protected $table = 'promo_codes';
    protected $fillable = ['name', 'code', 'is_need_requirement', 'description', 'type', 'is_need_approval', 'discount_amount', 'discount_type'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['is_need_requirement' => 'integer', 'discount_amount' => 'integer', 'is_need_approval' => 'integer'];
}
