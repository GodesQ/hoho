<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Organization;

use DataTables;

class OrganizationController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Organization::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function($row) {
                        if($row->is_active) {
                            return '<span class="badge bg-label-success me-1">Active</span>';
                        } else {
                            return '<span class="badge bg-label-warning me-1">In Active</span>';
                        }
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/organizations/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['actions', 'status'])
                    ->make(true);
        }

        return view('admin-page.organizations.list-organization');
    }

    public function create(Request $request) {
        return view('admin-page.organizations.create-organization');
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'featured_image', 'icon');

        $organization = Organization::create(array_merge($data, [
            'is_active' => $request->has('is_active')
        ]));

        if($request->hasFile('featured_image')) {
            $featured_file = $request->file('featured_image');
            $featured_file_name = Str::snake(Str::lower($request->name)) . '.' . $featured_file->getClientOriginalExtension();
            $save_file = $featured_file->move(public_path() . '/assets/img/organizations/' . $organization->id, $featured_file_name);

            $organization->update([
                'featured_image' => $featured_file_name
            ]);
        }

        if($request->hasFile('icon')) {
            $icon_file = $request->file('icon');
            $icon_file_name = Str::snake(Str::lower($request->name)) . '_icon' . '.' . $icon_file->getClientOriginalExtension();
            $save_file = $icon_file->move(public_path() . '/assets/img/organizations/' . $organization->id, $icon_file_name);

            $organization->update([
                'icon' => $icon_file_name
            ]);
        }

        if($organization) return redirect()->route('admin.organizations.edit', $organization->id)->withSuccess('Organization created successfully');
        abort(400);
    }

    public function edit(Request $request) {
        $organization = Organization::where('id', $request->id)->firstOrFail();
        return view('admin-page.organizations.edit-organization', compact('organization'));
    }

    public function update(Request $request) {
        $data = $request->except('_token', 'featured_image', 'icon');
        $organization = Organization::where('id', $request->id)->firstOrFail();

        $organization->update(array_merge($data, [
            'is_active' => $request->has('is_active')
        ]));

        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $featured_file_name = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/organizations/') . $organization->id . '/' . $organization->featured_image;
            if($old_upload_image) {
                $remove_image = @unlink($old_upload_image);
            }
            $save_file = $file->move(public_path() . '/assets/img/organizations/' . $organization->id, $featured_file_name);
        } else {
            $featured_file_name = $organization->featured_image;
        }

        if($request->hasFile('icon')) {
            $icon_file = $request->file('icon');
            $name = Str::snake(Str::lower($request->name));
            $icon_file_name = $name . '.' . $icon_file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/organizations/') . $organization->id . '/' . $organization->icon;
            if($old_upload_image) {
                $remove_image = @unlink($old_upload_image);
            }
            $save_file = $icon_file->move(public_path() . '/assets/img/organizations/' . $organization->id, $icon_file_name);
        } else {
            $icon_file_name = $organization->icon;
        }

        $organization->update([
            'featured_image' => $featured_file_name,
            'icon' => $icon_file_name
        ]);

        if($organization) return back()->withSuccess('Organization updated successfully');

    }

    public function destroy(Request $request) {

    }
}
