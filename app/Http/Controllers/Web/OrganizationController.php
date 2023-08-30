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
                                    <a href="/admin/organizations/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
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
        $data = $request->except('_token', 'featured_image', 'icon', 'images');
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

        $count = 1;
        $images = [];
        if($request->images) {
            foreach ($request->images as $key => $image) {
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $count . '.' . $image_file->getClientOriginalExtension();
                $save_file = $image_file->move(public_path() . '/assets/img/organizations/' . $organization->id, $image_file_name);

                array_push($images, $image_file_name);
                $count++;
            }

            $organization->update([
                'images' => count($images) > 0 ? json_encode($images) : null
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
        $data = $request->except('_token', 'featured_image', 'icon', 'images');
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

        $images = $organization->images ? json_decode($organization->images, true) : [];
        $count = $organization->images ? count(json_decode($organization->images, true)) + 1 : 1;

        if($request->has('images')) {
            foreach ($request->images as $key => $image) {
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $count . '.' . $image_file->getClientOriginalExtension();
                $save_file = $image_file->move(public_path() . '/assets/img/organizations/' . $organization->id, $image_file_name);
                array_push($images, $image_file_name);
                $count++;
            }
        }

        $organization->update([
            'featured_image' => $featured_file_name,
            'icon' => $icon_file_name,
            'images' => count($images) > 0 ? json_encode($images) : $organization->images,
        ]);

        if($organization) return back()->withSuccess('Organization updated successfully');

    }

    public function destroy(Request $request) {

    }

    public function removeImage(Request $request) {
        $organization = Organization::where('id', $request->id)->first();
        $images = json_decode($organization->images);
        $image_path = $request->image_path;

        if(is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/organizations/') . $organization->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $organization->update([
            'images' => json_encode(array_values($images))
        ]);

        if($update) {
            return response([
                'status' => TRUE,
                'message' => 'Image successfully remove'
            ]);
        }
    }
}
