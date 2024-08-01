<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{   
    protected $userService;
    
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function getUser(Request $request)
    {
        $user = Auth::user();

        if ($user->role == Role::BUS_OPERATOR) {
            $user->load('transport');
        }

        if($user->role == 'guest') {
            $user->load('user_badges');
        }
 
        return $user;
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $update_profile = $this->userService->updateProfile($request);
        
        return $update_profile
        ? response([
            'status' => TRUE,
            'message' => 'User profile updated successfully'
        ])
        : response([
            'status' => FALSE,
            'message' => 'User profile failed to update'
        ], 400);
    }

    public function updateInterest(Request $request)
    {   
        $update_interests = $this->userService->updateInterest($request);

        return $update_interests
        ? response([
            'status' => TRUE,
            'message' => 'Interest updated successfully'
        ])
        : response([
            'status' => FALSE,
            'message' => 'User Interest failed to update'
        ], 400);
    }

    public function destroyAccount(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response([
                'status' => false,
                'message' => 'User Not Found',
            ], 404);
        }

        $delete_user = $this->userService->deleteUser($user);

        return $delete_user
            ? response([
                'status' => true,
                'message' => 'User deleted successfully',
            ], 200)
            : response([
                'status' => false,
                'message' => 'User failed to delete',
            ], 400);
    }

    public function changePassword(Request $request)
    {   
        $change_password = $this->userService->changePassword($request);

        return $change_password
            ? response([
                'status' => true,
                'message' => 'Change Password Successfully',
            ], 200)
            : response([
                'status' => false,
                'message' => 'User password failed to update',
            ], 500);
    }
}