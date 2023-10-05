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
    public function getUser(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 'bus_operator') {
            $user->load('transport');
        }

        return $user;
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $image_name = $request->username;

            if ($request->hasFile('user_profile')) {
                $old_upload_image = public_path('/assets/img/user_profiles') . $user->user_profile;
                @unlink($old_upload_image);
                $file = $request->file('user_profile');
                $file_name = Str::snake(Str::lower($image_name)) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . '/assets/img/user_profiles', $file_name);
            } else {
                $file_name = $user->user_profile;
            }

            $result = $this->parseContactNumber($request, $request->contact_no);

            $user->update([
                'user_profile' => $file_name,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'birthdate' => $request->birthdate,
                'country_of_residence' => $request->country_of_residence,
                'contact_no' => $result['contactNumber'] ?? null,
                'countryCode' => $result['countryCode'] ?? null,
                'isoCode' => $result['isoCode'] ?? null,
            ]);

            return response([
                'status' => true,
                'message' => 'User updated successfully'
            ]);

        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => 'User failed to update'
            ]);
        }
    }

    public function updateInterest(Request $request)
    {
        try {
            $user = Auth::user();
            $user->update([
                'interest_ids' => json_encode($request->interest_ids)
            ]);

            return response([
                'status' => TRUE,
                'message' => 'Interest updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return response([
                'status' => FALSE,
                'message' => 'User Interest failed to update'
            ]);
        }
    }

    public function destroyAccount(Request $request)
    {
        $user = Auth::user();

        $delete_tokens = $user->tokens()->delete();

        $delete_user = $user->delete();

        if ($delete_tokens && $delete_user) {
            return response([
                'status' => TRUE,
                'message' => 'Delete Account Successfully'
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'username' => ['required'],
            'new_password' => ['required', 'min:8'],
            'confirm_password' => ['required', 'same:new_password']
        ]);

        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user_password = User::where($fieldType, $request->username)->update([
            'password' => Hash::make($request->new_password),
            'is_old_user' => FALSE,
        ]);

        if ($user_password) {
            return response([
                'status' => TRUE,
                'message' => 'Change Password Successfully'
            ]);
        }
    }

    private function parseContactNumber(Request $request, $contactNo): array
    {
        try {
            if (is_string($contactNo) && is_array(json_decode($contactNo, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                $data = is_string($contactNo) ? json_decode($contactNo, true) : null;
                $countryCode = $data['countryCode'] ?? null;
                $isoCode = $data['isoCode'] ?? null;
                $number = $data['number'] ?? null;
                $contactNo = $number;
            } else {
                $countryCode = null;
                $isoCode = null;
                $contactNo = $request->input('contact_no');
            }

            return [
                'countryCode' => $countryCode,
                'isoCode' => $isoCode,
                'contactNumber' => $contactNo
            ];
        } catch (\Exception $e) {
            // Handle JSON decoding or other exceptions here, possibly log the error.
            return [
                'countryCode' => null,
                'isoCode' => null,
                'contactNumber' => null
            ];
        }
    }

}