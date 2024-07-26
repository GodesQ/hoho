<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\MerchantStore\StoreRequest;
use App\Http\Requests\MerchantStore\UpdateRequest;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantStore;
use App\Models\Merchant;
use App\Models\Organization;
use App\Models\Interest;

use App\Services\MerchantStoreService;

use InvalidArgumentException;
use Yajra\DataTables\DataTables;
use DB;

class MerchantStoreController extends Controller
{
    protected $merchantStoreService;

    public function __construct(MerchantStoreService $merchantStoreService)
    {
        $this->merchantStoreService = $merchantStoreService;
    }

    /**
     * Retrieves a list of merchant stores and returns it as a DataTables response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
     */
    public function list(Request $request) {
        if($request->ajax()) {
            $search = $request->search;
            $organization_id = $request->organization_id;

            $data = MerchantStore::when($search, function($query) use ($search) {
                $query->whereHas('merchant', function ($merchantQuery) use ($search) {
                    $merchantQuery->where('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($organization_id, function ($query) use ($organization_id) {
                $query->whereHas('merchant', function($q) use ($organization_id) {
                    $q->where('organization_id', $organization_id);
                });
            })
            ->with('merchant');

            return $this->merchantStoreService->generateDataTable($request, $data);

        }

        $organizations = Organization::get();
        return view('admin-page.merchants.stores.list-store', compact('organizations'));
    }

    /**
     * Create a new store.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request) {
        $organizations = Organization::get();
        $interests = Interest::get();
        return view('admin-page.merchants.stores.create-store', compact('organizations', 'interests'));
    }

    /**
     * Store a new merchant store based on the given request.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request) {
        try {
            $result = $this->merchantStoreService->createMerchantStore($request);

            $previousUrl = \URL::previous();
            $previousPath = parse_url($previousUrl, PHP_URL_PATH);

            if ($previousPath === '/merchant_form/store') {
                $admin = Auth::guard('admin')->user();

                if(in_array($admin->role, merchant_roles())) {
                    $admin->update([
                        'merchant_id' => $result['merchant']->id,
                    ]);
                }

                return redirect()->route('admin.dashboard')->withSuccess('Merchant Store Created Successfully');
            }

            return redirect()->route('admin.merchants.stores.edit', $result['merchant_store']->id)->withSuccess('Merchant Store created successfully');
    
        } catch (ErrorException $e) {
            return back()->with('fail', $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return back()->with('fail', $e->getMessage());
        }
    }

    /**
     * Edit the merchant store based on the given request.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request) {
        $organizations = Organization::get();
        $store = MerchantStore::where('id', $request->id)->with('merchant')->first();
        $interests = Interest::get();

        return view('admin-page.merchants.stores.edit-store', compact('store', 'organizations', 'interests'));
    }

    public function update(UpdateRequest $request) {
        $result = $this->merchantStoreService->updateMerchantStore($request);

        if($result['status']) {
            return back()->with('success', 'The merchant store successfully update.');
        }

        return back()->with('fail', 'The merchant store failed to update.');
    }

    public function destroy(Request $request) {
        $store = MerchantStore::where('id', $request->id)->with('merchant')->firstOrFail();

        $old_upload_image = public_path('assets/img/stores/') . $store->merchant->id . '/' . $store->merchant->featured_image;
        if($old_upload_image) {
            @unlink($old_upload_image);
        }

        // Remove all files from the directory
        $directory = public_path('assets/img/stores/') . $store->merchant->id;
        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        if (is_dir($directory)) {
            @rmdir($directory);
        }

        $store->merchant->delete();
        
        return response([
            'status' => true,
            'message' => 'Store Deleted Successfully'
        ]);
    }

    public function removeImage(Request $request) {
        $store = MerchantStore::where('id', $request->id)->first();
        $images = json_decode($store->images);
        $image_path = $request->image_path;
        if(is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/stores/') . $store->merchant->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $store->update([
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
