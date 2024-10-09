<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attraction\StoreRequest;
use App\Services\AttractionService;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Attraction;
use App\Models\Organization;
use App\Models\ProductCategory;
use App\Models\Interest;
use App\Models\Merchant;

use Yajra\DataTables\DataTables;

class AttractionController extends Controller
{
    protected $attractionService;

    public function __construct(AttractionService $attractionService)
    {
        $this->attractionService = $attractionService;
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->attractionService->getAttractions($request);
            return $this->attractionService->generateDataTables($data);
        }

        $organizations = Organization::get();
        return view('admin-page.attractions.list-attraction', compact('organizations'));
    }

    public function create(Request $request)
    {
        $organizations = Organization::get();
        $interests = Interest::latest()->get();
        $product_categories = ProductCategory::latest()->get();
        $attractions = Attraction::latest()->get();
        $hotels = Merchant::where('type', 'Hotel')->get();
        $stores = Merchant::where('type', 'Store')->get();
        $restaurants = Merchant::where('type', 'Restaurant')->get();

        return view('admin-page.attractions.create-attraction', compact('organizations', 'interests', 'product_categories', 'attractions', 'hotels', 'stores', 'restaurants'));
    }

    public function store(StoreRequest $request)
    {
        try {
            $attraction = $this->attractionService->createAttraction($request);
            return redirect()->route('admin.attractions.edit', $attraction->id)->withSuccess('Attraction created successfully');
        } catch (ErrorException $e) {
            return back()->with('fail', $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        $attraction = Attraction::findOrFail($request->id);
        $organizations = Organization::get();
        $product_categories = ProductCategory::get();
        $interests = Interest::latest()->get();
        $attractions = Attraction::latest()->get();
        $hotels = Merchant::where('type', 'Hotel')->get();
        $stores = Merchant::where('type', 'Store')->get();
        $restaurants = Merchant::where('type', 'Restaurant')->get();

        return view('admin-page.attractions.edit-attraction', compact('attraction', 'organizations', 'product_categories', 'interests', 'attractions', 'hotels', 'stores', 'restaurants'));
    }

    public function update(Request $request)
    {
        $update_attraction = $this->attractionService->updateAttraction($request);
        if ($update_attraction)
            return back()->withSuccess('Attraction Updated Successfully');
    }

    public function destroy(Request $request)
    {
        $remove_attraction = $this->attractionService->destroyAttraction($request);
        if ($remove_attraction) {
            return response([
                'status' => true,
                'message' => 'Attraction deleted successfully'
            ]);
        }
    }

    public function removeImage(Request $request)
    {
        $attraction = Attraction::where('id', $request->id)->first();
        $images = json_decode($attraction->images);
        $image_path = $request->image_path;

        if (is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/attractions/') . $attraction->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $attraction->update([
            'images' => json_encode(array_values($images))
        ]);

        if ($update) {
            return response([
                'status' => TRUE,
                'message' => 'Image successfully remove'
            ]);
        }
    }

    public function featured_attractions(Request $request)
    {
        $attractions = Attraction::select('id', 'name', 'is_featured', 'featured_arrangement_number')
            ->where('organization_id', $request->organization_id)
            ->where('is_featured', 1)
            ->orderBy('featured_arrangement_number', 'asc')
            ->get();

        $attractions->each(function ($attraction) {
            $attraction->setAppends([]);
        });

        return response()->json([
            'status' => TRUE,
            'attractions' => $attractions,
        ]);
    }

    public function saveFeaturedAttractions(Request $request) {
        foreach ($request->sorted_ids as $key => $id) {
            $attraction = Attraction::where('id', $id)->first();
            $attraction->update([
                'featured_arrangement_number' => $key + 1,
            ]);
        }

        return response()->json([
            'status'=> TRUE,
            'message'=> 'Featured Attractions Updated Successfully'
        ]);
    }
}
