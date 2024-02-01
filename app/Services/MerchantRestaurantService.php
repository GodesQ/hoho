<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Merchant;
use App\Models\MerchantRestaurant;

use Yajra\DataTables\DataTables;
use DB;

class MerchantRestaurantService
{
    public function CreateMerchantRestaurant(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'featured_image', 'main_featured_image', 'images');
            $merchant = Merchant::create(array_merge($data, ['is_active' => $request->has('is_active'), 'is_featured' => $request->has('is_featured')]));
            $file_name = null;

            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = Str::snake(Str::lower($request->name));
                $file_name = $name . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . '/assets/img/restaurants/' . $merchant->id, $file_name);

                $merchant->update([
                    'featured_image' => $file_name,
                ]);
            }

            if ($request->hasFile('main_featured_image')) {
                $main_featured_file = $request->file('main_featured_image');
                $name = Str::snake(Str::lower($request->name)) . '_main_featured_image';
                $file_name = $name . '.' . $main_featured_file->getClientOriginalExtension();
                $main_featured_file->move(public_path() . '/assets/img/restaurants/' . $merchant->id, $file_name);

                $merchant->update([
                    'main_featured_image' => $file_name,
                ]);
            }

            if ($request->hasFile('brochure')) {
                $brochure_file = $request->file('brochure');
                $name = Str::snake(Str::lower($request->name)) . '_brochure_' . time();
                $brochure_file_name = $name . '.'. $brochure_file->getClientOriginalExtension();
                $brochure_file->move(public_path() . '/assets/img/restaurants/' . $merchant->id, $brochure_file_name);
            }

            $images = array();

            if ($request->has('images')) {
                foreach ($request->file('images') as $count => $image) {
                    $uniqueId = Str::random(5);
                    $path_folder = 'restaurants/' . $merchant->id . '/';
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    $images[] = $image_file_name;
                }
            }

            $merchant_restaurant_data = array_merge($data, [
                'merchant_id' => $merchant->id,
                'brochure' => $brochure_file_name ?? null,
                'images' => count($images) > 0 ? json_encode($images) : null,
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]);

            $merchant_restaurant = MerchantRestaurant::create($merchant_restaurant_data);

            if ($merchant_restaurant) {
                // return redirect()->route('admin.merchants.restaurants.edit', $merchant_restaurant->id)->withSuccess('Restaurant created successfully');
                return [
                    'status' => TRUE,
                    'merchant' => $merchant,
                    'merchant_restaurant' => $merchant_restaurant
                ];
            }

            return [
                'status' => FALSE,
                'merchant' => null,
                'merchant_restaurant' => null
            ];
        });
    }

    public function UpdateMerchantRestaurant(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'images', 'featured_image');
            $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->firstOrFail();

            $update_restaurant = $restaurant->update(array_merge($data, [
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]));

            $images = $restaurant->images ? json_decode($restaurant->images) : [];

            if ($request->has('images')) {
                foreach ($request->images as $key => $image) {
                    $uniqueId = Str::random(5);
                    $image_file = $image;
                    $path_folder = 'restaurants/' . $restaurant->merchant->id . '/';
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                    $save_file = Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    array_push($images, $image_file_name);
                }

                $update_restaurant = $restaurant->update([
                    'images' => count($images) > 0 ? json_encode($images) : $restaurant->images,
                ]);
            }

            if ($request->hasFile('brochure')) {
                $brochure_file = $request->file('brochure');
                $name = Str::snake(Str::lower($request->name)) . '_brochure_' . time();
                $brochure_file_name = $name . '.'. $brochure_file->getClientOriginalExtension();
                $old_upload_brochure = public_path('assets/img/restaurants/') . $restaurant->merchant->id . '/' . $restaurant->brochure;

                if($old_upload_brochure) {
                    $remove_image = @unlink($old_upload_brochure);
                }

                $brochure_file->move(public_path() . '/assets/img/restaurants/' . $restaurant->merchant->id, $brochure_file_name);

                $update_restaurant = $restaurant->update([
                    'brochure' => $brochure_file_name,
                ]);
            }

            // Save if the featured image exist in request
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = Str::snake(Str::lower($request->name));
                $file_name = $name . '.' . $file->getClientOriginalExtension();

                $old_upload_image = public_path('assets/img/restaurants/') . $restaurant->merchant->id . '/' . $restaurant->merchant->featured_image;
                if ($old_upload_image) {
                    $remove_image = @unlink($old_upload_image);
                }
                $save_file = $file->move(public_path() . '/assets/img/restaurants/' . $restaurant->merchant->id, $file_name);
            } else {
                $file_name = $restaurant->merchant->featured_image;
            }

            if ($request->hasFile('main_featured_image')) {
                $file = $request->file('main_featured_image');
                $name = Str::snake(Str::lower($request->name)) . '_main_featured_image';
                $main_featured_file_name = $name . '.' . $file->getClientOriginalExtension();
                $old_upload_image = public_path('assets/img/restaurants/') . $restaurant->merchant->id . '/' . $restaurant->merchant->main_featured_image;

                if ($old_upload_image) {
                    $remove_image = @unlink($old_upload_image);
                }

                $save_file = $file->move(public_path() . '/assets/img/restaurants/' . $restaurant->merchant->id, $main_featured_file_name);
            } else {
                $main_featured_file_name = $restaurant->merchant->main_featured_image;
            }

            $update_merchant = $restaurant->merchant->update(array_merge($data, [
                'featured_image' => $file_name,
                'main_featured_image' => $main_featured_file_name,
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
                if ($row->merchant) {
                    if ($row->merchant->featured_image) {
                        $path = '../../../assets/img/restaurants/' . $row->merchant->id . '/' . $row->merchant->featured_image;
                        return '<img src="' . $path . '" width="50" height="50" style="object-fit: cover;" />';
                    } else {
                        $path = '../../../assets/img/' . 'default-image.jpg';
                        return '<img src="' . $path . '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
                    }
                } else {
                    $path = '../../../assets/img/' . 'default-image.jpg';
                    return '<img src="' . $path . '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
                }
            })
            ->addColumn('name', function ($row) {
                return optional($row->merchant)->name;
            })
            ->addColumn('nature_of_business', function ($row) {
                return optional($row->merchant)->nature_of_business;
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

