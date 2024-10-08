<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Http\Resources\MerchantResource;
use App\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    public function getOrganizations(Request $request)
    {
        $organizations = Organization::get();
        return response([
            'status' => TRUE,
            'organizations' => OrganizationResource::collection($organizations),
        ]);
    }

    public function getOrganization(Request $request)
    {
        // Fetch the organization data with necessary relations and filtered results
        $organization = Organization::with([
            'attractions',
            'hotels.hotel_info',
            'stores.store_info',
            'restaurants.restaurant_info'
        ])->where('id', $request->id)->first();

        $userInterests = Auth::user()->interest_ids && Auth::user()->interest_ids != 'null' ? json_decode(Auth::user()->interest_ids) : [];

        // Filter and merge the results
        $filteredMerchants = [];

        // Filter hotels based on user interests and merge into the $filteredMerchants array
        $filteredHotels = $organization->hotels->filter(function ($hotel) use ($userInterests) {
            $hotelInterests = $hotel->hotel_info->interests ?? [];
            return $hotelInterests && array_intersect($userInterests, json_decode($hotelInterests));
        });
        $filteredMerchants = array_merge($filteredMerchants, $filteredHotels->all());

        // Filter restaurants based on user interests and merge into the $filteredMerchants array
        $filteredRestaurants = $organization->restaurants->filter(function ($restaurant) use ($userInterests) {
            $restaurantInterests = $restaurant->restaurant_info->interests ?? [];
            return $restaurantInterests && array_intersect($userInterests, json_decode($restaurantInterests));
        });

        $filteredMerchants = array_merge($filteredMerchants, $filteredRestaurants->all());

        // Filter stores based on user interests and merge into the $filteredMerchants array
        $filteredStores = $organization->stores->filter(function ($store) use ($userInterests) {
            $storeInterests = $store->store_info->interests ?? [];
            return $storeInterests && array_intersect($userInterests, json_decode($storeInterests));
        });
        $filteredMerchants = array_merge($filteredMerchants, $filteredStores->all());

        $featuredAttractions = [];

        $filteredAttractions = $organization->attractions->map(function ($attraction) {
            return $attraction->toArray();
        });

        $featuredAttractions = $filteredAttractions->filter(function ($attraction) {
            return $attraction['is_featured'];
        })->toArray();

        $featuredAttractions = array_values($featuredAttractions);


        $organizationResource  = OrganizationResource::make($organization);

        $organizationResource->filtered_merchants = MerchantResource::collection($filteredMerchants);
        $organizationResource->featured_attractions = $featuredAttractions;
        
        if ($organizationResource) {
            return response([
                'status' => TRUE,
                'organization' => $organizationResource,
            ]);
        }

        return response([
            'status' => FALSE,
            'organization' => 'Not Found'
        ], 404);
    }
}
