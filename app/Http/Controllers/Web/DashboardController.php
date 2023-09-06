<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Admin;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {
        return view('admin-page.dashboard.dashboard');
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
}
