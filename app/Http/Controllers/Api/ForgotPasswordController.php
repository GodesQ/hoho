<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use App\Mail\ResetPasswordMail;

use App\Models\ForgotPasswordToken;

class ForgotPasswordController extends Controller
{
    public function post_forgot_password(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $token = $this->generateToken();

        $create = ForgotPasswordToken::create([
            'email' => $request->email,
            'token' => $token
        ]);

        $send_mail = Mail::to($request->email)->send(new ResetPasswordMail($request->email, $token));

        return response([
            'status' => TRUE,
            'message' => 'Successfully Sent! Please check your email for the link of reset password'
        ]);

    }

    private function generateToken() {
        $token = Str::random(10);
        return $token;
    }
}
