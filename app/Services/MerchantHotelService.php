<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Merchant;
use App\Models\MerchantHotel;

use DB;


class MerchantHotelService
{
    public function CreateMerchantHotel(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'featured_image', 'main_featured_image', 'images');
            $merchant = Merchant::create(array_merge($data, ['is_active' => $request->has('is_active'), 'is_featured' => $request->has('is_featured')]));
            $file_name = null;
            $path_folder = 'hotels/' . $merchant->id . '/';

            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = Str::snake(Str::lower($request->name));
                $file_name = $name . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs($path_folder, $file, $file_name);

                $merchant->update([
                    'featured_image' => $file_name,
                ]);
            }

            if ($request->hasFile('main_featured_image')) {
                $main_featured_file = $request->file('main_featured_image');
                $name = Str::snake(Str::lower($request->name)) . '_main_featured_image';
                $file_name = $name . '.' . $main_featured_file->getClientOriginalExtension();
                $main_featured_file->move(public_path() . '/assets/img/hotels/' . $merchant->id, $file_name);

                $merchant->update([
                    'main_featured_image' => $file_name,
                ]);
            }

            $images = [];

            if ($request->has('images')) {
                foreach ($request->file('images') as $count => $image) {
                    $uniqueId = Str::random(5);
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    $images[] = $image_file_name;
                }
            }

            $merchant_hotel_data = array_merge($data, [
                'merchant_id' => $merchant->id,
                'images' => count($images) > 0 ? json_encode($images) : null,
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]);

            $merchant_hotel = MerchantHotel::create($merchant_hotel_data);

            if ($merchant_hotel) {
                return [
                    'status' => TRUE,
                    'merchant' => $merchant,
                    'merchant_hotel' => $merchant_hotel
                ];
            }

            return [
                'status' => FALSE,
                'merchant' => null,
                'merchant_hotel' => null
            ];
        });
    }

    public function UpdateMerchantHotel(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'images', 'featured_image');

            $hotel = MerchantHotel::where('id', $request->id)->with('merchant')->firstOrFail();

            $update_hotel = $hotel->update(array_merge($data, [
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]));

            $images = $hotel->images ? json_decode($hotel->images) : [];

            $path_folder = 'hotels/' . $hotel->merchant->id . '/';

            if ($request->has('images')) {
                foreach ($request->images as $key => $image) {
                    $uniqueId = Str::random(5);
                    $image_file = $image;
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                    $save_file = Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    $images[] = $image_file_name;
                }

                $update_hotel = $hotel->update([
                    'images' => count($images) > 0 ? json_encode($images) : $hotel->images,
                ]);
            }

            // Save if the featured image exist in request
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = Str::snake(Str::lower($request->name));
                $file_name = $name . '.' . $file->getClientOriginalExtension();

                $old_upload_image = public_path('assets/img/hotels/') . $hotel->merchant->id . '/' . $hotel->merchant->featured_image;

                if ($old_upload_image) {
                    $remove_image = @unlink($old_upload_image);
                }

                $save_file = Storage::disk('public')->putFileAs($path_folder, $file, $file_name);

            } else {
                $file_name = $hotel->merchant->featured_image;
            }

            if($request->hasFile('main_featured_image')) {
                $file = $request->file('main_featured_image');
                $name = Str::snake(Str::lower($request->name));
                $main_featured_file_name = $name . '.' . $file->getClientOriginalExtension();
                $old_upload_image = public_path('assets/img/restaurants/') . $hotel->merchant->id . '/' . $hotel->merchant->main_featured_image;

                if($old_upload_image) {
                    $remove_image = @unlink($old_upload_image);
                }

                $save_file = $file->move(public_path() . '/assets/img/hotels/' . $hotel->merchant->id, $main_featured_file_name);
            } else {
                $main_featured_file_name = $hotel->merchant->main_featured_image;
            }

            $update_merchant = $hotel->merchant->update(array_merge($data, [
                'featured_image' => $file_name, 
                'main_featured_image' => $main_featured_file_name,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
            ]));

            if ($update_hotel && $update_merchant) {
                return [
                    'status' => TRUE,
                    'merchant' => $hotel->merchant,
                    'merchant_hotel' => $hotel
                ];
            }

            return [
                'status' => FALSE,
                'merchant' => null,
                'merchant_hotel' => null
            ];
        });
    }
}
?>