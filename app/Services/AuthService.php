<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use ErrorException;
use Illuminate\Support\Facades\Hash;
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

                if($isOldUser) throw new ErrorException('This is an old user. Please reset your password.');

                throw new ErrorException('Invalid Credentials.');
            }

            $this->validationVerifiedUserEmail($user);

            $token = $user->createToken("API TOKEN")->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];

        } catch (ErrorException $e) {
            throw $e;
        }
    }

    public function register()
    {

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

    private function validationVerifiedUserEmail($user)
    {
        if ($user instanceof User && !$user->is_verify) {
            throw new ErrorException("Please verify your email first before signing in.");
        }
    }
}