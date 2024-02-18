<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function index(Request $request) {
        return MerchantResource::collection(Merchant::get());
    }

    public function show(Request $request, $merchant_id) {
        return MerchantResource::make(Merchant::findOrFail($merchant_id));
    }
}
