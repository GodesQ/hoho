<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantHotel;
use App\Models\Merchant;
use App\Models\Organization;

use App\Services\MerchantHotelService;

use DataTables;
use DB;

class MerchantHotelController extends Controller
{

    protected $merchantHotelService;
    public function __construct(MerchantHotelService $merchantHotelService) {
        $this->merchantHotelService = $merchantHotelService;
    }

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
        $result = $this->merchantHotelService->CreateMerchantHotel($request);
        
        if ($result['status'] && $result['merchant'] && $result['merchant_hotel']) {
            $previousUrl = \URL::previous();
            $previousPath = parse_url($previousUrl, PHP_URL_PATH);

            if ($previousPath === '/merchant_form/hotel') {
                $admin = Auth::guard('admin')->user();

                if($admin->is_merchant) {
                    $admin->update([
                        'merchant_data_id' =>  $result['merchant_hotel']->id
                    ]);
                }

                return redirect()->route('admin.dashboard')->withSuccess('Merchant Hotel Created Successfully');
            }

            return redirect()->route('admin.merchants.hotels.edit', $result['merchant_hotel']->id)->withSuccess('Merchant Hotel Created Successfully');
        }
    }

    public function edit(Request $request) {
        $organizations = Organization::get();
        $hotel = MerchantHotel::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.hotels.edit-hotel', compact('hotel', 'organizations'));
    }

    public function update(Request $request) {
        $result = $this->merchantHotelService->UpdateMerchantHotel($request);

        if($result['status']) {
            return back()->with('success', 'Merchant Restaurant Updated Successfully');
        }

        return back()->with('fail', 'Merchant Restaurant Failed to Update');

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
