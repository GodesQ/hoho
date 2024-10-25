<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SSOLoginRequest;
use App\Http\Requests\Auth\SSORegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SSOController extends Controller
{
    public function register(SSORegisterRequest $request)
    {
        $data = $request->validated();
        $account_id = generateRandomUuid();

        $fullContactNumber = $request->contact_number;
        $countryCode = substr($fullContactNumber, 0, 3);
        $contactNumber = substr($fullContactNumber, 3);

        User::create(array_merge($data, [
            'account_id' => $account_id,
            'countryCode' => preg_replace('/[^0-9]/', '', $countryCode),
            'contact_no' => preg_replace('/[^0-9]/', '', $contactNumber),
            'login_with' => 'egov',
            'role' => 'guest'
        ]));

        return response([
            'status' => TRUE,
            'message' => 'User registered successfully'
        ]);
    }

    public function login(SSORegisterRequest $request)
    {

        $admin = Admin::where("email", $request->email)
            ->orWhere('username', $request->username)->exists();

        if ($admin) {
            return response([
                'status' => false,
                'message' => 'An account with this email or username already exists in the administration staff.'
            ], 400);
        }

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                ->orWhere('username', $request->username);
        })
            ->where('login_with', 'egov')->first();

        $data = $request->validated();
        $account_id = generateRandomUuid();

        $fullContactNumber = $request->contact_number;
        $countryCode = substr($fullContactNumber, 0, 3);
        $contactNumber = substr($fullContactNumber, 3);

        if (! $user) {
            $user = User::create(array_merge($data, [
                'password' => Hash::make($request->password),
                'account_id' => $account_id,
                'countryCode' => preg_replace('/[^0-9]/', '', $countryCode),
                'contact_no' => preg_replace('/[^0-9]/', '', $contactNumber),
                'login_with' => 'egov',
                'role' => 'guest'
            ]));
        }

        // if (! Hash::check($request->password, $user->password)) {
        //     return response([
        //         'status' => FALSE,
        //         'message' => 'Your password is incorrect. Please check and try again.'
        //     ], 400);
        // }

        $token = $user->createToken("API TOKEN")->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
