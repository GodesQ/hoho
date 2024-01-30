<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;

use App\Models\Merchant;
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
        if(Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin-page.auth.login');
    }

    public function saveLogin(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::guard('admin')->attempt(array_merge($credentials))) {
            $admin = Auth::guard('admin')->user();

            // After login, check if the account has been approved by administrator
            if (!$admin->is_approved) {
                Auth::logout();
                return back()->with('fail', 'This account has not been approved yet. Please await approval from the administrator.');
            }

            // Check if this login account is merchant
            if (in_array($admin->role, merchant_roles())) $this->checkMerchantRole($admin);

            return redirect()->route('admin.dashboard')->withSuccess('Login Successfully');
        } 
        
        // Return back to login if failed
        return back()->with('fail', 'Invalid Credentials');
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

            $receiver = env('APP_ENVIRONMENT') == 'LIVE' ? env('MAIL_ADMIN_RECEIVER') : 'james@godesq.com';

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

    private function checkMerchantRole($admin)
    {

        $merchant_data = Merchant::where('id', $admin->merchant_id)->exists();

        switch ($admin->role) {
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

        // Redirect to merchant form if no merchant data found
        if (!$merchant_data)
            return redirect()->route('merchant_form', $type)->withSuccess('Login Successfully. Please complete this form to continue.');
    }
}
