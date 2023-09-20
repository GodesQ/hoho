<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MerchantRestaurant;
use App\Models\Merchant;
use App\Models\Organization;

use DataTables;
use DB;

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
                                    <a href="/admin/merchants/restaurants/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id=" '. $row->id .' " class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.merchants.restaurants.list-restaurant');
    }

    public function create(Request $request) {
        $organizations = Organization::get();
        return view('admin-page.merchants.restaurants.create-restaurant', compact('organizations'));
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
                $file->move(public_path() . '/assets/img/restaurants/' . $merchant->id, $file_name);

                $merchant->update([
                    'featured_image' => $file_name,
                ]);
            }

            $images = [];

            if ($request->has('images')) {
                foreach ($request->file('images') as $count => $image) {
                    $uniqueId = Str::random(5);
                    $path_folder = 'restaurants/' . $merchant->id . '/';
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    $images[] = $image_file_name;
                }
            }

            $merchant_restaurant_data = array_merge($data, [
                'merchant_id' => $merchant->id,
                'images' => count($images) > 0 ? json_encode($images) : null,
            ]);

            $merchant_restaurant = MerchantRestaurant::create($merchant_restaurant_data);

            if ($merchant_restaurant) {
                return redirect()->route('admin.merchants.restaurants.edit', $merchant_restaurant->id)->withSuccess('Restaurant created successfully');
            }

            return redirect()->route('admin.merchants.restaurants.list')->with('fail', 'Restaurant failed to add');
        });
    }


    public function edit(Request $request) {
        $organizations = Organization::get();
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.restaurants.edit-restaurant', compact('restaurant', 'organizations'));
    }

    public function update(Request $request) {
        $data = $request->except('_token', 'images', 'featured_image');
        $restaurant = MerchantRestaurant::where('id', $request->id)->with('merchant')->firstOrFail();

        $update_restaurant = $restaurant->update($data);

        $images = $restaurant->images ? json_decode($restaurant->images) : [];

        if($request->has('images')) {
            foreach ($request->images as $key => $image) {
                $uniqueId = Str::random(5);
                $image_file = $image;
                $path_folder = 'restaurants/' . $restaurant->merchant->id . '/';
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                $save_file = Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                array_push($images, $image_file_name);
            }

            $update_restaurant = $restaurant->update([
                'images' => count($images) > 0 ? json_encode($images) : $restaurant->images,
            ]);
        }

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
        }

        // Remove all files from the directory
        $directory = public_path('assets/img/restaurants/') . $restaurant->merchant->id;
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

    public function removeImage(Request $request) {
        $restaurant = MerchantRestaurant::where('id', $request->id)->first();
        $images = json_decode($restaurant->images);
        $image_path = $request->image_path;

        if(is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/restaurants/') . $restaurant->merchant->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $restaurant->update([
            'images' => count($images) > 0 ? json_encode(array_values($images)) : null,
        ]);

        if($update) {
            return response([
                'status' => TRUE,
                'message' => 'Image successfully remove'
            ]);
        }
    }

    public function update_restaurants(Request $request) {
        $arrayData = 
        [
            [
                "Name" => "Bistro Remedios",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Restaurant",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "28-5239153"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "1911 ",
                    "street" => "M. Adriatico Street",
                    "district" => "Malate",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.5706897064549, 120.986216730681"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Cafe Adriatico Premiere",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "28-8915202"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "1790",
                    "street" => "M. Adriatico Street",
                    "district" => "Malate",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.57064016, 120.9860855"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "The Aviary Cafe",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "",
                "Description" => "Aviary Cafe, situated in Manila Zoo, is a charming Cafe where guests can enjoy a variety of delicious pastries and beverages amidst the zoo's aviary area. It's a delightful destination for both zoo-goers and coffee enthusiasts.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "917-5047384"
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
                    "addressLine" => "Manila Zoo",
                    "street" => " M. Adriatico St. ",
                    "district" => "Malate",
                    "city" => "Manila ",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "zipCode" => "",
                    "coordinates" => "14.5654556102716, 120.988541261376"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Batala Bar",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "917-894703"
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
                            "openingHours" => "9:00 am",
                            "closingHours" => "6:00 pm",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "6:00 pm",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "6:00 pm",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "6:00 pm",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "6:00 pm",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "Plaza San Luis",
                    "street" => "General Luna Street corner Rizal Street",
                    "district" => "Intramuros",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "zipCode" => "1002",
                    "coordinates" => "14.589652346289, 120.975134052439"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Cabalen - Glorietta",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "976-2002663"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "Space 2020",
                    "street" => "Glorietta 2, Palm Drive Ayala Center",
                    "city" => "Makati",
                    "region" => "National Capital Region",
                    "country" => "Philippines"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Sincerity Cafe and Restaurant",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Cafe, Food and Dining, Restaurant",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "28-2419991"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "519",
                    "street" => "Quintin Paredes Street",
                    "district" => "Binondo",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.5855929229575, 121.057080140713"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Itialianni's - Ayala Triangle",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining",
                "Description" => "American-Italian cuisine, coupled with its unique shared style dining concept as essayed by its slogan “Amore di Merrier”, made Italianni’s an instant hit among Filipinos. We brought the Italian family art of eating for everyone to enjoy. An Italian passion evident in every item on our menu, authentically prepared with the finest ingredients and built on a wide selection of American-Italian flavors.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "9391357644"
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
                    "addressLine" => "Unit B110-B111 Ground Flr Shops at Ayala Triangle Gardens ",
                    "street" => "Salcedo Village ",
                    "district" => "Bel-air",
                    "city" => "Makati",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "zipCode" => "1209",
                    "coordinates" => "14.55871, 121.02527"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Papa Kape",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Cafe",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "917-8986268"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "8:00 PM"
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
                    "addressLine" => "",
                    "street" => "",
                    "district" => "Intramuros ",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.5947108908937, 120.969542379427"
                ],
                "ContactAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "street" => "Fort Santiago",
                    "district" => "Intramuros",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines"
                ]
            ],
            [
                "Name" => "The Royal Cafe Gallery Manila",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Cafe;Travel Essentials",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "945-7926938"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "8:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "8:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "8:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "8:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "8:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "8:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "8:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "Stall 2D",
                    "street" => "Plaza San Luis Complex, General Luna Street",
                    "district" => "Intramuros",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.5901627696296, 120.97500203493"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Cabalen - Robinson's Place Manila",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Restaurant",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "976-1825228"
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
                    "addressLine" => "Level 1",
                    "street" => "Robinsons Place Manila",
                    "district" => "Ermita",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "zipCode" => "1013",
                    "coordinates" => "14.57747131, 120.9840436"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Wai Ying Fast Food",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Restaurant",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "28-2420310"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "810 ",
                    "street" => "Benavides Street",
                    "district" => "Binondo",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.602719530557, 120.976147945936"
                ],
                "ContactAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "country" => "Philippines",
                    "coordinates" => "14.602719530557, 120.976147945936"
                ]
            ],
            [
                "Name" => "The Royale Sharksfin Seafood Restaurant",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining",
                "Description" => "The Royale Sharksfin Seafood Restaurant is a renowned establishment known for its premium seafood and elegant ambiance. With a focus on exquisite dishes like their signature sharksfin soup, the restaurant offers a diverse menu featuring fresh fish, lobster, prawns, and crab prepared in various styles. With impeccable service and a commitment to sourcing the finest ingredients, it is a top choice for seafood enthusiasts seeking a memorable dining experience.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "917-8606777"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "10:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "10:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "10:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "10:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "10:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "10:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "10:30 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "2/F Pacific Centre ",
                    "street" => "460 Quintin Paredes St. ",
                    "district" => "Binondo, ",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.599427230241, 120.975644784655"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "The Great Buddha Cafe",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Restaurant;Attractions",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "28-2419999"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "2nd Floor",
                    "street" => "Ongpin Street Street ",
                    "district" => "Binondo",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.6010407833717, 120.974600673559"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "President Grand Palace Restaurant",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Restaurant",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "28-2445886"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "746 ",
                    "street" => "Ongpin Street",
                    "district" => "Binondo",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.6022590370686, 120.976665949742"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Grand Cafe 1919",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "",
                "Description" => "Grand Cafe 1919 offers a delectable culinary experience with its diverse and flavorful cuisine. Drawing inspiration from international and local influences, the menu showcases a fusion of Mediterranean, Middle Eastern, and Western dishes. With a focus on fresh and high-quality ingredients, the restaurant serves up a range of options to satisfy various tastes and preferences. From succulent steaks to vibrant salads and irresistible pastas, Grand Cafe 1919 provides a memorable dining experience that caters to a wide array of palates.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "956-4976223"
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
                    "addressLine" => "1919 Grand Cafe, ",
                    "street" => "Juan Luna Street, ",
                    "district" => "Binondo, ",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.5967403846019, 120.975624995391"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Kapetolyo by SGD Coffee",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "",
                "Description" => "Kapetolyo by SGD Coffee is a renowned purveyor of the finest Philippine coffees sourced directly from smallholder farmers. With a commitment to supporting local communities, their handpicked and expertly roasted beans deliver an authentic and exceptional coffee experience. Each cup showcases the rich heritage and distinct profiles of Philippine coffees, while empowering the hardworking farmers behind them. By enjoying Kapetolyo, you contribute to the sustainability and success of these farmers, making every sip a meaningful and delicious journey.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "945-4353908"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "10:30 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "10:30 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "10:30 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "10:30 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "9:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "9:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "9:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "Kapetolyo by SGD Coffee,",
                    "street" => " Cecilla Munoz st, Kartilya ng Katipunan Park",
                    "district" => "Ermita",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.5923265747754, 120.981199538623"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Barbara's Heritage Restaurant",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "",
                "Description" => "Barbara's Heritage Restaurant is a renowned dining establishment that offers a captivating cultural and culinary experience in Intramuros, Manila. With its rich heritage and elegant ambiance, Barbara's provides a unique setting for patrons to enjoy traditional Filipino cuisine and immerse themselves in the country's vibrant cultural performances.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "956-0934659"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "9:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "street" => "Plaza San Luis Complex, General Luna Street",
                    "district" => "Intramuros",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.5904079869935, 120.975350615341"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Mr. Ube Rice and Noodle House",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Restaurant",
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
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "8:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "707 ",
                    "street" => "Imperial Sky Garden, Ongpin Street",
                    "district" => "Binondo",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.6019749970972, 120.975943372891"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Chuan Kee Fast Food",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Restaurant",
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
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "7:00 AM",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "GF 650 ",
                    "street" => "Ongpin Street",
                    "district" => "Binondo",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.6013822514721, 120.975511026331"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Cafe Mezzanine",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Cafe",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "28-2888888 "
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "2nd Floor, 650 ",
                    "street" => "Ongin Street",
                    "district" => "Binondo",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.6011378754532, 120.975429195646"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Golden Fortune Seafood Restaurant - Luneta",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "",
                "Description" => "Golden Fortune Seafood Restaurant is widely celebrated for its amazing seafood offerings. The restaurant creates a friendly and inviting atmosphere, ensuring a memorable dining experience. With their expertly prepared seafood delicacies and attentive service, it's the perfect choice for anyone looking to indulge in delicious seafood cuisine.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "0917-8563666"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "11:30 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "11:30 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "11:30 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "11:30 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "11:30 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "11:30 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "11:30 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "",
                    "street" => "678 T.M. Kalaw St. ",
                    "district" => "Ermita, ",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.58427208, 120.987916287302"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Chung Dam Restaurant",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining, Restaurant",
                "Description" => "",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "9275014414"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "2nd Floor",
                    "street" => "590 Remedios Street",
                    "district" => "Malate",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.5702440619396, 120.986720637097"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Golden Fortune Seafood Restaurant - Binondo",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "",
                "Description" => "Golden Fortune Seafood Restaurant is widely celebrated for its amazing seafood offerings. The restaurant creates a friendly and inviting atmosphere, ensuring a memorable dining experience. With their expertly prepared seafood delicacies and attentive service, it's the perfect choice for anyone looking to indulge in delicious seafood cuisine.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "917-8576777"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "11:00 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "G/F Peace Hotel ",
                    "street" => "1283 Soler St. ",
                    "district" => "Binondo, ",
                    "city" => "Manila",
                    "region" => "National Capital Region",
                    "country" => "Philippines",
                    "coordinates" => "14.6039377767818, 120.9776490819"
                ],
                "ContactAddress" => ""
            ],
            [
                "Name" => "Cafe Via Mare",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "food and dining",
                "Description" => "Café Via Mare was introduced to the market, dubbed to be a pioneer in introducing the first authentic Filipino café.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "28898 1306"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "9:00 am",
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Friday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Sunday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "9:30 PM",
                            "isOpen" => true
                        ]
                    ]
                ],
                "PermanentAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "2nd Floor, The Landmark, Makati Ave, Ayala Center, Makati, Metro Manila",
                    "street" => "Makati Ave",
                    "district" => "Ayala Center",
                    "city" => "Makati",
                    "region" => "NCR",
                    "country" => "Philippines",
                    "zipCode" => "1226",
                    "coordinates" => "14.553031100779766, , 121.0234967"
                ],
                "ContactAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "2nd Floor, The Landmark, Makati Ave, Ayala Center, Makati, Metro Manila",
                    "street" => "Makati Ave",
                    "district" => "Ayala Center",
                    "city" => "Makati",
                    "region" => "NCR",
                    "country" => "Philippines",
                    "zipCode" => "1226",
                    "coordinates" => "14.553031100779766, , 121.0234967"
                ]
            ],
            [
                "Name" => "Via Mare - Greenbelt 1",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Food and Dining",
                "Description" => "Café Via Mare was introduced to the market, dubbed to be a pioneer in introducing the first authentic Filipino café.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "2888151918"
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
                            "closingHours" => "10:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Saturday",
                            "openingHours" => "11:00 AM",
                            "closingHours" => "10:00 PM",
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
                    "addressType" => "Commercial",
                    "addressLine" => "Groud Flr, Greenbelt 1",
                    "street" => "",
                    "district" => "Paseo De Roxas",
                    "city" => "Makati",
                    "region" => "NCR",
                    "country" => "Philippines",
                    "zipCode" => "1226",
                    "coordinates" => "14.553853151729612, , 121.01991419999999"
                ],
                "ContactAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "Groud Flr, Greenbelt 1",
                    "street" => "",
                    "district" => "Paseo De Roxas",
                    "city" => "Makati",
                    "region" => "NCR",
                    "country" => "Philippines",
                    "zipCode" => "1226",
                    "coordinates" => "14.553853151729612, , 121.01991419999999"
                ]
            ],
            [
                "Name" => "Cafe Intramuros",
                "Types" => "Restaurant",
                "NatureOfBusiness" => "Restaurant",
                "Description" => "Cafe Intramuros is a charming and cozy eatery nestled within the historic walls of Intramuros, offering a delightful fusion of local and international flavors. With its inviting ambiance and a menu that showcases both traditional Filipino dishes and international cuisine, Cafe Intramuros provides a unique culinary experience that pays homage to the rich heritage of this iconic Manila district. Whether you're seeking a taste of local culture or a cozy spot to enjoy a cup of coffee, Cafe Intramuros promises a memorable and culturally immersive dining experience.",
                "ContactNumber" => [
                    "countryCode" => "63",
                    "extensionNumber" => "",
                    "number" => "969 425 9294"
                ],
                "OperatingHours" => [
                    "isOpenOnHolidays" => true,
                    "isOpenOnSpecialHolidays" => true,
                    "hours" => [
                        [
                            "day" => "Monday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "7:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Tuesday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "7:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Wednesday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "7:00 PM",
                            "isOpen" => true
                        ],
                        [
                            "day" => "Thursday",
                            "openingHours" => "10:00 AM",
                            "closingHours" => "7:00 PM",
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
                    "addressLine" => "Stall 1C Plaza San Luis Complex ",
                    "street" => "General A Luna",
                    "district" => "Intramuros",
                    "city" => "Manila ",
                    "country" => "Philippines",
                    "zipCode" => "1002",
                    "coordinates" => "14.589613143478504, 120.9750841097415"
                ],
                "ContactAddress" => [
                    "culture" => "PH",
                    "addressType" => "0",
                    "addressLine" => "Stall 1C Plaza San Luis Complex ",
                    "street" => "General A Luna",
                    "district" => "Intramuros",
                    "city" => "Manila ",
                    "country" => "Philippines",
                    "zipCode" => "1002",
                    "coordinates" => "14.589613143478504, 120.9750841097415"
                ]
            ]
                ];

        
                foreach ($arrayData as $key => $merchantData) {
                    $merchant = Merchant::where('name',  $merchantData['Name'])->first();
        
                    if($merchant) {
                        $street = isset($merchantData['PermanentAddress']['street']) ? $merchantData['PermanentAddress']['street'] . ', ' : null;
                        $district = isset($merchantData['PermanentAddress']['district']) ? $merchantData['PermanentAddress']['district'] . ', ' : null;
                        $city = isset($merchantData['PermanentAddress']['city']) ? $merchantData['PermanentAddress']['city'] . ', ' : null;
                        $country = isset($merchantData['PermanentAddress']['country']) ? $merchantData['PermanentAddress']['country'] : null;
        
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
        
                        $restaurant = MerchantRestaurant::where('merchant_id', $merchant->id)->first();
                        if($restaurant) {
        
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
        
                            $restaurant->business_hours = $operatingHours;
                            $restaurant->contact_number = $contactNo;
                            $restaurant->save();
        
                        }
        
                    }
                }
    }
}
