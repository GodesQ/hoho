<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\DeleteAccountOTPMail;
use App\Models\DeleteAccountToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DeleteAccountController extends Controller
{
    public function index(Request $request)
    {
        return view('admin-page.auth.delete-account.index');
    }

    public function confirmDeleteAccountEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', new \App\Rules\ExistAcrossTables('users', 'admins', 'email')],
        ]);

        $code = $this->generateOTP();

        $delete_account_model = DeleteAccountToken::create([
            'email' => $request->email,
            'code' => $code,
            'token' => Str::random(10),
            'expired_at' => Carbon::now()->addMinutes(60),
        ]);

        Mail::to($delete_account_model->email)->send(new DeleteAccountOTPMail($delete_account_model->code, $delete_account_model->email));

        return redirect()->route('delete-account.otp', ['token' => $delete_account_model->token, 'email' => $delete_account_model->email])
        ->withSuccess('We sent an OTP in your email address provided earlier.');
    }

    public function showOTPInputForm(Request $request)
    {
        $token = $request->token;

        $delete_account_model = DeleteAccountToken::where('token', $token)->first();
        abort_if(!$delete_account_model, 404);

        $expiredAt = Carbon::parse($delete_account_model->expired_at);
        abort_if($expiredAt->isPast(), 404);

        $email = $request->email;

        return view('admin-page.auth.delete-account.pincode-form', compact('email'));
    }

    public function confirmOTP(Request $request) {
        $user = User::where('email', $request->email)->first();
        abort_if(!$user, 404);

        $user->tokens()->delete();
        $user->delete();

        $delete_account_model = DeleteAccountToken::where('email', $request->email)->where('code', $request->code)->first();
        $delete_account_model->delete();

        return redirect()->route('delete-account.message')->withSuccess('Account deleted successfully.');

    }

    private function generateOTP()
    {
        // Set the minimum and maximum values for a 6-digit OTP
        $min = 100000;
        $max = 999999;

        // Generate a random 6-digit OTP
        $otp = rand($min, $max);

        // Return the generated OTP
        return $otp;
    }

}
