<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests\AdminAuth\LoginRequest;

use App\Mail\NewRegisteredMerchantNotification;

use App\Models\Admin;
use App\Models\MerchantHotel;
use App\Models\MerchantRestaurant;
use App\Models\MerchantStore;
use App\Models\MerchantTourProvider;

use App\Http\Requests\AdminAuth\RegisterRequest;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        return view('admin-page.auth.login');
    }

    public function saveLogin(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (Auth::guard('admin')->attempt(array_merge($credentials))) {

            $admin = Auth::guard('admin')->user();

            if ($admin->is_merchant) {
                return $this->checkAdminRoleForMerchant($admin);
            } 

            if(!$admin->is_approved) {
                Auth::logout();
                return back()->with('fail', 'This account has not been approved yet. Please await approval from the administrator.');
            }

            return redirect()->route('admin.dashboard')->with('success', 'Login Successful');

        } else {
            return back()->with('fail', 'Invalid Credentials');
        }
    }

    public function register(Request $request)
    {
        return view('admin-page.auth.register');
    }

    public function saveRegister(RegisterRequest $request)
    {
        $data = $request->validated();

        $admin_user = Admin::create(array_merge($data, [
            'password' => Hash::make($request->password),
            'is_merchant' => TRUE
        ]));

    
        if ($admin_user) {
            $details = [
                'email' => $admin_user->email,
                'registered_date' => date('F d, Y')
            ];

            $receiver = env('APP_ENVIRONMENT') == 'LIVE' ? 'philippinehoho@tourism.gov.ph' : 'james@godesq.com';
    
            Mail::to($receiver)->send(new NewRegisteredMerchantNotification($details));

            // Login to create a session
            return redirect()->route('merchant_account_registered_message');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    private function checkAdminRoleForMerchant($admin)
    {

        switch ($admin->role) {
            case 'merchant_store_admin':
                $merchant_data = MerchantStore::where('id', $admin->merchant_data_id)->exists();
                $type = 'store';
                break;
            case 'merchant_restaurant_admin':
                $merchant_data = MerchantRestaurant::where('id', $admin->merchant_data_id)->exists();
                $type = 'restaurant';
                break;

            case 'merchant_hotel_admin':
                $merchant_data = MerchantHotel::where('id', $admin->merchant_data_id)->exists();
                $type = 'hotel';
                break;

            case 'tour_operator_admin':
                $merchant_data = MerchantTourProvider::where('id', $admin->merchant_data_id)->exists();
                $type = 'tour_provider';
                break;  

            default:
                $merchant_data = false;
                $type = '0';
                break;
        }


        if ($merchant_data) {
            return redirect()->route('admin.dashboard')->with('success', 'Login Successfully');
        } else {
            if(!$admin->is_approved) {
                Auth::logout();
                return back()->with('fail', 'This account has not been approved yet. Please await approval from the administrator.');
            }
            return redirect()->route('merchant_form', $type)->withSuccess('Login Successfully. Please complete this form to continue.');
        }
    }
}
