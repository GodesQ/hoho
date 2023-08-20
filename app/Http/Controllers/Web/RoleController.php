<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;
use DataTables;
class RoleController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Role::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/roles/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
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

    }
}
