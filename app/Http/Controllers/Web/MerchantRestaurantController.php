<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\MerchantRestaurant\StoreRequest;
use App\Http\Requests\MerchantRestaurant\UpdateRequest;
use App\Services\MerchantRestaurantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MerchantRestaurant;
use App\Models\Organization;
use App\Models\Interest;

use Illuminate\Support\Facades\URL;
use Yajra\DataTables\DataTables;

class MerchantRestaurantController extends Controller
{
    protected $merchantRestaurantService;

    public function __construct(MerchantRestaurantService $merchantRestaurantService) {
        $this->merchantRestaurantService = $merchantRestaurantService;
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $search = $request->search;
            $organization_id = $request->organization_id;

            $data = MerchantRestaurant::when($search, function($query) use ($search) {
                $query->whereHas('merchant', function ($merchantQuery) use ($search) {
                    $merchantQuery->where('name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($organization_id, function ($query) use ($organization_id) {
                $query->whereHas('merchant', function($q) use ($organization_id) {
                    $q->where('organization_id', $organization_id);
                });
            })->with('merchant');

            return $this->merchantRestaurantService->_generateDataTable($data);
        }

        $organizations = Organization::get();

        return view('admin-page.merchants.restaurants.list-restaurant', compact('organizations'));
    }

    public function create(Request $request)
    {
        $organizations = Organization::get();
        $interests = Interest::get();
        return view('admin-page.merchants.restaurants.create-restaurant', compact('organizations', 'interests'));
    }

    public function store(StoreRequest $request)
    {
        $result = $this->merchantRestaurantService->createMerchantRestaurant($request);

        if ($result['status']) {
            $previousUrl = URL::previous();
            $previousPath = parse_url($previousUrl, PHP_URL_PATH);

            if ($previousPath === '/merchant_form/restaurant') {
                $admin = Auth::guard('admin')->user();

                if(in_array($admin->role, merchant_roles())) {
                    $admin->update([
                        'merchant_id' => $result['merchant']->id,
                    ]);
                }

                return redirect()->route('admin.dashboard')->withSuccess('Merchant Restaurant Created Successfully');
            }

            return redirect()->route('admin.merchants.restaurants.edit', $result['merchant_restaurant']->id)->withSuccess('Merchant Restaurant created successfully');
        }
    }


    public function edit(Request $request)
    {
        $organizations = Organization::get();
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->first();
        $interests = Interest::get();

        return view('admin-page.merchants.restaurants.edit-restaurant', compact('restaurant', 'organizations', 'interests'));
    }

    public function update(UpdateRequest $request)
    {
        $result = $this->merchantRestaurantService->updateMerchantRestaurant($request);

        if($result['status']) {
            return back()->with('success', 'Merchant Restaurant Updated Successfully');
        }

        return back()->with('fail', 'Merchant Restaurant Failed to Update');

    }

    public function destroy(Request $request)
    {
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->firstOrFail();

        $old_upload_image = public_path('assets/img/restaurants/') . $restaurant->merchant->id . '/' . $restaurant->merchant->featured_image;
        if ($old_upload_image) {
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

        if (is_dir($directory)) {
            @rmdir($directory);
        }

        $delete_merchant = $restaurant->merchant->delete();

        if ($delete_merchant) {
            $delete_restaurant = $restaurant->delete();
            if ($delete_restaurant) {
                return response([
                    'status' => true,
                    'message' => 'Restaurant Deleted Successfully'
                ]);
            }
        }
    }

    public function removeImage(Request $request)
    {
        $restaurant = MerchantRestaurant::where('id', $request->id)->first();
        $images = json_decode($restaurant->images);
        $image_path = $request->image_path;

        if (is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/restaurants/') . $restaurant->merchant->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $restaurant->update([
            'images' => count($images) > 0 ? json_encode(array_values($images)) : null,
        ]);

        if ($update) {
            return response([
                'status' => TRUE,
                'message' => 'Image successfully remove'
            ]);
        }
    }

    public function update_restaurants(Request $request)
    {
        $arrayData = [];

        // foreach ($arrayData as $key => $merchantData) {
        //     $merchant = Merchant::where('name', $merchantData['Name'])->first();

        //     if ($merchant) {
        //         $street = isset($merchantData['PermanentAddress']['street']) ? $merchantData['PermanentAddress']['street'] . ', ' : null;
        //         $district = isset($merchantData['PermanentAddress']['district']) ? $merchantData['PermanentAddress']['district'] . ', ' : null;
        //         $city = isset($merchantData['PermanentAddress']['city']) ? $merchantData['PermanentAddress']['city'] . ', ' : null;
        //         $country = isset($merchantData['PermanentAddress']['country']) ? $merchantData['PermanentAddress']['country'] : null;

        //         $complete_address = $street . $district . $city . $country;

        //         $coordinates = isset($merchantData['PermanentAddress']['coordinates']) ? $merchantData['PermanentAddress']['coordinates'] : null;

        //         if (!empty($coordinates)) {
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

        //         $restaurant = MerchantRestaurant::where('merchant_id', $merchant->id)->first();
        //         if ($restaurant) {

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

        //             if ($mobileNumber !== 'NULL' || !empty($mobileNumber) || $mobileNumber != null) {
        //                 $countryCode = isset($mobileNumber['countryCode']) ? $mobileNumber['countryCode'] : null;
        //                 $number = isset($mobileNumber['number']) ? $mobileNumber['number'] : null;

        //                 $contactNo = $countryCode . $number;
        //             } else {
        //                 $contactNo = null;
        //             }

        //             $restaurant->business_hours = $operatingHours;
        //             $restaurant->contact_number = $contactNo;
        //             $restaurant->save();

        //         }

        //     }
        // }
    }
}
