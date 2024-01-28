<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\MerchantHotel;
use App\Models\MerchantRestaurant;
use App\Models\MerchantStore;
use App\Models\MerchantTourProvider;

class IsMerchantCreated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
        $user = Auth::guard('admin')->user();
        if(in_array($user->role, merchant_roles())) {
            if(!$user->merchant_id) {
                $type = '0';
                switch ($user->role) {
                    case 'merchant_store_admin':
                        $type = 'store';
                        break;
                    case 'merchant_restaurant_admin':
                        $type = 'restaurant';
                        break;
        
                    case 'merchant_hotel_admin':
                        $type = 'hotel';
                        break;
        
                    case 'tour_operator_admin':
                        $type = 'tour_provider';
                        break;  
        
                    default:
                        $merchant_data = false;
                        $type = '0';
                        break;
                }
                return redirect()->route('merchant_form', $type)->with('fail', 'Please complete this form to continue.');
            }
            return $next($request);
        } else {
            return $next($request);
        }
    }
}
