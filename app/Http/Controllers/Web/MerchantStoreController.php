<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\MerchantStore;
use App\Models\Merchant;

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
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/admin/merchants/stores/edit/' .$row->id. '">
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

        return view('admin-page.merchants.stores.list-store');
    }

    public function create(Request $request) {
        return view('admin-page.merchants.stores.create-store');
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
        $store = MerchantStore::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.stores.edit-store', compact('store'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $store = MerchantStore::where('id', $request->id)->with('merchant')->firstOrFail();

        $update_store = $store->update($data);

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
}
