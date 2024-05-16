<?php

namespace App\Services;

use App\Enum\UserRoleEnum;
use App\Mail\EmailVerification;
use App\Models\Admin;
use App\Models\User;
use ErrorException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class AuthService
{
    public function __construct()
    {

    }

    public function login($request)
    {
        try {
            $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $user = $this->authenticateUser($request, $fieldType);

            if(!$user) throw new ErrorException('Invalid Credentials.');

            if(!Hash::check($request->password, $user->password)) {
                $isOldUser = $user && $user->is_old_user;

                if($isOldUser) throw new ErrorException('Your account requires a password reset. Please update your password to continue.');

                throw new ErrorException('Invalid Credentials.');
            }

            $this->validateUserVerifiedEmail($user);

            $token = $user->createToken("API TOKEN")->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];

        } catch (ErrorException $e) {
            throw $e;
        }
    }

    public function register($request)
    {
        try {
            $data = $request->validated();
            $account_uid = $this->generateRandomUuid();
    
            $contact_no_format = $this->checkContactNumberJSON($request->contact_no);
    
            $user = User::create(array_merge($data, [
                'account_uid' => $account_uid,
                'country_of_residence' => $request->country_of_residence,
                'contact_no' =>  preg_replace('/[^0-9]/', '', $contact_no_format['contactNumber']),
                'countryCode' => preg_replace("/[^0-9]/", "", $contact_no_format['countryCode']),
                'isoCode' => $contact_no_format['isoCode'],
                'is_first_time_philippines' => $request->has('is_first_time_philippines'),
                'is_international_tourist' => $request->has('is_international_tourist'),
                'role' => UserRoleEnum::GUEST
            ]));
    
            // SEND EMAIL FOR VERIFICATION
            $details = ['email' => $request->email, 'username' => $request->username];
            Mail::to($request->email)->send(new EmailVerification($details));

            return $user;
    
            // if($user) {
            //     return response([
            //         'status' => TRUE,
            //         'message' => 'User registered successfully'
            //     ]);
            // }
        } catch (ErrorException $e) {
            throw $e;
        }
    }

    public function changePassword()
    {

    }

    public function logout()
    {

    }

    private function authenticateUser($request, $fieldType)
    {
        try {
            $user = $this->findUser($fieldType, $request->username);

            if ($user) {
                $this->validateUserRole($user);
                $this->loadUserRelations($user);
            }

            return $user;

        } catch (ErrorException $e) {
            throw $e;
        }
    }

    private function findUser($fieldType, $username)
    {
        $user = User::where($fieldType, $username)->first();

        if (!$user) {
            $user = Admin::where($fieldType, $username)->first();
        }

        return $user;
    }

    private function validateUserRole($user)
    {
        if ($user instanceof Admin && $user->role !== 'bus_operator') {
            throw new ErrorException("Invalid user role. Please try another account");
        }
    }

    private function loadUserRelations($user)
    {
        if ($user instanceof Admin && $user->role === 'bus_operator') {
            $user->load('transport');
        }
    }

    private function validateUserVerifiedEmail($user)
    {
        if ($user instanceof User && !$user->is_verify) {
            throw new ErrorException("Please verify your email before signing in. Don't forget to check your spam or junk folder.");
        }
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