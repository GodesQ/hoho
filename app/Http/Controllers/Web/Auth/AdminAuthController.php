<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Admin;

class AdminAuthController extends Controller
{
    public function login(Request $request) {
        return view('admin-page.auth.login');
    }

    public function saveLogin(Request $request) {
        $credentials = $request->except('_token');
        if (Auth::guard('admin')->attempt(array_merge($credentials))) {
            return redirect()->route('admin.dashboard')->with('success', 'Login Successfully');
        } else {
            return back()->with('fail', 'Invalid Credentials.');
        }
    }
}
