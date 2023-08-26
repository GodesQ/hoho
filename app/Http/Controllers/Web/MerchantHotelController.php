<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\MerchantHotel;
use App\Models\Merchant;
use App\Models\Organization;

use DataTables;

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
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
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
        $data = $request->except('_token', 'featured_image');

        // First, Create a merchant
        $merchant = Merchant::create($data);

        // Save if the featured image exist in request
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/hotels/' . $merchant->id, $file_name);

            $merchant->update([
                'featured_image' => $file_name
            ]);
        } else {
            $file_name = null;
        }

        if($merchant) {
            // Second, Create Hotel Data
            $merchant_hotel = MerchantHotel::create(array_merge($data, [
                'merchant_id' => $merchant->id
            ]));

            if($merchant_hotel) return redirect()->route('admin.merchants.hotels.edit', $merchant_hotel->id)->withSuccess('Hotel created successfully');
        }

        return redirect()->route('admin.merchants.hotels.list')->with('fail', 'Merchant failed to add');

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

        // Save if the featured image exist in request
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/hotels/') . $hotel->merchant->id . '/' . $hotel->merchant->featured_image;
            if($old_upload_image) {
                $remove_image = @unlink($old_upload_image);
            }
            $save_file = $file->move(public_path() . '/assets/img/hotels/' . $hotel->merchant->id, $file_name);
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
            rmdir(public_path('assets/img/hotels/') . $hotel->merchant->id . '/');
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
}
