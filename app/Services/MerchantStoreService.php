<?php

namespace App\Services;

use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantStore;
use App\Models\Merchant;
use App\Models\Organization;
use App\Models\Interest;

use DB;
use InvalidArgumentException;
use Yajra\DataTables\DataTables;

class MerchantStoreService
{

    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function generateDataTable(Request $request, $data)
    {
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('featured_image', function ($row) {
                $defaultImagePath = asset('assets/img/default-image.jpg');

                if ($row->merchant && $row->merchant->featured_image) {
                    $path = asset('assets/img/stores/' . $row->merchant->id . '/' . $row->merchant->featured_image);
                } else {
                    $path = $defaultImagePath;
                }

                if (!File::exists(public_path('assets/img/stores/' . $row->merchant->id . '/' . $row->merchant->featured_image))) {
                    $path = $defaultImagePath;
                }

                return '<img src="' . $path . '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
            })
            ->editColumn('name', function ($row) {
                return ($row->merchant)->name;
            })
            ->editColumn('location', function ($row) {
                if ($row->merchant->address) {
                    return view('components.merchant-location', ['data' => $row]);
                }
                return "-";
            })
            ->editColumn('is_featured', function ($row) {
                if ($row->merchant->is_featured) {
                    return '<span class="badge bg-label-success me-1">Yes</span>';
                } else {
                    return '<span class="badge bg-label-secondary me-1">No</span>';
                }
            })
            ->editColumn('actions', function ($row) {
                return '<div class="dropdown">
                                <a href="'. route('admin.merchants.stores.edit', $row->id) .'" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <a href="javascript:void(0);" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                            </div>';
            })
            ->rawColumns(['actions', 'featured_image', 'is_featured', 'location'])
            ->make(true);
    }

    /**
     * Create a new merchant store using the provided request data.
     *
     * @param Request $request
     * @return array
     */
    public function createMerchantStore($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->except('_token', 'featured_image', 'main_featured_image', 'images', 'brochure');

            $merchant = Merchant::create(array_merge($data, [
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
            ]));

            $images_field = ['featured_image', 'main_featured_image'];
            $path = "/stores/{$merchant->id}/";

            // Upload images based on fields
            $this->fileService->uploadAndSaveFiles($path, $images_field, $merchant->name, $merchant, $request);

            $images = [];

            if ($request->has('images')) {
                foreach ($request->file('images') as $count => $image) {
                    $uniqueId = Str::random(5) . time();
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs($path, $image, $image_file_name);
                    $images[] = $image_file_name;
                }
            }

            $merchant_store_data = array_merge($data, [
                'merchant_id' => $merchant->id,
                'images' => count($images) > 0 ? json_encode($images) : null,
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]);

            $merchant_store = MerchantStore::create($merchant_store_data);

            DB::commit();

            return [
                'status' => TRUE,
                'merchant' => $merchant,
                'merchant_store' => $merchant_store
            ];

        } catch (ErrorException $e) {
            DB::rollBack();
            throw $e;
        } catch (InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateMerchantStore(Request $request)
    {   
        try {
            $data = $request->except('_token', 'images', 'featured_image', 'brochure');
            $store = MerchantStore::where('id', $request->id)->with('merchant')->firstOrFail();

            $update_store = $store->update(array_merge($data, [
                'interests' => $request->has('interests') ? json_encode($request->interests) : null,
            ]));

            $update_merchant = $store->merchant->update(array_merge($data, [
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
            ]));

            $images_field = ['featured_image', 'main_featured_image'];
            $path = "/stores/{$store->merchant->id}/";

            // Upload images based on fields
            $this->fileService->uploadAndSaveFiles($path, $images_field, $store->merchant->name, $store->merchant, $request);

            $images = $store->images ? json_decode($store->images) : [];

            if ($request->has('images')) {
                foreach ($request->images as $key => $image) {
                    $uniqueId = Str::random(5);
                    $image_file = $image;
                    $path_folder = 'stores/' . $store->merchant->id . '/';
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                    $save_file = Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    $images[] = $image_file_name;
                }

                $update_store = $store->update([
                    'images' => count($images) > 0 ? json_encode($images) : $store->images,
                ]);
            }

            return [
                'status' => TRUE,
                'merchant' => $store->merchant,
                'merchant_store' => $store
            ];
        } catch (ErrorException $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
