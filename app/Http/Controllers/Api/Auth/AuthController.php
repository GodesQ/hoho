<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;

use App\Models\Admin;
use App\Models\User;

use App\Http\Requests\Auth\RegisterRequest;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = \Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 401);
        }

        $user = null;
        $token = '';

        # Find the user based on email or username
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($fieldType, $request->username)->first();

        # if it is not user then try to search in admin table
        if(!$user) {
            $user = Admin::where($fieldType, $request->username)->first();
            if($user) {
                # Load the 'transport' relationship if the role is 'bus_operator'
                if ($user->role === 'bus_operator') {
                    $user->load('transport');
                }
            } else {
                return response([
                    'status' => false,
                    'is_old_user' => FALSE,
                    'message' => "Invalid credentials."
                ], 200);
            }
        }



        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status' => false,
                'is_old_user' => $user && $user->is_old_user,
                'message' => $user && $user->is_old_user ? 'This is an old user. Please change password' : 'Invalid credentials.'
            ], 200);
        }
        

        if($user->role == 'guest') {
            if(!$user->is_verify) {
                return response([
                    'status' => false,
                    'message' => "Please Verify First Your Email Verification"
                ], 200);
            }
        } 

        $token = $user->createToken("API TOKEN")->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function dash(Request $request) {
        $user = Auth::user();
        return response()->json($user, 200);
    }


    public function register(RegisterRequest $request) {
        $account_uid = $this->generateRandomUuid();


        $register = User::create([
            'account_uid' => $account_uid,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'birthdate' => $request->birthdate != null || $request->birthdate != '' ? Carbon::createFromFormat('Y-m-d', $request->birthdate) : null,
            'country_of_residence' => $request->country_of_residence,
            'is_old_user' => false,
            'is_first_time_philippines' => $request->has('is_first_time_philippines'),
            'is_international_tourist' => $request->has('is_international_tourist'),
            'role' => 'guest'
        ]);

        # details for sending email to worker
        $details = [
            'title' => 'Verification email from HOHO',
            'email' => $request->email,
            'username' => $request->username,
        ];

        // SEND EMAIL FOR VERIFICATION
        Mail::to($request->email)->send(new EmailVerification($details));

        if($register) {
            return response([
                'status' => TRUE,
                'message' => 'User registered successfully'
            ]); 
        }
    }

    public function logout(Request $request) {
        $user = Auth::user();

        # delete token
        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout Successfully',
        ], 200);
    }

    private function generateRandomUuid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4 (random)
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant (RFC 4122)

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return $uuid;
    }
}
