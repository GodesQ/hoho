<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantStore;
use App\Models\Merchant;
use App\Models\Organization;
use App\Models\Interest;

use App\Services\MerchantStoreService;

use Yajra\DataTables\DataTables;
use DB;

class MerchantStoreController extends Controller
{
    protected $merchantStoreService;

    public function __construct(MerchantStoreService $merchantStoreService)
    {
        $this->merchantStoreService = $merchantStoreService;
    }

    /**
     * Retrieves a list of merchant stores and returns it as a DataTables response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
     */
    public function list(Request $request) {
        if($request->ajax()) {
            // dd($request->all());
            $data = MerchantStore::with('merchant');
            
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

    /**
     * Create a new store.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request) {
        $organizations = Organization::get();
        $interests = Interest::get();
        return view('admin-page.merchants.stores.create-store', compact('organizations', 'interests'));
    }

    /**
     * Store a new merchant store based on the given request.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        $result = $this->merchantStoreService->CreateMerchantStore($request);

        // Checks if the 'status' key in the $result array is truthy.
        if ($result['status']) {
            $previousUrl = \URL::previous();
            $previousPath = parse_url($previousUrl, PHP_URL_PATH);

            if ($previousPath === '/merchant_form/store') {
                $admin = Auth::guard('admin')->user();

                if($admin->is_merchant) {
                    $admin->update([
                        'merchant_data_id' =>  $result['merchant_store']->id
                    ]);
                }

                return redirect()->route('admin.dashboard')->withSuccess('Merchant Store Created Successfully');
            }

            return redirect()->route('admin.merchants.stores.edit', $result['merchant_store']->id)->withSuccess('Merchant Store created successfully');
        }

        return back()->with('fail', 'Merchant Store Failed to Create');
    }

    /**
     * Edit the merchant store based on the given request.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request) {
        $organizations = Organization::get();
        $store = MerchantStore::where('id', $request->id)->with('merchant')->first();
        $interests = Interest::get();

        return view('admin-page.merchants.stores.edit-store', compact('store', 'organizations', 'interests'));
    }

    public function update(Request $request) {
        $result = $this->merchantStoreService->UpdateMerchantStore($request);

        if($result['status']) {
            return back()->with('success', 'Merchant Store Updated Successfully');
        }

        return back()->with('fail', 'Merchant Store Failed to Update');
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

    public function update_stores(Request $request) {
        // foreach ($arrayData as $key => $merchantData) {
        //     $merchant = Merchant::where('name',  $merchantData['Name'])->first();

        //     if($merchant) {
        //         $street = isset($merchantData['PermanentAddress']['street']) ? $merchantData['PermanentAddress']['street'] . ', ' : null;
        //         $district = isset($merchantData['PermanentAddress']['district']) ? $merchantData['PermanentAddress']['district'] . ', ' : null;
        //         $city = isset($merchantData['PermanentAddress']['city']) ? $merchantData['PermanentAddress']['city'] . ', ' : null;
        //         $country = isset($merchantData['PermanentAddress']['country']) ? $merchantData['PermanentAddress']['country'] : null;

        //         $complete_address = $street . $district . $city . $country;

        //         $coordinates = isset($merchantData['PermanentAddress']['coordinates']) ? $merchantData['PermanentAddress']['coordinates'] : null;

        //         if(!empty($coordinates)) {
        //             $coordinates = explode(", ", $coordinates);
        //             $latitude = isset($coordinates[0]) ? $coordinates[0] : null;
        //             $longitude = isset($coordinates[1]) ? $coordinates[1] : null;
        //         } else {
        //             $latitude = null;
        //             $longitude = null;
        //         }

        //         $merchant->description = $merchantData['Description'];
        //         $merchant->nature_of_business = $merchantData['NatureOfBusiness'];
        //         $merchant->address = $complete_address;
        //         $merchant->latitude = $latitude;
        //         $merchant->longitude = $longitude;

        //         $merchant->save();

        //         $store = MerchantStore::where('merchant_id', $merchant->id)->first();
        //         if($store) {

        //             $operatingHours = "Monday : Closed\nTuesday : 9:00 AM - 8:00 PM\nWednesday : 9:00 AM - 8:00 PM\nThursday : 9:00 AM - 8:00 PM\nFriday : 9:00 AM - 8:00 PM\nSaturday : 9:00 AM - 8:00 PM\nSunday : 9:00 AM - 8:00 PM";

        //             // Additional conditions for Operating Hours
        //             if (isset($attractionData['OperatingHours']['isOpenOnHolidays'])) {
        //                 $operatingHours .= "\n\nOpen On Holidays: Yes";
        //             } else {
        //                 $operatingHours .= "\n\nOpen On Holidays: No";
        //             }

        //             if (isset($attractionData['OperatingHours']['isOpen24Hours'])) {
        //                 $operatingHours .= "\nOpen 24 Hours: Yes";
        //             } else {
        //                 $operatingHours .= "\nOpen 24 Hours: No";
        //             }

        //             if (isset($attractionData['OperatingHours']['isOpenOnSpecialHolidays'])) {
        //                 $operatingHours .= "\nOpen On Special Holidays: Yes";
        //             } else {
        //                 $operatingHours .= "\nOpen On Special Holidays: No";
        //             }

        //             if (isset($attractionData['OperatingHours']['isClosedOnWeekends'])) {
        //                 $operatingHours .= "\nClosed On Weekends: Yes";
        //             } else {
        //                 $operatingHours .= "\nClosed On Weekends: No";
        //             }

        //             $mobileNumber = $merchantData['ContactNumber'];

        //                 if ($mobileNumber !== 'NULL' || !empty($mobileNumber) || $mobileNumber != null) {
        //                     $countryCode = isset($mobileNumber['countryCode']) ? $mobileNumber['countryCode'] : null;
        //                     $number = isset($mobileNumber['number']) ? $mobileNumber['number'] : null;

        //                     $contactNo = $countryCode . $number;
        //                 } else {
        //                     $contactNo = null;
        //                 }

        //             $store->business_hours = $operatingHours;
        //             $store->contact_number = $contactNo;
        //             $store->save();

        //         }

        //     }
        // }
    }
}
