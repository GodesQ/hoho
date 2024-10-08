<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Permission;
use App\Models\Role;

use Yajra\DataTables\DataTables;
use DB;

class PermissionController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Permission::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('roles', function($row) {
                        $roles = json_decode($row->roles);
                        $output = '';
                        foreach ($roles as $key => $role) {
                           $output .= "<div class='badge bg-primary m-1'>$role</div>";
                           if($key == 2) {
                                $output .= "<div class='badge bg-primary m-1'>Other Roles...</div>";
                                break;
                           }
                        }
                        return $output;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/permissions/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="'.$row->id.'" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['roles', 'actions'])
                    ->make(true);
        }

        return view('admin-page.permissions.list-permission');
    }

    public function create(Request $request) {
        $roles = Role::get();
        return view('admin-page.permissions.create-permission', compact('roles'));
    }

    public function store(Request $request) {
        $permission = Permission::create([
            'permission_name' => $request->permission_name,
            'roles' => $request->has('roles') ? json_encode($request->roles) : json_encode([Role::SUPER_ADMIN])
        ]);

        if($permission) return redirect()->route('admin.permissions.list')->withSuccess('Permission created successfully');
    }

    public function edit(Request $request) {
        $permission = Permission::where('id', $request->id)->first();
        $roles = Role::get();

        return view('admin-page.permissions.edit-permission', compact('roles', 'permission'));
    }

    public function update(Request $request) {
        $permission = Permission::where('id', $request->id)->first();

        $update = $permission->update([
            'permission_name' => $request->permission_name,
            'roles' => $request->has('roles') ? json_encode($request->roles) : json_encode([Role::SUPER_ADMIN])
        ]);

        if($update) return back()->withSuccess('Permission updated successfully');
    }

    public function destroy(Request $request) {
        $permissions = Permission::findOrFail($request->id);

        $upload_image = public_path('assets/img/permissions/') . $permissions->id . '/' . $permissions->featured_image;

        if($upload_image) {
             @unlink($upload_image);
        }

        $remove = $permissions->delete();

        if($remove) {
            return response([
                'status' => true,
                'message' => 'Permission Deleted Successfully'
            ]);
        }
    }

}
