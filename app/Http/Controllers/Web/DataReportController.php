<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class DataReportController extends Controller
{
    public function user_demographics(Request $request) {
        return view('admin-page.reports.user_demographics');
    }
}
