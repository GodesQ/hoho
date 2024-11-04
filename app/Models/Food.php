<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    protected $table = "foods";
    protected $fillable = ["merchant_id", "title", "image", "description", "price", "food_category_id", "note", "other_images", "is_active"];
    protected $hidden = ['created_at', 'updated_at'];
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, "merchant_id");
    }

    public function food_category()
    {
        return $this->belongsTo(FoodCategory::class, "food_category_id");
    }
}
