<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\MerchantStore;
use App\Models\Merchant;
use App\Models\Organization;
use App\Models\Interest;

use DataTables;

class MerchantStoreController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = MerchantStore::latest()->with('merchant');
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
                                <a href="/admin/merchants/stores/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                            </div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin-page.merchants.stores.list-store');
    }

    public function create(Request $request) {
        $organizations = Organization::get();
        $interests = Interest::get();
        return view('admin-page.merchants.stores.create-store', compact('organizations', 'interests'));
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
            $save_file = $file->move(public_path() . '/assets/img/stores/' . $merchant->id, $file_name);

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
                $save_file = $image_file->move(public_path() . '/assets/img/stores/' . $merchant->id, $image_file_name);

                array_push($images, $image_file_name);
                $count++;
            }

            $merchant->update([
                'images' => count($images) > 0 ? json_encode($images) : null,
            ]);
        }

        if($merchant) {
            // Second, Create Store Data
            $merchant_store = MerchantStore::create(array_merge($data, [
                'merchant_id' => $merchant->id
            ]));

            if($merchant_store) return redirect()->route('admin.merchants.stores.edit', $merchant_store->id)->withSuccess('Merchant created successfully');
        }

        return redirect()->route('admin.merchants.stores.list')->with('fail', 'Merchant failed to add');
    }

    public function edit(Request $request) {
        $organizations = Organization::get();
        $store = MerchantStore::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.stores.edit-store', compact('store', 'organizations'));
    }

    public function update(Request $request) {
        $data = $request->except('_token', 'images', 'featured_image');
        $store = MerchantStore::where('id', $request->id)->with('merchant')->firstOrFail();

        $update_store = $store->update($data);

        $images = $store->images ? json_decode($store->images) : [];
        $count = $store->images ? count(json_decode($store->images)) : 1;

        if($request->has('images')) {
            foreach ($request->images as $key => $image) {
                $count++;
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $count . '.' . $image_file->getClientOriginalExtension();
                $save_file = $image_file->move(public_path() . '/assets/img/stores/' . $store->id, $image_file_name);

                array_push($images, $image_file_name);
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

        $update_merchant = $store->merchant->update(array_merge($data, ['featured_image' => $file_name]));

        if($update_store && $update_merchant) {
            return back()->with('success', 'Store updated successfully');
        }
    }

    public function destroy(Request $request) {

    }

    public function removeImage(Request $request) {
        $store = MerchantStore::where('id', $request->id)->first();
        $images = json_decode($store->images);
        $image_path = $request->image_path;

        if(is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/stores/') . $store->merchant->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $store->update([
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
