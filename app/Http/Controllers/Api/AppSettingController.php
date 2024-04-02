<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function checkMaintenanceMode(Request $request) {
        $setting = AppSetting::where('code', 'hoho_mobile')->first();

        return response([
            'maintenance_mode' => $setting->maintenance_mode ? true : false,
        ]);

    }
}
