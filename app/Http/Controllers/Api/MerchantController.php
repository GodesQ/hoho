<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Merchant;
use App\Models\MerchantStore;
use App\Models\MerchantHotel;
use App\Models\MerchantRestaurant;
use App\Models\MerchantTourProvider;

class MerchantController extends Controller
{
    public function getMerchant(Request $request) {
        $merchant = Merchant::where('id', $request->id)->first();

        if (!$merchant) {
            return response([
                'status' => FALSE,
                'message'=> 'No Merchant Found'
            ], 400);
        }
        // return response($merchant);

        switch ($merchant->type) {
            case 'Store':
                $merchant_child = MerchantStore::where('merchant_id', $merchant->id)->first();
                break;

            case 'Hotel':
                $merchant_child = MerchantHotel::where('merchant_id', $merchant->id)->first();
                break;

            case 'Restaurant':
                $merchant_child = MerchantRestaurant::where('merchant_id', $merchant->id)->first();
                break;

            case 'Tour Provider':
                $merchant_child = MerchantTourProvider::where('merchant_id', $merchant->id)->first();
                break;

            default:
                $merchant_child = null;
                break;
        }

        $merchantResult = array_merge($merchant->toArray(), [
            'merchant_type_data' => $merchant_child->toArray()
        ]);

        return response($merchantResult);
    }

    public function getFeaturedMerchants(Request $request) {
        $featured_merchants = Merchant::where('is_featured', 1)
        ->where('is_active', 1)
        ->where(function ($query) {
            $query->whereHas('store_info')
                ->orWhereHas('restaurant_info')
                ->orWhereHas('hotel_info');
        })
        ->with(['store_info', 'restaurant_info', 'hotel_info'])
        ->get();

        return response([
            'status' => TRUE,
            'featured_merchants' => $featured_merchants
        ]);
    }
}
