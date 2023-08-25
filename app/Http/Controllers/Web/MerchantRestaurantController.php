<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\MerchantRestaurant;
use App\Models\Merchant;

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
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/merchants/restaurants/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.merchants.restaurants.list-restaurant');
    }

    public function create(Request $request) {
        return view('admin-page.merchants.restaurants.create-restaurant');
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
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.restaurants.edit-restaurant', compact('restaurant'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->firstOrFail();

        $update_restaurant = $restaurant->update($data);

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
}
