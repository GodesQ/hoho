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
        'featured_arrangement_number',
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
            $data = Attraction::select('id', 'name', 'featured_image', 'organization_id', 'latitude', 'longitude', 'address', 'status')
                ->whereIn('id', $nearest_attractions)
                ->get();

            $data->each->setAppends([]);
    
            self::$loadingNearestAttractions = false; // Reset the flag after loading
    
            return $data;
        }
    
        self::$loadingNearestAttractions = false; // Reset the flag if no nearest attractions found
    
        return [];
    }

    public function getNearestStoresAttribute() {
        $nearest_stores = $this->nearest_store_ids ? json_decode($this->nearest_store_ids, true) : [];
    
        if (is_array($nearest_stores) && !empty($nearest_stores)) {
            $data = Merchant::select('id', 'name', 'featured_image', 'organization_id', 'address', 'latitude', 'longitude', 'is_active')
                ->whereIn('id', $nearest_stores)
                ->with(['store_info' => function ($query) {
                    $query->select('id', 'merchant_id', 'images', 'brochure'); 
                }])
                ->get()
                ->toArray();
            
            if (!empty($data)) {
                return $data;
            }
        }
    
        return [];
    }

    public function getNearestRestaurantsAttribute() {
        $nearest_restaurants = $this->nearest_restaurant_ids ? json_decode($this->nearest_restaurant_ids, true) : [];
        
        if (is_array($nearest_restaurants) && !empty($nearest_restaurants)) {
            $data = Merchant::select('id', 'name', 'featured_image', 'organization_id', 'address', 'latitude', 'longitude', 'is_active')
                ->whereIn('id', $nearest_restaurants)
                ->with(['restaurant_info' => function ($query) {
                    $query->select('id', 'merchant_id', 'images', 'brochure'); 
                }])
                ->get()
                ->toArray();
            if (!empty($data)) {
                return $data;
            }
        }

        return [];
    }

    public function getNearestHotelsAttribute() {
        $nearest_hotels = $this->nearest_hotel_ids ? json_decode($this->nearest_hotel_ids, true) : [];
        
        if (is_array($nearest_hotels) && !empty($nearest_hotels)) {
            $data = Merchant::select('id', 'name', 'featured_image', 'organization_id', 'address', 'latitude', 'longitude', 'is_active')
                ->whereIn('id', $nearest_hotels)
                ->with(['hotel_info' => function ($query) {
                    $query->select('id', 'merchant_id', 'images', 'brochure'); 
                }])
                ->get()
                ->toArray();
            if (!empty($data)) {
                return $data;
            }
        }

        return [];
    }

    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization_id');
    }
}
