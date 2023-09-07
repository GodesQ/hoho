<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantRestaurant;
use App\Models\Merchant;
use App\Models\Organization;

use DataTables;
use DB;

class MerchantRestaurantController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = MerchantRestaurant::latest()->with('merchant');
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name', function ($row) {
                        return optional($row->merchant)->name;
                    })
                    ->addColumn('nature_of_business', function($row) {
                        return optional($row->merchant)->nature_of_business;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/merchants/restaurants/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id=" '. $row->id .' " class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.merchants.restaurants.list-restaurant');
    }

    public function create(Request $request) {
        $organizations = Organization::get();
        return view('admin-page.merchants.restaurants.create-restaurant', compact('organizations'));
    }

    public function store(Request $request) {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'featured_image', 'images');
            $merchant = Merchant::create($data);
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
            ]);

            $merchant_restaurant = MerchantRestaurant::create($merchant_restaurant_data);

            if ($merchant_restaurant) {
                return redirect()->route('admin.merchants.restaurants.edit', $merchant_restaurant->id)->withSuccess('Restaurant created successfully');
            }

            return redirect()->route('admin.merchants.restaurants.list')->with('fail', 'Restaurant failed to add');
        });
    }


    public function edit(Request $request) {
        $organizations = Organization::get();
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.restaurants.edit-restaurant', compact('restaurant', 'organizations'));
    }

    public function update(Request $request) {
        $data = $request->except('_token', 'images', 'featured_image');
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->firstOrFail();

        $update_restaurant = $restaurant->update($data);

        $images = $restaurant->images ? json_decode($restaurant->images) : [];

        if($request->has('images')) {
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
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/restaurants/') . $restaurant->merchant->id . '/' . $restaurant->merchant->featured_image;
            if($old_upload_image) {
                $remove_image = @unlink($old_upload_image);
            }
            $save_file = $file->move(public_path() . '/assets/img/restaurants/' . $restaurant->merchant->id, $file_name);
        } else {
            $file_name = $restaurant->merchant->featured_image;
        }

        $update_merchant = $restaurant->merchant->update(array_merge($data, ['featured_image' => $file_name]));

        if($update_restaurant && $update_merchant) {
            return back()->with('success', 'Restaurant updated successfully');
        }
    }

    public function destroy(Request $request) {
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->firstOrFail();

        $old_upload_image = public_path('assets/img/restaurants/') . $restaurant->merchant->id . '/' . $restaurant->merchant->featured_image;
        if($old_upload_image) {
            $remove_image = @unlink($old_upload_image);
        }

        // Remove all files from the directory
        $directory = public_path('assets/img/restaurants/') . $restaurant->merchant->id;
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

        $delete_merchant = $restaurant->merchant->delete();

        if($delete_merchant) {
            $delete_restaurant = $restaurant->delete();
            if($delete_restaurant) {
                return response([
                    'status' => true,
                    'message' => 'Restaurant Deleted Successfully'
                ]);
            }
        }
    }

    public function removeImage(Request $request) {
        $restaurant = MerchantRestaurant::where('id', $request->id)->first();
        $images = json_decode($restaurant->images);
        $image_path = $request->image_path;

        if(is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/restaurants/') . $restaurant->merchant->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $restaurant->update([
            'images' => count($images) > 0 ? json_encode(array_values($images)) : null,
        ]);

        if($update) {
            return response([
                'status' => TRUE,
                'message' => 'Image successfully remove'
            ]);
        }
    }
}
