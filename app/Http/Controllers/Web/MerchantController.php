<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Merchant;
use App\Models\MerchantStore;
use App\Models\Organization;
use App\Models\Admin;

use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    public function merchant_form(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $organizations = Organization::get();
        switch ($request->type) {
            case 'hotel':
                if ($admin->role === 'merchant_hotel_admin') {
                    $admin = Admin::where('id', $admin->id)->with('merchant', 'merchant.hotel_info')->first();
                    return view('admin-page.merchants_forms.merchant_hotel_form', compact('organizations', 'admin'));
                }
                abort(404);

            case 'store':
                if ($admin->role === 'merchant_store_admin') {
                    $admin = Admin::where('id', $admin->id)->with('merchant', 'merchant.store_info')->first();
                    return view('admin-page.merchants_forms.merchant_store_form', compact('organizations', 'admin'));
                }
                abort(404);

            case 'restaurant':
                if ($admin->role === 'merchant_restaurant_admin') {
                    $admin = Admin::where('id', $admin->id)->with('merchant', 'merchant.restaurant_info')->first();
                    // dd($admin);

                    return view('admin-page.merchants_forms.merchant_restaurant_form', compact('organizations', 'admin'));
                }
                abort(404);

            case 'tour_provider':
                if ($admin->role === 'tour_operator_admin') {
                    $admin = Admin::where('id', $admin->id)->with('merchant', 'merchant.tour_provider_info')->first();
                    return view('admin-page.merchants_forms.merchant_tour_provider_form', compact('organizations', 'admin'));
                }
                abort(404);

            default:
                abort(404);
        }
    }

    public function merchantsByUserRole(Request $request)
    {
        $role = $request->role;
        $merchants = [];

        $adminMerchantIds = Admin::select('merchant_id')->whereNotNull('merchant_id')->get()->toArray();

        switch ($role) {
            case 'merchant_restaurant_admin':
                $merchants = Merchant::where('type', 'Restaurant')->whereNotIn('id', $adminMerchantIds)->get();
                break;
            case 'merchant_hotel_admin':
                $merchants = Merchant::where('type', 'Hotel')->whereNotIn('id', $adminMerchantIds)->get();
                break;
            case 'merchant_store_admin':
                $merchants = Merchant::where('type', 'Store')->whereNotIn('id', $adminMerchantIds)->get();
                break;
            case 'tour_operator_admin':
                    $merchants = Merchant::where('type', 'Tour Provider')->whereNotIn('id', $adminMerchantIds)->get();
                    break;
            default:
                $merchants  = [];
                break;
        }

        return response([
            'status' => TRUE,
            'merchants' => $merchants
        ]);
    }
}
