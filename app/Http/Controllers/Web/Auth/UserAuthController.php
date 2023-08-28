<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class UserAuthController extends Controller
{
    public function verifyEmail(Request $request) {
        $user = User::where('email', $request->email)->firstOrFail();

        $update_user = $user->update([
            'is_verify' => true,
        ]);

        if($update_user) return redirect()->route('user.success_verification_message');
    }
}
