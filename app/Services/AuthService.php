<?php

namespace App\Services;

use App\Enum\UserRoleEnum;
use App\Mail\EmailVerification;
use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use ErrorException;
use Exception;
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

            if (!$user)
                throw new Exception('Invalid Credentials.');

            if (!Hash::check($request->password, $user->password)) {
                $isOldUser = $user && $user->is_old_user;

                if ($isOldUser)
                    throw new Exception('Your account requires a password reset. Please update your password to continue.');

                throw new Exception('Invalid Credentials.');
            }

            $this->validateUserVerifiedEmail($user);

            $token = $user->createToken("API TOKEN")->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function register($request)
    {
        try {
            $data = $request->except(['confirm_password']);
            $account_uid = $this->generateRandomUuid();

            $data['password'] = Hash::make($request->password);

            $contact_no_format = $this->checkContactNumberJSON($request->contact_no);

            $user = User::updateOrCreate(array_merge($data, [
                'account_uid' => $account_uid,
                'country_of_residence' => $request->country_of_residence,
                'contact_no' => preg_replace('/[^0-9]/', '', $contact_no_format['contactNumber']),
                'countryCode' => preg_replace("/[^0-9]/", "", $contact_no_format['countryCode']),
                'isoCode' => $contact_no_format['isoCode'],
                'is_first_time_philippines' => $request->has('is_first_time_philippines'),
                'is_international_tourist' => $request->has('is_international_tourist'),
                'role' => UserRoleEnum::GUEST
            ]), []);

            if ($request->has('birthdate')) {
                $birthdate = $request->birthdate;

                if (Carbon::hasFormat($birthdate, 'Y-m-d') && strtotime($birthdate)) {
                    $age = Carbon::parse($birthdate)->age;

                    $user->update([
                        'age' => $age,
                    ]);
                }
            }

            // SEND EMAIL FOR VERIFICATION
            $details = ['email' => $request->email, 'username' => $request->username];
            Mail::to($request->email)->send(new EmailVerification($details));

            return $user;

        } catch (Exception $e) {
            throw $e;
        }
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
        if ($user instanceof Admin && $user->role !== Role::BUS_OPERATOR) {
            throw new ErrorException("Invalid user role. Please try another account");
        }
    }

    private function loadUserRelations($user)
    {
        if ($user instanceof Admin && $user->role === Role::BUS_OPERATOR) {
            $user->load('transport');
        }
    }

    private function validateUserVerifiedEmail($user)
    {
        if ($user instanceof User && !$user->is_verify) {
            throw new ErrorException("Please verify your email before signing in. Don't forget to check your spam or junk folder.");
        }
    }

    private function generateRandomUuid()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4 (random)
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant (RFC 4122)

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return $uuid;
    }

    private function checkContactNumberJSON($requestContactNo)
    {
        if (is_string($requestContactNo) && is_array(json_decode($requestContactNo, true)) && (json_last_error() == JSON_ERROR_NONE)) {
            $data = json_decode($requestContactNo, true); // Set the 2nd paramater to true to convert in associative array
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