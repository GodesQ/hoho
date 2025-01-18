<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $username_type = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($username_type, $request->username)->first();

        if (! $user)
        {
            return response([
                'message' => 'Invalid Credentials.'
            ], 400);
        }

        if (! Hash::check($request->password, $user->password))
        {
            $isOldUser = $user && $user->is_old_user;
            $response = [
                'message' => $isOldUser ? 'This is an old user. Please change password' : 'Invalid credentials.',
            ];

            return response($response, 400);
        }

        if ($user->role == 'guest' && ! $user->is_verify)
        {
            $response = [
                'message' => 'Please verify your email first before signing in.',
            ];

            return response($response, 400);
        }

        $token = $user->createToken("API TOKEN")->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 200);

    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user)
        {
            Auth::logout();
        }

        return response(['message' => 'Logout Successfully'], 200);
    }
}
