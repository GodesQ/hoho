<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\User;

use App\Http\Requests\User\UpdateProfileRequest;

class UserController extends Controller
{
    public function getUser(Request $request) {
        $user = Auth::user();
        if($user->role == 'bus_operator') {
            $user->load('transport');
        }

        return $user;
    }

    public function updateProfile(UpdateProfileRequest $request) {
        $user = Auth::user();

        $image_name = $request->username;

        if($request->hasFile('user_profile')) {
            $old_upload_image = public_path('/assets/img/user_profiles') . $user->user_profile;
            @unlink($old_upload_image);
            $file = $request->file('user_profile');
            $file_name = Str::snake(Str::lower($image_name)) . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/user_profiles', $file_name);
        } else {
            $file_name = $user->user_profile;
        }

        $user_update = $user->update([
            'email' => $request->email,
            'user_profile' => $file_name,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'birthdate' => $request->birthdate,
            'country_of_residence' => $request->country_of_residence,
            'contact_no' => $request->contact_no,
        ]);

        if($user_update) {
            return response([
                'status' => true,
                'message' => 'User updated successfully'
            ]);
        }
    }

    public function updateInterest(Request $request) {
        $user = Auth::user();

        $update_user = $user->update([
            'interest_ids' => json_encode($request->interest_ids)
        ]);

        if($update_user) {
            return response([
                'status' => TRUE,
                'message' => 'Interest updated successfully'
            ]);
        }
    }

    public function destroyAccount(Request $request) {
       $user = Auth::user();

       $delete_tokens = $user->tokens()->delete();

       $delete_user = $user->delete();

       if($delete_tokens && $delete_user) {
            return response([
                'status' => TRUE,
                'message' => 'Delete Account Successfully'
            ]);
       }
    }

    public function changePassword(Request $request) {
        $validator = \Validator::make($request->all(), [
            'username' => ['required', 'exists:user,username'],
            'new_password' => ['required', 'min:8'],
            'confirm_password' => ['required', 'same:new_password']
        ]);

        $user_password =  User::where('username', $request->username)->update([
            'password' => Hash::make($request->new_password),
            'is_old_user' => FALSE,
        ]);

        if($user_password) {
            return response([
                'status' => TRUE, 
                'message' => 'Change Password Successfully'
            ]);
        }

    }
}
