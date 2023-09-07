<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantStore;
use App\Models\Merchant;
use App\Models\Organization;
use App\Models\Interest;

use DataTables;
use DB;

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
                                <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
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
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'featured_image', 'images');
            $merchant = Merchant::create($data);
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

            $images = [];

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
            ]);

            $merchant_store = MerchantStore::create($merchant_store_data);

            if ($merchant_store) {
                return redirect()->route('admin.merchants.stores.edit', $merchant_store->id)->withSuccess('Store created successfully');
            }

            return redirect()->route('admin.merchants.stores.list')->with('fail', 'Store failed to add');
        });
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

        $update_merchant = $store->merchant->update(array_merge($data, ['featured_image' => $file_name]));

        if($update_store && $update_merchant) {
            return back()->with('success', 'Store updated successfully');
        }
    }

    public function destroy(Request $request) {
        $store = MerchantStore::where('id', $request->id)->with('merchant')->firstOrFail();

        $old_upload_image = public_path('assets/img/stores/') . $store->merchant->id . '/' . $store->merchant->featured_image;
        if($old_upload_image) {
            $remove_image = @unlink($old_upload_image);
        }

        // Remove all files from the directory
        $directory = public_path('assets/img/stores/') . $store->merchant->id;
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

        $delete_merchant = $store->merchant->delete();

        if($delete_merchant) {
            $delete_store = $store->delete();
            if($delete_store) {
                return response([
                    'status' => true,
                    'message' => 'Store Deleted Successfully'
                ]);
            }
        }
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
