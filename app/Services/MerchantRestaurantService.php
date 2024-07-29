<?php

namespace App\Services;

use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Merchant;
use App\Models\MerchantRestaurant;

use Yajra\DataTables\DataTables;
use DB;

class MerchantRestaurantService
{
    private $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function createMerchantRestaurant($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->except('_token', 'featured_image', 'main_featured_image', 'images', 'brochure');
            $merchant = Merchant::create(array_merge($data, ['is_active' => $request->has('is_active'), 'is_featured' => $request->has('is_featured')]));

            $image_fields = ['featured_image', 'main_featured_image'];
            $path = "/restaurants/{$merchant->id}/";

            // Upload images based on fields
            $this->fileService->uploadAndSaveFiles($path, $image_fields, $merchant->name, $merchant, $request);

            $images = [];

            if ($request->has('images')) {
                foreach ($request->file('images') as $count => $image) {
                    $uniqueId = Str::random(5);
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image->getClientOriginalExtension();
                    FileService::upload($path, $image_file_name, $image);
                    $images[] = $image_file_name;
                }
            }

            $merchant_restaurant_data = array_merge($data, [
                'merchant_id' => $merchant->id,
                'images' => count($images) > 0 ? json_encode($images) : null,
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]);

            $merchant_restaurant = MerchantRestaurant::create($merchant_restaurant_data);

            DB::commit();

            return [
                'status' => TRUE,
                'merchant' => $merchant,
                'merchant_restaurant' => $merchant_restaurant
            ];
        } catch (ErrorException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateMerchantRestaurant(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'images', 'featured_image', 'main_featured_image');
            $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->firstOrFail();

            $update_restaurant = $restaurant->update(array_merge($data, [
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]));

            $image_fields = ['featured_image', 'main_featured_image'];
            $path = "/restaurants/{$restaurant->merchant->id}/";

            // Upload images based on fields
            $this->fileService->uploadAndSaveFiles($path, $image_fields, $restaurant->merchant->name, $restaurant->merchant, $request);

            $images = $restaurant->images ? json_decode($restaurant->images) : [];

            if ($request->has('images')) {
                foreach ($request->images as $key => $image) {
                    $uniqueId = Str::random(5);
                    $image_file = $image;
                    $path_folder = "/restaurants/{$restaurant->merchant->id}/";
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    array_push($images, $image_file_name);
                }

                $update_restaurant = $restaurant->update([
                    'images' => count($images) > 0 ? json_encode($images) : $restaurant->images,
                ]);
            }

            $update_merchant = $restaurant->merchant->update(array_merge($data, [
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
            ]));

            if ($update_restaurant && $update_merchant) {
                return [
                    'status' => TRUE,
                    'merchant' => $restaurant->merchant,
                    'merchant_restaurant' => $restaurant
                ];
            }

            return [
                'status' => FALSE,
                'merchant' => null,
                'merchant_restaurant' => null
            ];
        });
    }

    public function _generateDataTable($data)
    {
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('featured_image', function ($row) {
                $defaultImagePath = asset('assets/img/default-image.jpg');

                if ($row->merchant && $row->merchant->featured_image) {
                    $path = asset('assets/img/restaurants/' . $row->merchant->id . '/' . $row->merchant->featured_image);
                } else {
                    $path = $defaultImagePath;
                }

                if (!File::exists(public_path('assets/img/restaurants/' . $row->merchant->id . '/' . $row->merchant->featured_image))) {
                    $path = $defaultImagePath;
                }

                return '<img src="' . $path . '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
            })
            ->addColumn('name', function ($row) {
                return optional($row->merchant)->name;
            })
            ->addColumn('location', function ($row) {
                if ($row->merchant->address) {
                    return view('components.merchant-location', ['data' => $row]);
                }

                return '-';
            })
            ->addColumn('is_featured', function ($row) {
                if ($row->merchant->is_featured) {
                    return '<span class="badge bg-label-success me-1">Yes</span>';
                } else {
                    return '<span class="badge bg-label-secondary me-1">No</span>';
                }
            })
            ->addColumn('actions', function ($row) {
                return '<div class="dropdown">
                            <a href="/admin/merchants/restaurants/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                            <a href="javascript:void(0);" id=" ' . $row->id . ' " class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                        </div>';
            })
            ->rawColumns(['actions', 'featured_image', 'is_featured'])
            ->make(true);
    }
}

