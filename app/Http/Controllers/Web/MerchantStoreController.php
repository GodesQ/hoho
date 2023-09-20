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

    public function update_stores(Request $request) {
        
    $arrayData = [
	[
		"Name" => "Eng Bee Tin - Robinson's Place Manila",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "Ground Floor",
			"street" => "Robinson's Place Ermita, Padre Faura Wing, Pedro Gil corner Adriatico Street",
			"district" => "Ermita",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.5770285785844, 120.984233418495"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - Flagship Store",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "7:30 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "7:30 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "7:30 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "7:30 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "7:30 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "7:30 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "7:30 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "628 ",
			"street" => "Ongin Street",
			"district" => "Binondo",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.600898095275, 120.977013432746"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - Tutuban Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "9:00 am",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "9:00 am",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "9:00 am",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "9:00 am",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "9:00 am",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "9:00 am",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "9:00 am",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "MS-G11 Level 1",
			"street" => "Main Station Building, Tutuban Center, CM Recto",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.6094352939394, 120.972827406746"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - 529 Ronquillo Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "7:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "7:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "7:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "7:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "7:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "7:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "7:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "529 ",
			"street" => "Ronquillo Street corner F. Torres ",
			"district" => "Sta Cruz",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.6010383062594, 120.980903703699"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - Cash and Carry Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "8:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "8:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "8:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "8:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "8:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "8:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "8:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "Ground Floor",
			"street" => "Cash and Carry Mall, Infront of the Supermarket, Filmore Street",
			"city" => "Makati",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.558780646968, 121.005754256621"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - Glorietta Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "2nd Floor",
			"street" => "Glorietta 2, Palm Drive Ayala Center",
			"city" => "Makati",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.5514403346692, 121.024842111643"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - 168 Mall Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "9:00 am",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "9:00 am",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "9:00 am",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "9:00 am",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "9:00 am",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "9:00 am",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "9:00 am",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "3rd Floor",
			"street" => "168 Mall, Old Food Court ",
			"district" => "Binondo",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.6050437520322, 120.974393224801"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - Lucky Chinatown Mall",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "Ground Floor, Atrium",
			"street" => "Lucky Chinatown Mall",
			"district" => "Binondo",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.6040680842011, 120.973441457671"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - Odeon Terminal Mall Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "7:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "7:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "7:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "7:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "7:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "7:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "7:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "Ground Floor",
			"street" => "Odeon Terminal Mall, Rizal Avenue, Recto",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.6050274536052, 120.982394864968"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin  - 518 Quintin Paredes Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "8:00 AM",
					"closingHours" => "6:00 pm",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "8:00 AM",
					"closingHours" => "6:00 pm",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "8:00 AM",
					"closingHours" => "6:00 pm",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "8:00 AM",
					"closingHours" => "6:00 pm",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "8:00 AM",
					"closingHours" => "6:00 pm",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "8:00 AM",
					"closingHours" => "6:00 pm",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "8:00 AM",
					"closingHours" => "6:00 pm",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "518 ",
			"street" => "Quintin Paredes ",
			"district" => "Binondo",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.6004258233953, 120.975707280412"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - Quiapo Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "8:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "8:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "8:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "8:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "8:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "8:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "8:00 AM",
					"closingHours" => "7:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "",
			"street" => "Lacson Underpass, Victory Mall",
			"district" => "Quiapo",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.5987875026468, 120.986762278227"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Eng Bee Tin - Waltermart Makati Branch",
		"Types" => "Store",
		"NatureOfBusiness" => "Store, Grocery, Pasalubong, Bakery",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "28-2888888"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "10:00 AM",
					"closingHours" => "10:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "2nd Floor",
			"street" => "Waltermart Center Makati, Arnaiz Avenue corner Chino Roces Avenue",
			"city" => "Makati",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "{\"latitude\":\"14.5522493586106\",\"longitude\":\"121.013282173013\"}"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Browhaus: The Brow Salon - Lucky Chinatown",
		"Types" => "Store",
		"NatureOfBusiness" => "",
		"Description" => "Browhaus is a Singaporean eyebrow and eyelash grooming beauty chain with outlets worldwide.",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "917-8711748"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "4th Lucy Chinatown, ",
			"street" => "Reina Regente St. cor Dela Reina St. ",
			"district" => "Binondo ",
			"city" => "Manila",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.603859533521, 120.973748146033"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Strip Ministry of Waxing - Greenbelt",
		"Types" => "Store",
		"NatureOfBusiness" => "",
		"Description" => "",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "917-8978747"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "4th Floor, ",
			"street" => "Greenbelt Dr, ",
			"district" => "",
			"city" => "Makati, ",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"zipCode" => "1228 ",
			"coordinates" => "14.5537687687704, 121.021887659079"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Rustan's - Ayala Center",
		"Types" => "Store",
		"NatureOfBusiness" => "",
		"Description" => "Rustan's is the Philippines' leading upscale and luxury retail destination, known for its unparalleled range of prestigious brands and quality merchandise.",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "9171111952"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "Rustan's Makati, Courtyard Drive",
			"district" => "Ayala Avenue",
			"city" => "Makati City",
			"country" => "Philippines",
			"coordinates" => "121.026645561 , 14.5525112545"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Browhaus: The Brow Salon - Powerplant",
		"Types" => "Store",
		"NatureOfBusiness" => "Health and Wellness",
		"Description" => "The Browhaus Blueprint System is a precise system designed to create the perfect brow.",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "917-8827697"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"alias" => "",
			"addressType" => "0",
			"addressLine" => "Concourse Level Power Plant Mall",
			"street" => "Rockwell Center",
			"district" => "Brgy. Poblacion",
			"city" => "Makati",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"coordinates" => "14.5652434222321, 121.036165057679"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Strip Ministry of Waxing",
		"Types" => "Store",
		"NatureOfBusiness" => "Health and Wellness",
		"Description" => "Ministry Of Waxing is the first international concept waxing boutique since 2002.",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "917-8978747"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "11:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"alias" => "",
			"addressType" => "0",
			"addressLine" => "4th Floor, Greenbelt Dr",
			"city" => "Makati",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"zipCode" => "1228",
			"coordinates" => "14.5537687687704, 121.021887659079"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Browhaus: The Brow Salon - Greenbelt",
		"Types" => "Store",
		"NatureOfBusiness" => "Health and Wellness",
		"Description" => "Browhaus is a Singaporean eyebrow and eyelash grooming beauty chain with outlets worldwide.",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "908-8922769"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "11:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"alias" => "",
			"addressType" => "0",
			"addressLine" => "4th Floor Greenbelt",
			"street" => "Ayala Center",
			"city" => "Makati",
			"region" => "National Capital Region",
			"country" => "Philippines",
			"zipCode" => "1228",
			"coordinates" => "14.5537784595591, 121.021322730689"
		],
		"ContactAddress" => ""
	],
	[
		"Name" => "Sincerity Cafe and Restaurant - Lucky Chinatown Mall",
		"Types" => "Store",
		"NatureOfBusiness" => "Food and Dining",
		"Description" => "Sincerity Cafe and Restaurant is a cherished family business nestled in the heart of Binondo, Manila. With a legacy spanning generations, Sincerity Cafe and Restaurant preserves culinary traditions while delighting taste buds with their signature flavors.",
		"ContactNumber" => "",
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "10:00 AM",
					"closingHours" => "9:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "G/F",
			"street" => "Reina Regente St., Lucky Chinatown Mall",
			"district" => "Binondo",
			"city" => "Manila ",
			"country" => "Philippines",
			"coordinates" => "14.5855929229575, 121.057080140713"
		],
		"ContactAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "G/F",
			"street" => "Reina Regente St., Lucky Chinatown Mall",
			"district" => "Binondo",
			"city" => "Manila ",
			"country" => "Philippines",
			"coordinates" => "14.5855929229575, 121.057080140713"
		]
	],
	[
		"Name" => "Wai Ying Fast Food - Lucky Chinatown Mall",
		"Types" => "Store",
		"NatureOfBusiness" => "Food and Dining",
		"Description" => "Established in 1998, Wai Ying Fastfood is a renowned dining establishment with a focus on authentic Cantonese cuisine. This restaurant has gained a reputation for serving delectable dishes from savory dim sum to flavorful noodle dishes, Wai Ying Fastfood offers a taste of Cantonese culinary traditions in a bustling and vibrant setting.",
		"ContactNumber" => [
			"countryCode" => "63",
			"extensionNumber" => "",
			"number" => "917-3118777"
		],
		"OperatingHours" => [
			"isOpenOnHolidays" => true,
			"isOpenOnSpecialHolidays" => true,
			"hours" => [
				[
					"day" => "Monday",
					"openingHours" => "10:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Tuesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Wednesday",
					"openingHours" => "10:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Thursday",
					"openingHours" => "10:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Friday",
					"openingHours" => "10:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Saturday",
					"openingHours" => "10:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				],
				[
					"day" => "Sunday",
					"openingHours" => "10:00 AM",
					"closingHours" => "8:00 PM",
					"isOpen" => true
				]
			]
		],
		"PermanentAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "WFK-8",
			"street" => "Lucky Chinatown Mall",
			"district" => "Binondo",
			"city" => "Manila ",
			"region" => "",
			"country" => "Philippines",
			"coordinates" => "14.60271953, 120.9761479"
		],
		"ContactAddress" => [
			"culture" => "PH",
			"addressType" => "0",
			"addressLine" => "WFK-8",
			"street" => "Lucky Chinatown Mall",
			"district" => "Binondo",
			"city" => "Manila ",
			"country" => "Philippines",
			"coordinates" => "14.602719530557, 120.976147945936"
		]
	]
        ];

        foreach ($arrayData as $key => $merchantData) {
            $merchant = Merchant::where('name',  $merchantData['Name'])->first();

            if($merchant) {
                $street = isset($merchantData['PermanentAddress']['street']) ? $merchantData['PermanentAddress']['street'] . ', ' : null;
                $district = isset($merchantData['PermanentAddress']['district']) ? $merchantData['PermanentAddress']['district'] . ', ' : null;
                $city = isset($merchantData['PermanentAddress']['city']) ? $merchantData['PermanentAddress']['city'] . ', ' : null;
                $country = isset($merchantData['PermanentAddress']['country']) ? $merchantData['PermanentAddress']['country'] . ', ' : null;

                $complete_address = $street . $district . $city . $country;

                $coordinates = isset($merchantData['PermanentAddress']['coordinates']) ? $merchantData['PermanentAddress']['coordinates'] : null;

                if(!empty($coordinates)) {
                    $coordinates = explode(", ", $coordinates);
                    $latitude = isset($coordinates[0]) ? $coordinates[0] : null;
                    $longitude = isset($coordinates[1]) ? $coordinates[1] : null;
                } else {
                    $latitude = null;
                    $longitude = null;
                }
                
                $merchant->description = $merchantData['Description'];
                $merchant->nature_of_business = $merchantData['NatureOfBusiness'];
                $merchant->address = $complete_address;
                $merchant->latitude = $latitude;
                $merchant->longitude = $longitude;

                $merchant->save();

                $store = MerchantStore::where('merchant_id', $merchant->id)->first();
                if($store) {

                    $operatingHours = "Monday : Closed\nTuesday : 9:00 AM - 8:00 PM\nWednesday : 9:00 AM - 8:00 PM\nThursday : 9:00 AM - 8:00 PM\nFriday : 9:00 AM - 8:00 PM\nSaturday : 9:00 AM - 8:00 PM\nSunday : 9:00 AM - 8:00 PM";
            
                    // Additional conditions for Operating Hours
                    if (isset($attractionData['OperatingHours']['isOpenOnHolidays'])) {
                        $operatingHours .= "\n\nOpen On Holidays: Yes";
                    } else {
                        $operatingHours .= "\n\nOpen On Holidays: No";
                    }

                    if (isset($attractionData['OperatingHours']['isOpen24Hours'])) {
                        $operatingHours .= "\nOpen 24 Hours: Yes";
                    } else {
                        $operatingHours .= "\nOpen 24 Hours: No";
                    }

                    if (isset($attractionData['OperatingHours']['isOpenOnSpecialHolidays'])) {
                        $operatingHours .= "\nOpen On Special Holidays: Yes";
                    } else {
                        $operatingHours .= "\nOpen On Special Holidays: No";
                    }

                    if (isset($attractionData['OperatingHours']['isClosedOnWeekends'])) {
                        $operatingHours .= "\nClosed On Weekends: Yes";
                    } else {
                        $operatingHours .= "\nClosed On Weekends: No";
                    }

                    $mobileNumber = $merchantData['ContactNumber'];

                        if ($mobileNumber !== 'NULL' || !empty($mobileNumber) || $mobileNumber != null) {
                            $countryCode = isset($mobileNumber['countryCode']) ? $mobileNumber['countryCode'] : null;
                            $number = isset($mobileNumber['number']) ? $mobileNumber['number'] : null;

                            $contactNo = $countryCode . $number; 
                        } else {
                            $contactNo = null;
                        }

                    $store->business_hours = $operatingHours;
                    $store->contact_number = $contactNo;
                    $store->save();

                }

            }
        }
    }
}
