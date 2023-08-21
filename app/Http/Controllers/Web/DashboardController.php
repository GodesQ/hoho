<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {
        return view('admin-page.dashboard.dashboard');
    }

    public function testLocation() {
        return view('misc.test-location');
    }
}
