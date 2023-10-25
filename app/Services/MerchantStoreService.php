<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantStore;
use App\Models\Merchant;
use App\Models\Organization;
use App\Models\Interest;

use DB;

class MerchantStoreService {

    /**
     * Create a new merchant store using the provided request data.
     *
     * @param Request $request
     * @return array
     */
    public function CreateMerchantStore(Request $request) {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'featured_image', 'images');

            $merchant = Merchant::create(array_merge($data, [
                'is_active' => $request->has('is_active')
            ]));

            $file_name = null;

            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = Str::snake(Str::lower($request->name));
                $file_name = $name . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . '/assets/img/stores/' . $merchant->id, $file_name);

                $merchant->update([
                    'featured_image' => $file_name,
                ]);
            }

            $images = array();

            if ($request->has('images')) {
                foreach ($request->file('images') as $count => $image) {
                    $uniqueId = Str::random(5);
                    $path_folder = 'stores/' . $merchant->id . '/';
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    $images[] = $image_file_name;
                }
            }

            $merchant_store_data = array_merge($data, [
                'merchant_id' => $merchant->id,
                'images' => count($images) > 0 ? json_encode($images) : null,
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]);

            $merchant_store = MerchantStore::create($merchant_store_data);

            if ($merchant_store) {
                return [
                    'status' => TRUE,
                    'merchant' => $merchant,
                    'merchant_store' => $merchant_store
                ];
                // return redirect()->route('admin.merchants.stores.edit', $merchant_store->id)->withSuccess('Store created successfully');
            }

            return [
                'status' => FALSE,
                'merchant' => null,
                'merchant_store' => null
            ];
        });
    }

    public function UpdateMerchantStore(Request $request) {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'images', 'featured_image');
            $store = MerchantStore::where('id', $request->id)->with('merchant')->firstOrFail();

            $update_store = $store->update(array_merge($data, [
                'interests' => $request->has('interests') ? json_encode($request->interests) : null,
            ]));

            $images = $store->images ? json_decode($store->images) : [];

            if($request->has('images')) {
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

            // Save if the featured image exist in request
            if($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = Str::snake(Str::lower($request->name));
                $file_name = $name . '.' . $file->getClientOriginalExtension();
                $old_upload_image = public_path('assets/img/stores/') . $store->merchant->id . '/' . $store->merchant->featured_image;

                if($old_upload_image) {
                    $remove_image = @unlink($old_upload_image);
                }

                $save_file = $file->move(public_path() . '/assets/img/stores/' . $store->merchant->id, $file_name);
            } else {
                $file_name = $store->merchant->featured_image;
            }

            $update_merchant = $store->merchant->update(array_merge($data, [
                    'featured_image' => $file_name,
                    'is_active' => $request->has('is_active')
            ]));

            if($update_store && $update_merchant) {
                return [
                    'status' => TRUE,
                    'merchant' => $store->merchant,
                    'merchant_store' => $store
                ];
            }

            return [
                'status' => FALSE,
                'merchant' => $store->merchant,
                'merchant_store' => $store
            ];
        });
    }
}
