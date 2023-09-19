<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\ForgotPasswordToken;
use App\Models\User;

use App\Http\Requests\ForgotPassword\ResetPasswordRequest;

class ForgotPasswordController extends Controller
{
    public function resetPasswordForm(Request $request) {
        $email = $request->email;
        $token = $request->verify_token;
        return view('misc.reset-password-form', compact('email', 'token'));
    }

    public function postResetPasswordForm(ResetPasswordRequest $request) {
        $forgot_password = ForgotPasswordToken::where('token', $request->verify_token)->delete();

        if($forgot_password) {
            $user_password =  User::where('email', $request->email)->update([
                'password' => Hash::make($request->new_password),
                'is_old_user' => FALSE,
            ]);

            if($user_password) return redirect()->route('user.reset_password_success')->with('success', 'User Password Reset Successfully.');
        }
    }
}
