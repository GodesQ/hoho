<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SSORegisterRequest;
use App\Services\AuthService;
use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;

use App\Models\Admin;
use App\Models\User;

use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{   
    private $authService;
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request) {

        try {
            $auth = $this->authService->login($request);

            return response([
                'status' => TRUE,
                'user' => $auth['user'],
                'token' => $auth['token'],
            ], 200);

        } catch(Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function dash(Request $request) {
        $user = Auth::user();
        return response()->json($user, 200);
    }

    public function register(RegisterRequest $request) {
        try {
            $user = $this->authService->register($request);
            
            return response([
                'status' => true,
                'message' => 'Registration successful. Please verify your email now.'
            ]);

        } catch (Exception $e) {
           return response()->json([
            'status' => false,
            'message' => $e->getMessage()
           ], 400);
        }
    }

    public function logout(Request $request) {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout Successfully',
        ], 200);
    }
}
