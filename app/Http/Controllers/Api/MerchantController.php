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
        $merchant = Merchant::where('id', $request->id)->firstOr(function () {
            return response([
                'status' => FALSE,
                'message' => 'Merchant not found.'
            ], 404);
        });


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
}
