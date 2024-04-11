<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateProfile(Request $request)
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

            $update_user = $user->update([
                'user_profile' => $file_name,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'birthdate' => $request->birthdate,
                'country_of_residence' => $request->country_of_residence,
                'contact_no' => $request->contact_no['number'] ?? null,
                'countryCode' => $request->contact_no['countryCode'] ? preg_replace("/[^0-9]/", "", $request->contact_no['countryCode']) : null,
                'isoCode' => $request->contact_no['isoCode'] ?? null,
            ]);

            return response([
                'status' => 'success',
                'message' => 'Updated Successfully',
            ], 200);

        } catch (\Exception $e) {
            return response([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function parseContactNumber(Request $request, $contactNo): array
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
