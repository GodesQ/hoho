<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Http\Requests\User\ChangeUserPasswordRequest;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\MerchantHotel;
use App\Models\MerchantStore;
use App\Models\MerchantRestaurant;
use App\Models\MerchantTourProvider;
use App\Models\TourReservation;


class DashboardController extends Controller
{
    public function dashboard(Request $request) {
        $user = Auth::guard('admin')->user();
    
        if ($user->is_merchant) {
            $merchantInfo = null;
            $type = null;
            
            $recentTourReservations = TourReservation::with('user', 'tour')->where('created_by', $user->id)->with('tour', 'user')->latest()->limit(5)->get();

            switch ($user->role) {
                case 'merchant_hotel_admin':
                    $merchantInfo = MerchantHotel::where('id', $user->merchant_data_id)->with('merchant')->first();
                    $type = 'hotel';
                    break;
    
                case 'merchant_store_admin':
                    $merchantInfo = MerchantStore::where('id', $user->merchant_data_id)->with('merchant')->first();
                    $type = 'store';
                    break;
    
                case 'merchant_restaurant_admin':
                    $merchantInfo = MerchantRestaurant::where('id', $user->merchant_data_id)->with('merchant')->first();
                    $type = 'restaurant';
                    break;
    
                case 'tour_operator_admin':
                    $merchantInfo = MerchantTourProvider::where('id', $user->merchant_data_id)->with('merchant')->first();
                    $type = 'tour_provider';
                    break;
            }
    
            return view('admin-page.dashboard.merchant-dashboard', compact('merchantInfo', 'type', 'recentTourReservations'));
        }
    
        $recentTransactions = Transaction::select('reference_no', 'id', 'transaction_by_id', 'payment_status', 'aqwire_totalAmount', 'aqwire_paymentMethodCode')
            ->where('payment_status', 'success')
            ->with('user')
            ->latest()
            ->limit(6)
            ->get();
    
        return view('admin-page.dashboard.dashboard', compact('recentTransactions'));
    }    

    public function testLocation() {
        return view('misc.test-location');
    }

    public function testLocation2() {
        return view('misc.test-location-2');
    }

    public function adminProfile(Request $request) {
        $user = Auth::guard('admin')->user();

        return view('admin-page.profile.profile', compact('user'));
    }

    public function saveProfile(Request $request) {
        $data = $request->except('_token', 'admin_profile');
        $admin = Auth::guard('admin')->user();
        // dd($request->all());

        $image_name = $admin->username;

        if($request->hasFile('admin_profile')) {
            $old_upload_image = public_path('/assets/img/admin_profiles') . $admin->admin_profile;
            @unlink($old_upload_image);
            $file = $request->file('admin_profile');
            $file_name = Str::snake(Str::lower($image_name)) . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/admin_profiles', $file_name);
        } else {
            $file_name = $admin->admin_profile;
        }

        $update_admin = $admin->update(array_merge($data, [
            'admin_profile' => $file_name
        ]));

        if($update_admin) {
            return back()->withSuccess('Profile updated successfully');
        }
    }

    public function changePassword(ChangeUserPasswordRequest $request) {
        $user = Auth::user();

        if(!Hash::check($request->old_password, $user->password)) return back()->with('fail', 'Your old password is incorrect. Please Try Again.');

        $update_user = $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        if($update_user) {
            return back()->withSuccess('Your Password Updated Successfully.');
        }
    }
}
