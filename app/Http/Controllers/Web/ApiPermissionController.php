<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiPermission\StoreRequest;
use App\Models\ApiPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ApiPermissionController extends Controller
{
    public function index(Request $request) {

        if($request->ajax()) {
            $data = ApiPermission::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn("name", function ($row) {
                    return str_replace("_", " ", $row->name);
                })
                ->editColumn("created_at", function ($row) {
                    return Carbon::parse($row->created_at)->format("F d, Y");
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                <a href="/admin/api-permissions/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                            </div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view("admin-page.api-permissions.list-api-permission");
    }

    public function create() {
        return view('admin-page.api-permissions.create-api-permission');
    }

    public function store(StoreRequest $request) {
        $data = $request->validated();
        
        $permission = ApiPermission::create($data);

        return redirect()->route('admin.api_permissions.edit', $permission->id)->withSuccess('Permission added successfully.');
    }
    
    public function edit(Request $request, $id) {
        $permission = ApiPermission::findOrFail($id);
        return view('admin-page.api-permissions.edit-api-permission', compact('permission'));
    }

    public function update(StoreRequest $request, $id) {
        $data = $request->validated();
        $permission = ApiPermission::findOrFail($id);

        $permission->update($data);

        return back()->withSuccess('Permission updated successfully');
    }

    public function destroy(Request $request, $id) {
        
    }
}
