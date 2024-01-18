<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodCategory extends Model
{
    use HasFactory;
    protected $table = "food_categories";
    protected $fillable = ["merchant_id", "title", "description"];
    protected $hidden = ['created_at', 'updated_at'];

    public function merchant() {
        return $this->belongsTo(Merchant::class, "merchant_id");
    }
}
