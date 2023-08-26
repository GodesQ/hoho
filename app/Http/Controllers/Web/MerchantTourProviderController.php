<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\MerchantTourProvider;
use App\Models\Merchant;

use DataTables;

class MerchantTourProviderController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = MerchantTourProvider::latest()->with('merchant');
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
                                    <a href="/admin/merchants/tour_providers/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }
        return view('admin-page.merchants.tour_providers.list-tour-provider');
    }

    public function create(Request $request) {
        return view('admin-page.merchants.tour_providers.create-tour-provider');
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
            $save_file = $file->move(public_path() . '/assets/img/tour_providers/' . $merchant->id, $file_name);

            $merchant->update([
                'featured_image' => $file_name
            ]);
        } else {
            $file_name = null;
        }

        if($merchant) {
            // Second, Create Hotel Data
            $merchant_tour_provider = MerchantTourProvider::create(array_merge($data, [
                'merchant_id' => $merchant->id
            ]));

            if($merchant_tour_provider) return redirect()->route('admin.merchants.tour_providers.edit', $merchant_tour_provider->id)->withSuccess('Tour Provider created successfully');
        }

        return redirect()->route('admin.merchants.tour_providers.list')->with('fail', 'Tour Provider failed to add');
    }

    public function edit(Request $request) {
        $restaurant = MerchantTourProvider::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.tour_providers.edit-tour-provider', compact('restaurant'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $tour_provider = MerchantTourProvider::where('id', $request->id)->with('merchant')->firstOrFail();

        $update_tour_provider = $tour_provider->update($data);

        // Save if the featured image exist in request
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/tour_providers/') . $tour_provider->merchant->id . '/' . $tour_provider->merchant->featured_image;
            if($old_upload_image) {
                $remove_image = @unlink($old_upload_image);
            }
            $save_file = $file->move(public_path() . '/assets/img/tour_providers/' . $tour_provider->merchant->id, $file_name);
        } else {
            $file_name = $tour_provider->merchant->featured_image;
        }

        $update_merchant = $tour_provider->merchant->update(array_merge($data, ['featured_image' => $file_name]));

        if($update_tour_provider && $update_merchant) {
            return back()->with('success', 'Tour Provider updated successfully');
        }
    }

    public function destroy(Request $request) {
        $tour_provider = MerchantTourProvider::where('id', $request->id)->with('merchant')->firstOrFail();

        $old_upload_image = public_path('assets/img/tour_providers/') . $tour_provider->merchant->id . '/' . $tour_provider->merchant->featured_image;
        if($old_upload_image) {
            $remove_image = @unlink($old_upload_image);
            rmdir(public_path('assets/img/tour_providers/') . $tour_provider->merchant->id . '/');
        }

        $delete_merchant = $tour_provider->merchant->delete();

        if($delete_merchant) {
            $delete_tour_provider = $tour_provider->delete();
            if($delete_tour_provider) {
                return response([
                    'status' => true,
                    'message' => 'Restaurant Deleted Successfully'
                ]);
            }
        }
    }
}
