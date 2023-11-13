<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $table = 'organizations';
    protected $fillable = [
        'name',
        'acronym',
        'region',
        'icon',
        'featured_image',
        'images',
        'description',
        'visibility',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'integer'
    ];

    public function attractions() {
        return $this->hasMany(Attraction::class, 'organization_id')->where('status', 1);
    }

    public function stores() {
        return $this->hasMany(Merchant::class, 'organization_id')->where('type', 'Store')->where('is_active', 1)->inRandomOrder();
    }

    public function hotels() {
        return $this->hasMany(Merchant::class, 'organization_id')->where('type', 'Hotel')->where('is_active', 1)->inRandomOrder();
    }

    public function restaurants() {
        return $this->hasMany(Merchant::class, 'organization_id')->where('type', 'Restaurant')->where('is_active', 1)->inRandomOrder();
    }

    public function tour() {
        return $this->belongsTo(Tour::class, 'id', 'organization_id');
    }
}
