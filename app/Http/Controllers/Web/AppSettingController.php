<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function update(Request $request) {
        $setting = AppSetting::where('code', 'hoho_mobile')->first();
        $setting->update([
            'maintenance_mode' => $request->maintenance_mode,
        ]);

        return response([
            'message' => 'Maintenance Mode updated successfully.'
        ]);
    }
}
