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
    /**
     * Login page of admins.
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {   
        if(Auth::guard('admin')->check()) {
            if($request->query('redirectTo')) {
                return redirect($request->query('redirectTo'));
            }
            return redirect()->route('admin.dashboard');
        }

        return view('admin-page.auth.login');
    }
    
    /**
     * Save and validate login request.
     * @param \App\Http\Requests\AdminAuth\LoginRequest $request
     * @return mixed
     */
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
        
        return back()->with('fail', 'Invalid Credentials');
    }

    /**
     * Register page of merchants.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function register(Request $request)
    {
        return view('admin-page.auth.register');
    }

    /**
     * Validate and save the register request of merchants. The admin will receive notification after the successful registration.
     * @param \App\Http\Requests\AdminAuth\RegisterRequest $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function saveRegister(RegisterRequest $request)
    {
        $data = $request->validated();

        $admin_user = Admin::create(array_merge($data, [
            'is_merchant' => TRUE
        ]));
            
        $details = [
            'email' => $admin_user->email,
            'registered_date' => date('F d, Y')
        ];

        $receiver = config('app.env') === 'production' ? env('MAIL_ADMIN_RECEIVER') : config('mail.test_receiver');

        Mail::to($receiver)->send(new NewRegisteredMerchantNotification($details));

        // Login to create a session
        return redirect()->route('merchant_account_registered_message');
    }

    /**
     * Logout the current authenticated user.
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */ 
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    /**
     * Check the merchant role, then redirect if the merchant doesn't have any merchant information in the database.
     * @param mixed $admin
     * @return mixed
     */
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
