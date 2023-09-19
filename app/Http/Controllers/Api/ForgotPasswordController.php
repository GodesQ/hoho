<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function post_forgot_password(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $token = $this->generateToken();

        $create = ForgotPassword::create([
            'email' => $request->email,
            'token' => $token
        ]);

        $send_mail = Mail::to($request->email)->send(new ResetPasswordMail($request->email, $token));

        if($create) {
            return redirect()->route('user.forgot_password.message');
        }
    }
}
