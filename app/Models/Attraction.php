<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

class Attraction extends Model
{
    use HasFactory;
    protected $table = 'attractions';
    protected $fillable = [
        'name',
        'attraction_provider',
        'featured_image',
        'images',
        'contact_no',
        'description',
        'interest_ids',
        'youtube_id',
        'product_category_ids',
        'price',
        'operating_hours',
        'organization_id',
        'address',
        'latitude',
        'longitude',
        'is_cancellable',
        'is_refundable',
        'is_featured',
        'status',
        'nearest_attraction_ids',
        'nearest_hotel_ids',
        'nearest_store_ids',
        'nearest_restaurant_ids'
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'attraction_provider' => 'integer',
        'is_cancellable' => 'integer',
        'is_refundable' => 'integer',
        'is_featured' => 'integer',
        'status' => 'integer'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['nearest_attractions', 'nearest_stores', 'nearest_hotels', 'nearest_restaurants'];

    protected static $loadingNearestAttractions = false;

    public function getNearestAttractionsAttribute() {
        // Check if already loading the nearest attractions to prevent infinite loop
        if (self::$loadingNearestAttractions) {
            return [];
        }
    
        self::$loadingNearestAttractions = true;
    
        $nearest_attractions = $this->nearest_attraction_ids ? json_decode($this->nearest_attraction_ids, true) : [];
    
        if (is_array($nearest_attractions) && !empty($nearest_attractions)) {
            $data = Attraction::whereIn('id', $nearest_attractions)
                ->get()
                ->toArray();
    
            self::$loadingNearestAttractions = false; // Reset the flag after loading
    
            return $data;
        }
    
        self::$loadingNearestAttractions = false; // Reset the flag if no nearest attractions found
    
        return [];
    }

    public function getNearestStoresAttribute() {
        $nearest_stores = $this->nearest_store_ids ? json_decode($this->nearest_store_ids, true) : [];

        if (is_array($nearest_stores) && !empty($nearest_stores)) {
            $data = Merchant::whereIn('id', $nearest_stores)
                ->with('store_info')
                ->get()
                ->toArray();
            if (!empty($data)) {
                return $data;
            }
        }
    }

    public function getNearestRestaurantsAttribute() {
        $nearest_restaurants = $this->nearest_restaurant_ids ? json_decode($this->nearest_restaurant_ids, true) : [];
        
        if (is_array($nearest_restaurants) && !empty($nearest_restaurants)) {
            $data = Merchant::whereIn('id', $nearest_restaurants)
                ->with('restaurant_info')
                ->get()
                ->toArray();
            if (!empty($data)) {
                return $data;
            }
        }
    }

    public function getNearestHotelsAttribute() {
        $nearest_hotels = $this->nearest_hotel_ids ? json_decode($this->nearest_hotel_ids, true) : [];
        
        if (is_array($nearest_hotels) && !empty($nearest_hotels)) {
            $data = Merchant::whereIn('id', $nearest_hotels)
                ->with('hotel_info')
                ->get()
                ->toArray();
            if (!empty($data)) {
                return $data;
            }
        }
    }

    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization_id');
    }
}
