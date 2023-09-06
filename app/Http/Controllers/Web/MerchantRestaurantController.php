<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\MerchantRestaurant;
use App\Models\Merchant;
use App\Models\Organization;

use DataTables;

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
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
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
        $data = $request->except('_token', 'featured_image');

        // First, Create a merchant
        $merchant = Merchant::create($data);

        // Save if the featured image exist in request
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/restaurants/' . $merchant->id, $file_name);

            $merchant->update([
                'featured_image' => $file_name
            ]);
        } else {
            $file_name = null;
        }

        $count = 1;
        $images = [];
        if($request->images) {
            foreach ($request->images as $key => $image) {
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $count . '.' . $image_file->getClientOriginalExtension();
                $save_file = $image_file->move(public_path() . '/assets/img/restaurants/' . $merchant->id, $image_file_name);

                array_push($images, $image_file_name);
                $count++;
            }

            $merchant->update([
                'images' => count($images) > 0 ? json_encode($images) : null,
            ]);
        }

        if($merchant) {
            // Second, Create Hotel Data
            $merchant_restaurant = MerchantRestaurant::create(array_merge($data, [
                'merchant_id' => $merchant->id
            ]));

            if($merchant_restaurant) return redirect()->route('admin.merchants.restaurants.edit', $merchant_restaurant->id)->withSuccess('Restaurant created successfully');
        }

        return redirect()->route('admin.merchants.restaurants.list')->with('fail', 'Restaurant failed to add');
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
        $count = $restaurant->images ? count(json_decode($restaurant->images)) : 1;

        if($request->has('images')) {
            foreach ($request->images as $key => $image) {
                $count++;
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $count . '.' . $image_file->getClientOriginalExtension();
                $save_file = $image_file->move(public_path() . '/assets/img/restaurants/' . $restaurant->id, $image_file_name);

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
            rmdir(public_path('assets/img/restaurants/') . $restaurant->merchant->id . '/');
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
