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
                'status' => false,
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
                    'user' => (
                        [
                            'is_old_user' => 0
                        ]
                    ),
                    'message' => "Invalid credentials."
                ], 400);
            }
        }

        # Check if the password is correct and determined if the user is old user
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status' => false,
                'user' => $user && $user->is_old_user ? (
                    [
                        'is_old_user' => 1
                    ]
                ) : (
                    [
                        'is_old_user' => 0
                    ]
                ),
                'message' => $user && $user->is_old_user ? 'This is an old user. Please change password' : 'Invalid credentials.'
            ], 400);
        }

        if($user->role == 'guest' && !$user->is_verify) {
            return response([
                'status' => false,
                'message' => "Please verify your email first before signing-in."
            ], 400);
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

        # details for sending email to worker
        $details = [
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
        $user->tokens()->delete();

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
}
