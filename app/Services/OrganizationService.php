<?php
namespace App\Services;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class OrganizationService
{
    protected $fileManagerService;

    public function __construct(FileManagerService $fileManagerService)
    {
        $this->fileManagerService = $fileManagerService;
    }

    public function getOrganizations(Request $request)
    {
        $data = Organization::query();

        if ($request->is('api/*')) {
            $data = $data->where('is_active', 1)->get();
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public function getOrganization($id)
    {
        $organization = Organization::findOrFail($id);
        return $organization;
    }

    public function createOrganization(Request $request)
    {
        try {
            $data = $request->except('_token', 'featured_image', 'icon', 'images');

            $organizationData = ['is_active' => $request->has('is_active')];

            $organization = Organization::create(array_merge($data, $organizationData));

            $path = "organizations/{$organization->id}/";
            $file_name = Str::snake(Str::lower($request->name));
            $image_fields = ['featured_image', 'icon', 'images'];

            $this->uploadAndUpdateImage($request, $path, $file_name, $image_fields, $organization);

            return $organization;

        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                return response(['status' => 'error', 'message' => $e->getMessage()]);
            }

            return back()->with('fail', $e->getMessage());
        }
    }

    public function updateOrganization(Request $request)
    {
        try {
            $data = $request->except('_token', 'featured_image', 'icon', 'images');
            $organization = Organization::where('id', $request->id)->firstOrFail();

            $organization->update(array_merge($data, [
                'is_active' => $request->has('is_active')
            ]));

            $path = "organizations/{$organization->id}/";
            $file_name = Str::snake(Str::lower($request->name));
            $image_fields = ['featured_image', 'icon', 'images'];

            $this->uploadAndUpdateImage($request, $path, $file_name, $image_fields, $organization);

            return $organization;

        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                return response(['status' => 'error', 'message' => $e->getMessage()]);
            }

            return back()->with('fail', $e->getMessage());
        }
    }

    public function deleteOrganization(Request $request, $id) {
        try {
            $organization = Organization::where('id', $id)->firstOrFail();

            // Remove all files from the directory
            $directory = public_path('assets/img/organizations/') . $organization->id;
            $files = glob($directory . '/*');

            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }

            // Now try to remove the directory
            if (is_dir($directory)) {
                @rmdir($directory);
            }

            $delete_organization = $organization->delete();

            return $delete_organization;

        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                return response(['status' => 'error', 'message' => $e->getMessage()]);
            }

            return back()->with('fail', $e->getMessage());
        }
    }

    public function generateDataTable(Request $request, $data)
    {
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                if ($row->is_active) {
                    return '<span class="badge bg-label-success me-1">Active</span>';
                } else {
                    return '<span class="badge bg-label-warning me-1">Inactive</span>';
                }
            })
            ->addColumn('actions', function ($row) {
                return '<div class="dropdown">
                                    <a href="/admin/organizations/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    private function uploadAndUpdateImage($request, $path, $file_name, $fields, $organization)
    {
        try {
            foreach ($fields as $field) {
                if ($request->hasFile($field) && $field != 'images') {
                    $image_file_name = "{$file_name}_{$field}." . $request->file($field)->getClientOriginalExtension();

                    $this->removeImage($request, $path, $organization->{$field});

                    $this->handleUploadImage($request, $path, $image_file_name, $request->file($field));

                    $organization->update([$field => $image_file_name]);
                }
            }

            if ($request->has('images')) {
                $images = $organization->images ? json_decode($organization->images) : [];
                foreach ($request->images as $image) {
                    $count = Str::random(5);
                    $image_file_name = "{$file_name}_image_{$count}." . $image->getClientOriginalExtension();
                    $this->handleUploadImage($request, $path, $image_file_name, $image);
                    array_push($images, $image_file_name);
                }

                $organization->update(['images' => count($images) > 0 ? json_encode($images) : null]);
            }
        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                return response(['status' => 'error', 'message' => $e->getMessage()]);
            }

            return back()->with('fail', $e->getMessage());
        }
    }

    public function removeImage(Request $request, $path, $file_name)
    {   
        try {
            $old_image_path = $path . '/' . $file_name;
            if (Storage::disk('public')->exists($old_image_path)) {
                return Storage::disk('public')->delete($old_image_path);
            }
        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                return response(['status' => 'error', 'message' => $e->getMessage()]);
            }

            return back()->with('fail', $e->getMessage());
        }
        
    }

    private function handleUploadImage(Request $request, $path, $file_name, $file)
    {
        try {
            $save_file = Storage::disk('public')->putFileAs($path, $file, $file_name);

            if ($save_file)
                return $save_file;

        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                return response(['status' => 'error', 'message' => $e->getMessage()]);
            }

            return back()->with('fail', $e->getMessage());
        }
    }
}
?>