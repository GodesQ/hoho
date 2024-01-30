<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreRequest;
use App\Http\Requests\Organization\UpdateRequest;
use App\Models\Organization;
use App\Services\OrganizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class OrganizationController extends Controller
{
    protected $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->organizationService->getOrganizations($request);
            return $this->organizationService->generateDataTable($request, $data);
        }

        return view('admin-page.organizations.list-organization');
    }

    public function create(Request $request)
    {
        return view('admin-page.organizations.create-organization');
    }

    public function store(StoreRequest $request)
    {
        $organization = $this->organizationService->createOrganization($request);
        if ($organization)
            return redirect()->route('admin.organizations.edit', $organization->id)->with('success', 'Organization Created Successfully');
    }

    public function edit(Request $request)
    {
        $organization = Organization::where('id', $request->id)->firstOrFail();
        return view('admin-page.organizations.edit-organization', compact('organization'));
    }

    public function update(UpdateRequest $request)
    {
        $organization = $this->organizationService->updateOrganization($request);
        if ($organization)
            return back()->withSuccess('Organization updated successfully');
    }

    public function destroy(Request $request)
    {

        $delete_organization = $this->organizationService->deleteOrganization($request, $request->id);

        if ($delete_organization) {
            return response([
                'status' => true,
                'message' => 'Organization Deleted Successfully'
            ]);
        }
    }

    public function removeImage(Request $request)
    {   
        $organization = Organization::find($request->id);

        if (!$organization) {
            return response(['status' => false, 'message' => 'Organization not found']);
        }

        $images = json_decode($organization->images, true);
        $image_file_name = $request->image_file_name;

        if (is_array($images)) {
            $key = array_search($image_file_name, $images);

            if ($key !== false) {
                $path = "organizations/{$organization->id}/";
                $this->organizationService->removeImage($request, $path, $image_file_name);
                unset($images[$key]);

                $organization->update([
                    'images' => json_encode(array_values($images))
                ]);

                return response([
                    'status' => true,
                    'message' => 'Image successfully removed'
                ]);
            }
        }

        return response([
            'status' => false,
            'message' => 'Image failed to remove'
        ]);
    }

}