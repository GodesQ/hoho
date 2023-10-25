<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Merchant;
use App\Models\MerchantRestaurant;

use DB;

class MerchantRestaurantService
{
    public function CreateMerchantRestaurant(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'featured_image', 'images');
            $merchant = Merchant::create(array_merge($data, ['is_active' => $request->has('is_active')]));
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

            $images = [];

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

            $update_merchant = $restaurant->merchant->update(array_merge($data, ['featured_image' => $file_name, 'is_active' => $request->has('is_active')]));

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
}

?>