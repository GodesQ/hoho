<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Merchant;
use App\Models\MerchantStore;
use App\Models\Organization;

use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    public function merchant_form(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $organizations = Organization::get();
        switch ($request->type) {
            case 'hotel':
                if($admin->role == 'merchant_hotel_admin') {
                    return view('admin-page.merchants_forms.merchant_hotel_form', compact('organizations'));
                }
                abort(404);

            case 'store':
                if($admin->role == 'merchant_store_admin') {
                    return view('admin-page.merchants_forms.merchant_store_form', compact('organizations'));
                }
                abort(404);

            case 'restaurant':
                if($admin->role == 'merchant_restaurant_admin') {
                    return view('admin-page.merchants_forms.merchant_restaurant_form', compact('organizations'));
                }
                abort(404);

            case 'tour_provider':
                if($admin->role == 'tour_operator_admin') {
                    return view('admin-page.merchants_forms.merchant_tour_provider_form', compact('organizations'));
                }
                abort(404);

            default: 
                abort(404);
        }
    }
}