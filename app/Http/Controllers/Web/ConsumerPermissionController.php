<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ConsumerPermission;
use Illuminate\Http\Request;

class ConsumerPermissionController extends Controller
{
    public function update(Request $request) {
        $consumer_id = $request->consumer_id;

        if($request->permissions) {
            foreach ($request->permissions as $key => $permission) {
                ConsumerPermission::updateOrCreate(
                    ['permission_id' => $permission],
                    ['consumer_id' => $consumer_id],
                );
            }
        }

        return back()->withSuccess('Permission synced successfully');
    }
}
