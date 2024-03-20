<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SSORegisterRequest;
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
    public function login(Request $request) {
        $validator = \Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        # Find the user based on email or username
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        return $this->authenticate($request, $fieldType);
    }

    public function dash(Request $request) {
        $user = Auth::user();
        return response()->json($user, 200);
    }   


    public function register(RegisterRequest $request) {
        $data = $request->validated();
        $account_uid = $this->generateRandomUuid();

        $contact_no_format = $this->checkContactNumberJSON($request->contact_no);

        $register = User::create(array_merge($data, [
            'account_uid' => $account_uid,
            'country_of_residence' => $request->country_of_residence,
            'contact_no' =>  preg_replace('/[^0-9]/', '', $contact_no_format['contactNumber']),
            'countryCode' => preg_replace("/[^0-9]/", "", $contact_no_format['countryCode']),
            'isoCode' => $contact_no_format['isoCode'],
            'is_first_time_philippines' => $request->has('is_first_time_philippines'),
            'is_international_tourist' => $request->has('is_international_tourist'),
            'role' => 'guest'
        ]));

        // SEND EMAIL FOR VERIFICATION
        $details = ['email' => $request->email, 'username' => $request->username];
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
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout Successfully',
        ], 200);
    }

    # HELPERS    

    private function generateRandomUuid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4 (random)
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant (RFC 4122)

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return $uuid;
    }

    private function checkContactNumberJSON($requestContactNo) {
        if (is_string($requestContactNo) && is_array(json_decode($requestContactNo, true)) && (json_last_error() == JSON_ERROR_NONE)) {
            $data = json_decode($requestContactNo, true);
            $countryCode = $data['countryCode'];
            $isoCode = $data['isoCode'] ?? null;
            $contactNumber = $data['number'];
        } else {
            $countryCode = null;
            $isoCode = null;
            $contactNumber = trim($requestContactNo);
        }

        return [
            'countryCode' => $countryCode,
            'isoCode' => $isoCode,
            'contactNumber' => $contactNumber
        ];
    }

    private function authenticateUser($request, $fieldType) {
        $user = User::where($fieldType, $request->username)->first();
    
        if (!$user) {
            $user = Admin::where($fieldType, $request->username)->first();
            if ($user && $user->role === 'bus_operator') {
                $user->load('transport');
            } elseif (!$user) {
                return $this->handleInvalidCredentials();
            }
        }
    
        return $user;
    }

    private function handleInvalidCredentials() {
        $response = [
            'status' => false,
            'message' => 'Invalid credentials.',
            'user' => ['is_old_user' => 0],
        ];
    
        return response($response, 400);
    }

    private function authenticate(Request $request, $fieldType) {        
        $user = $this->authenticateUser($request, $fieldType);
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            $isOldUser = $user && $user->is_old_user;
            $response = [
                'status' => FALSE,
                'user' => ['is_old_user' => $isOldUser ? 1 : 0],
                'message' => $isOldUser ? 'This is an old user. Please change password' : 'Invalid credentials.',
            ];
    
            return response($response, 400);
        }
        
        // Check if the user is a guest and already verify the email address
        if ($user->role == 'guest' && !$user->is_verify) {
            $response = [
                'status' => FALSE,
                'message' => 'Please verify your email first before signing in.',
            ];

            return response($response, 400);
        }
    
        $token = $user->createToken("API TOKEN")->plainTextToken;
    
        return response([
            'status' => TRUE,
            'user' => $user,
            'token' => $token,
            'message' => 'User Found'
        ], 200);
    }

    # END OF HELPERS
}
