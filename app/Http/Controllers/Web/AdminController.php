<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Admin;
use App\Models\Role;

use DataTables;

class AdminController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Admin::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/admins/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }
        return view('admin-page.admins.list-admin');
    }

    public function create(Request $request) {
        $roles = Role::get();
        return view('admin-page.admins.create-admin', compact('roles'));
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'password');

        $admin = Admin::create(array_merge($data, [
            'password' => Hash::make($request->password)
        ]));

        if($admin) return redirect()->route('admin.admins.edit', $admin->id)->withSuccess('Admin created successfully');
    }

    public function edit(Request $request) {
        $roles = Role::get();
        $admin = Admin::where('id', $request->id)->first();
        return view('admin-page.admins.edit-admin', compact('admin', 'roles'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $admin = Admin::where('id', $request->id)->first();

        $update_admin = $admin->update($data);

        return back()->withSuccess('Admin updated successfully');
    }

    public function destroy(Request $request) {

    }
}
