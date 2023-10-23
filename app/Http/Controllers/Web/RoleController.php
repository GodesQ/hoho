<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Role::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/roles/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id=' .$row->id. '  class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);

        }
        return view('admin-page.roles.list-role');
    }

    public function create(Request $request) {
        return view('admin-page.roles.create-role');

    }

    public function store(Request $request) {
        $data = $request->except('_token');
        $role = Role::create($data);

        if($role) return redirect()->route('admin.roles.edit', $role->id)->withSuccess('Role created successfully');
    }

    public function edit(Request $request) {
        $role = Role::where('id', $request->id)->first();
        return view('admin-page.roles.edit-role', compact('role'));

    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $role = Role::where('id', $request->id)->first();

        $update_role = $role->update($data);
        if($update_role) return back()->withSuccess('Role updated successfully');
    }

    public function destroy(Request $request) {
        $roles = Role::findOrFail($request->id);

        $upload_image = public_path('assets/img/roles/'). $roles->ticket_image;

        if($upload_image) {
             @unlink($upload_image);
        }

        $remove = $roles->delete();

        if($remove) {
            return response([
               'status' => true,
               'message' => 'Ticket Pass Deleted Successfully'
            ]);
        }
    }
}
