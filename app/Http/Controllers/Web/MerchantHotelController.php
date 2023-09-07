<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantHotel;
use App\Models\Merchant;
use App\Models\Organization;

use DataTables;
use DB;

class MerchantHotelController extends Controller
{
    public function list(Request $request) {

        if($request->ajax()) {
            $data = MerchantHotel::latest()->with('merchant');
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name', function ($row) {
                        return ($row->merchant)->name;
                    })
                    ->addColumn('nature_of_business', function($row) {
                        return optional($row->merchant)->nature_of_business;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/merchants/hotels/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.merchants.hotels.list-hotel');
    }

    public function create(Request $request) {
        $organizations = Organization::get();
        return view('admin-page.merchants.hotels.create-hotel', compact('organizations'));
    }

    public function store(Request $request) {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'featured_image', 'images');
            $merchant = Merchant::create($data);
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
            ]);

            $merchant_hotel = Merchanthotel::create($merchant_hotel_data);

            if ($merchant_hotel) {
                return redirect()->route('admin.merchants.hotels.edit', $merchant_hotel->id)->withSuccess('Hotel created successfully');
            }

            return redirect()->route('admin.merchants.hotels.list')->with('fail', 'Hotel failed to add');
        });
    }

    public function edit(Request $request) {
        $organizations = Organization::get();
        $hotel = MerchantHotel::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.hotels.edit-hotel', compact('hotel', 'organizations'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $hotel = MerchantHotel::where('id', $request->id)->with('merchant')->firstOrFail();
        $update_hotel = $hotel->update($data);
        $images = $hotel->images ? json_decode($hotel->images) : [];

        $path_folder = 'hotels/' . $hotel->merchant->id . '/';

        if($request->has('images')) {
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
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/hotels/') . $hotel->merchant->id . '/' . $hotel->merchant->featured_image;

            if($old_upload_image) {
                $remove_image = @unlink($old_upload_image);
            }

            $save_file = Storage::disk('public')->putFileAs($path_folder, $file, $file_name);

        } else {
            $file_name = $hotel->merchant->featured_image;
        }

        $update_merchant = $hotel->merchant->update(array_merge($data, ['featured_image' => $file_name]));

        if($update_hotel && $update_merchant) {
            return back()->with('success', 'Hotel updated successfully');
        }
    }

    public function destroy(Request $request) {
        $hotel = MerchantHotel::where('id', $request->id)->with('merchant')->firstOrFail();

        $old_upload_image = public_path('assets/img/hotels/') . $hotel->merchant->id . '/' . $hotel->merchant->featured_image;
        if($old_upload_image) {
            $remove_image = @unlink($old_upload_image);
        }

        // Remove all files from the directory
        $directory = public_path('assets/img/hotels/') . $hotel->merchant->id;
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

        $delete_merchant = $hotel->merchant->delete();

        if($delete_merchant) {
            $delete_hotel = $hotel->delete();
            if($delete_hotel) {
                return response([
                    'status' => true,
                    'message' => 'Hotel Deleted Successfully'
                ]);
            }
        }
    }

    public function removeImage(Request $request) {
        $hotel = MerchantHotel::where('id', $request->id)->first();
        $images = json_decode($hotel->images);
        $image_path = $request->image_path;
        if(is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/hotels/') . $hotel->merchant->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $hotel->update([
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
