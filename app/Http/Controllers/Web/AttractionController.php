<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Attraction;
use App\Models\Organization;
use App\Models\ProductCategory;

use DataTables;

class AttractionController extends Controller
{
    public function list(Request $request) {

        if($request->ajax()) {
            $data = Attraction::latest('id')->with('organization');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('organization_logo', function ($row) {
                    if($row->organization) {
                        if($row->organization->icon) {
                            $path = '../../../assets/img/organizations/' . $row->organization->id . '/' . $row->organization->icon;
                            return '<img src="' .$path. '" width="50" height="50" />';
                        } else {
                            $path = '../../../assets/img/' . 'default-image.jpg';
                            return '<img src="' .$path. '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
                        }
                    } else {
                        $path = '../../../assets/img/' . 'default-image.jpg';
                        return '<img src="' .$path. '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                <a href="/admin/attractions/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                            </div>';
                })
                ->addColumn('status', function($row) {
                    if($row->status) {
                        return '<div class="badge bg-label-success">Active</div>';
                    } else {
                        return '<div class="badge bg-label-warning">InActive</div>';

                    }
                })
                ->rawColumns(['actions', 'status', 'organization_logo'])
                ->make(true);
        }

        return view('admin-page.attractions.list-attraction');
    }

    public function create(Request $request) {
        $organizations = Organization::get();
        return view('admin-page.attractions.create-attraction', compact('organizations'));
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'organization_ids', 'images', 'featured_image');

        $attraction = Attraction::create(array_merge($data, [
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
            'status' => $request->has('is_active'),
        ]));

        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $file_name = Str::snake(Str::lower($request->name)) . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/attractions/' . $attraction->id, $file_name);
        } else {
            $file_name = $attraction->featured_image;
        }

        $images = [];
        if($request->images) {
            foreach ($request->images as $key => $image) {
                $uniqueId = Str::random(5);
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                $save_file = $image_file->move(public_path() . '/assets/img/attractions/' . $attraction->id, $image_file_name);

                array_push($images, $image_file_name);
                $count++;
            }
        }

        $update_attraction = $attraction->update([
            'featured_image' => $file_name,
            'images' => count($images) > 0 ? json_encode($images) : null,
        ]);

        if($attraction) return redirect()->route('admin.attractions.edit', $attraction->id)->withSuccess('Attraction created successfully');
    }

    public function edit(Request $request) {
        $attraction = Attraction::findOrFail($request->id);
        $organizations = Organization::get();
        $product_categories = ProductCategory::get();
        return view('admin-page.attractions.edit-attraction', compact('attraction', 'organizations', 'product_categories'));
    }

    public function update(Request $request) {
        $data = $request->except("_token", "images");
        $attraction = Attraction::findOrFail($request->id);
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $file_name = Str::snake(Str::lower($request->name)) . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/attractions/' . $attraction->id, $file_name);
        } else {
            $file_name = $attraction->featured_image;
        }

        $images = $attraction->images ? json_decode($attraction->images) : [];

        if($request->has('images')) {
            foreach ($request->images as $key => $image) {
                $uniqueId = Str::random(5);
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                $save_file = $image_file->move(public_path() . '/assets/img/attractions/' . $attraction->id, $image_file_name);

                array_push($images, $image_file_name);
            }
        }

        $update_attraction = $attraction->update(array_merge($data, [
            'featured_image' => $file_name,
            'images' => count($images) > 0 ? json_encode($images) : $attraction->images,
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
            'status' => $request->has('is_active'),
        ]));

        if($update_attraction) return back()->withSuccess('Attraction Updated Successfully');
    }

    public function destroy(Request $request) {
        $attraction = Attraction::where('id', $request->id)->firstOr(function () {
            return response()->json([
                'status' => false,
                'message' => 'Not Found'
            ]);
        });

        $remove_attraction = $attraction->delete();
        if($remove_attraction) {
            return response([
                'status' => true,
                'message' => 'Attraction deleted successfully'
            ]);
        }
    }

    public function removeImage(Request $request) {
        $attraction = Attraction::where('id', $request->id)->first();
        $images = json_decode($attraction->images);
        $image_path = $request->image_path;

        if(is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/attractions/') . $attraction->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $attraction->update([
            'images' => json_encode(array_values($images))
        ]);

        if($update) {
            return response([
                'status' => TRUE,
                'message' => 'Image successfully remove'
            ]);
        }
    }

    public function update_attractions(Request $request) {
$arrayVar = [
    [
        "Name" => "Assumption College Chapel",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Manila Clock Tower Museum",
        "Description" =>
            "Aside from the national museums, there is also a museum housed in the City Hall, the Manila Clock Tower Museum, which has the largest clock tower in the Philippines. The museum features exhbits of the history of the Battle of Manila using a mixture of audio and visual presentations as well as 3D installations. Going up the museum, other art exhbitis can be seen. And at the two top-most floors, visitors can marvel at the 360-view of the City.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5902815430293, 120.98151125345",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Binondo",
        "Description" =>
            "When in Manila, a must-visit and must-explore is Binondo, Manila\'s Chinatown. It is known to be the oldest Chinatown in the world, and rightfully so, it boasts being a major commercial hub, it has its own Filipino-Chinese culture and a solid community. It is also known for its amazing Fil-Chi food, affordable shops, historical landmarks, and structures. Binondo is an explosion of rich culture that developed over time and coming here is an experience--of the marriage between the Filipino and Chinese cultures that has withstood centuries of war, conflict, and battles.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "864 ",
            "street" => "Masangkay Street",
            "district" => "Binondo ",
            "city" => "Manila ",
            "country" => "Philippines",
            "coordinates" => "14.600290762306654, 120.97379997874947",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "7dIuKN8noNo",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Dr Jose Rizal Statue",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" =>
            "Underpasses (Ayala Ave, Paseo De Roxas, Salcedo, Makati Ave, Ayala Legazpi)",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "National Museum of Natural History",
        "Description" =>
            "Last of the three national museums in the area, the National Museum of Natural History houses 12 permanent galleries that exhibit the rich biological and geological diversity of the Philippines. It includes creatively curated displays of botanical, zoological, and geological specimens that represent our unique natural history. Situated at the center of the museum is a “Tree of Life” structure that proudly connects all the unique ecosystems in the Philippines, from our magnificent mountain ridges to the outstanding marine reefs.",
        "Address" => [
            "culture" => "PH",
            "alias" => "",
            "addressType" => "0",
            "addressLine" => "TEODORO F. VALENCIA CIR",
            "street" => "Ermita",
            "city" => "Manila",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "zipCode" => "1000",
            "coordinates" => "14.58359905, 120.9827767",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "National Museum of Anthropology",
        "Description" =>
            "The National Museum of Anthropology stages the Philippine ethnographic and terrestrial and underwater archaeological collections narrating the story of the Philippines from the past, as presented through well-preserved artifacts.",
        "Address" => [
            "culture" => "PH",
            "alias" => "",
            "addressType" => "0",
            "addressLine" => "HXPJ+3C6, P. BURGOS DRIVE RIZAL PARK",
            "street" => "TEODORO F. VALENCIA CIR",
            "district" => "Ermita",
            "city" => "Manila",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "zipCode" => "1000",
            "coordinates" => "14.58590921, 120.9811879",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "PEV5CaKcwUk",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Dream Lab",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "The Filipinas Heritage Library",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Circuit Makati",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "St. Alphonsus Mary De Ligouri Church",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "San Agustin Church and Museum",
        "Description" =>
            "Built in 1607, the San Agustin Church is the oldest church in the Philippines. It was inscribed in the World Heritage List in 1993.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Ninoy Aquino Monument",
        "Description" =>
            "In the corners of the park are the monuments of prominent historical figures, Sultan Kudarat, Gabriela Silang, and Ninoy Aquino.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Omniverse Museum",
        "Description" =>
            "Another spot to visit in the area is the Omniverse Museum, inside Glorietta 2, showcasing an extensive collection of authentic pop culture memorabilia from your favorite comic books, movies, novels, and TV series.",
        "Address" => [
            "culture" => "PH",
            "alias" => "",
            "addressType" => "0",
            "addressLine" => "OMNIVERSE MUSEUM JAPAN TOWN 4/F GLORIETTA 2",
            "street" => "AYALA CENTER MAKATI PALM DR",
            "district" => "MAKATI AVE",
            "city" => "Makati",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "zipCode" => "1224",
            "coordinates" => "14.55177, 121.02922",
        ],
        "Metadata" => ["adultPrice" => "1499", "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "12:00 AM",
                    "closingHours" => "8:00 PM",
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "12:00 AM",
                    "closingHours" => "8:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "12:00 AM",
                    "closingHours" => "8:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "12:00 AM",
                    "closingHours" => "8:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "12:00 AM",
                    "closingHours" => "8:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "12:00 AM",
                    "closingHours" => "8:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "12:00 AM",
                    "closingHours" => "8:00 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Casa Manila",
        "Description" =>
            "A museum depicting the lifestyle of 19th Century upper class families in the Philippines.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Intramuros",
        "Description" =>
            "Historic core of present day City of Manila. Features the oldest and most historically important structures in the city. The district is surrounded by walls and fortifications built from the 16th to the 19th centuries. One of its landmarks, the San Agustin Church, is a UNESCO World Heritage Site.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "5th Floor Palacio del Gobernador",
            "street" => "Gen. Luna and Aduana Streets",
            "district" => "Intramuros ",
            "city" => "Manila",
            "country" => "Philippines",
            "coordinates" => "14.590012414051994, 120.97558363471815",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "N8PU8WvLmDU",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "11:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "11:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "11:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "11:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "11:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "11:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "11:00 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Ayala Triangle Garden, Mcmicking Memorial Courtyard",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Quezon City Circle",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => "",
    ],
    [
        "Name" => "Legazpi Sunday Market",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "St. John Bosco Parish Church",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "One Ayala",
        "Description" =>
            "One Ayala is a 2.8-hectare development that promises to uphold the iconic status of the address #1 Ayala Avenue. As the highly anticipated addition to the bustling commercial district of Ayala Center, One Ayala has all the elements of a self-contained community.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5567, 121.02234",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "SM Makati",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Plaza Roma",
        "Description" =>
            "Central square of Intramuros. Surrounded by the Manila Cathedral, the Palacio del Gobernador, and the Ayuntamiento de Manila. A monument to Charles IV stands at the center.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Ayala Museum",
        "Description" =>
            "Just a few five-minute walk from the complex is Ayala Museum housing a number of significant cultural and historical pieces. The Filipinas Heritage Library is also situated in this space.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.55398, 121.02651",
        ],
        "Metadata" => ["adultPrice" => "650", "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Manila City Hall",
        "Description" =>
            "When in Manila, the Manila City Hall and the National museums are a must-see. The City Hall is shaped like a coffin to honour the brave who died during the Battle of Manila. Its walls are full of stories dating back from the Spanish making it one of the most significant landmarks in the city.",
        "Address" => [
            "culture" => "PH",
            "alias" => "",
            "addressType" => "0",
            "addressLine" => "PADRE BURGOS AVE",
            "district" => "ERMITA",
            "city" => "Manila",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "zipCode" => "1000",
            "coordinates" => "14.58971096, 120.9816863",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "PEV5CaKcwUk",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                ],
            ],
        ],
    ],
    [
        "Name" => "Bambike",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Fort Santiago Rizal Shrine",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Jose Rizal Monument",
        "Description" =>
            "Inside the Rizal Park is Jose Rizal\'s Monument. It contains the national hero\'s remains and is guarded by an honor guard 24 hours a day, 365 days a year.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5819945341047, 120.977037766945",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "Os25Zyv9kQo",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "The RCBC Plaza",
        "Description" => "The RCBC Plaza is the biggest and most modern office development in the Philippines today. It has become a landmark and symbol of growth in Makati City and it continues to play a role in shaping the Philippine economy by providing state-of-the-art and world class facilities.

The building also houses the Yuchengco Museum, which features the contemporary and historic fine art collection of Ambassador Alfonso T. Yuchengco.

You can breeze through here for a quick forty-five minute to an hour stroll.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "city" => "Makati",
            "country" => "Philippines",
            "coordinates" => "14.560761, 121.016533",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Powerplant Rockwell",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Sultan Kudarat",
        "Description" =>
            "In the corners of the park are the monuments of prominent historical figures, Sultan Kudarat, Gabriela Silang, and Ninoy Aquino.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Intramuros Golf Club",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Escolta",
        "Description" =>
            "Escolta is one of the oldest streets in Manila. During its prime, it used to be the central business district. Now, its glory can still be seen through old architectural designs patterned after Western designs, its food, and heritage.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5980350107765, 120.978717568791",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "CDn3wiwozVI",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Malacañang Heritage Museum",
        "Description" => "",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "Jose P Laurel Sr",
            "district" => "San Miguel",
            "city" => "Manila",
            "region" => "Metro Manila",
            "country" => "Philippines",
            "coordinates" => "14.592814283242037, 120.99215455270276",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "San Carlos Seminary",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Binondo-Intramuros Bridge",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Goldenberg Mansion",
        "Description" => "In the last quarter of the 19th century, Goldenberg Mansion served as the home and headquarters of US Military Governor Arthur Macarthur,  father of General Douglas Macarthur.

After the war, it was bought by cosmetics manufacturer Michael Goldenberg. The mansion was restored by Filipino architect Leandro Locsin in 1975, and used as a guest house to receive guests and visiting dignitaries of the late President Ferdinand E. Marcos Sr.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Remedios Circle",
        "Description" =>
            "A roundbout in Malate, the Remedios Circle is also known as the Plaza dela Virgen delos Remedios and serves as the intersection between Remedios Street, Jorge Bocobo, and Adriatico Street.",
        "Address" => [
            "culture" => "PH",
            "alias" => "",
            "addressType" => "0",
            "addressLine" => "REMEDIOS CIRLCE",
            "street" => "MALATE",
            "district" => "",
            "city" => "Manila",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "zipCode" => "1000",
            "coordinates" => "14.5704222293653, 120.986178351604",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Manila Zoo",
        "Description" =>
            "Formerly known as the Manila Zoological and Botanical Garden, it is a 5.5 hectare zoo located in Malate. It debuted to the public in 1959 and has since underwent major redevelopment and rehabilitation. In 2021, a spanking new Manila Zoo was opened to the public.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5650090828213, 120.988326680439",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Kennely Ann L. Binay Park",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Paco park",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Calvo Museum",
        "Description" =>
            "After crossing the memorial bridge, the Calvo Museum in Calvo Building is right around the corner. The museum is an ode to the past as it carries antiques and memorabilia from Old Manila. Old posters, advertisements, products, bottles, paintings and photographs here will give you a glimpse of the early 1900s.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5974116110713, 120.978903144926",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Bouldering Hive",
        "Description" =>
            "Other awesome activities to do in Circuit are going to an interactive museum, Dream Lab, go bouldering at Bouldering Hive, try to escape in Left Behind\'s escape rooms, and go for a stroll at the mall\'s colorful and vibrant skate park.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "First United Building Community Museum",
        "Description" =>
            "Similarly, First United Building Community Museum carries antique artifacts, objects, and memories of patriarch Sy-Lian Teng, which also shows Escolta\'s revitalization efforts. Now, the Museum also promotes homegrown crafts and products and is often called the Art Deco building of Binondo, Manila.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5988711683417, 120.979359270638",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Sta Cruz Church",
        "Description" =>
            "Going further down Escolta, Sta. Cruz Church stands. It\'s a baroque Roman Catholic parish church built in the 17th century and is considered as a heritage site. It has withstand several historical accounts, and has undergone several reconstructions.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.6001998776507, 120.980421364361",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "New Binondo Chinatown Arch",
        "Description" =>
            "A historical landmark which is quite popular in Manila is the colorful and intricate New Binondo Chinatown Arch. It is said to be the world’s largest Chinatown arch and is the perfect entryway to the well-beloved Binondo, Manila\'s Chinatown.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5969452541889, 120.977293722199",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "9:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "9:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "9:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "9:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "9:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "9:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "9:00 AM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Our lady of Guidance-Ermita Church",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Kanlungan ng SIning",
        "Description" =>
            "Another spot, which is lesser known in Rizal Park, is the Kanlungan ng Sining (Artist\'s Haven). Its a haven for Filipino budding artists who want to learn from the best artists in the country through Kanlungan. Professional artists come to the Haven to hang out with peers and to train these young people for free.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5862923298454, 120.982579768791",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "The Manila Cathedral",
        "Description" =>
            "Presently the 8th structure on site. The cathedral is the seat of the Archbishop of Manila. It was elevated to the rank of a minor basilica in 1981 by Pope John Paul II.",
        "Address" => [
            "culture" => "PH",
            "alias" => "",
            "addressType" => "0",
            "addressLine" => "Cabildo",
            "street" => "132 BEATERIO ST,",
            "district" => "Intramuros",
            "city" => "Manila",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "coordinates" => "14.59204329, 120.9732526",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "6:30 AM",
                    "closingHours" => "5:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "6:30 AM",
                    "closingHours" => "5:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "6:30 AM",
                    "closingHours" => "5:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "6:30 AM",
                    "closingHours" => "5:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "6:30 AM",
                    "closingHours" => "5:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "7:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "7:00 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "San Andres Market",
        "Description" =>
            "A perfect combination for a walk in Malate are sweet, fresh, and citrusy fruits. You can find as many fruits as you can in San Andres Market from local, imported, to exotic kinds.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Bonifacio and the Katipunan Revolution Monument",
        "Description" =>
            "The Bonifacio and the Katipunan Revolution Monument is a large wall sculpture recalling the 1896 Philippine Revolution spearheaded by Andres Bonifacio who urged his countrymen to raise against the colonial rule of Spain.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5910984109711, 120.98105343811",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Martyrdom of Rizal Diorama",
        "Description" =>
            "Beside the Chinese Garden is a historical memorial of Jose Rizal. It\'s an audio-visual experience with life-sized sculptures capturing the moment of the national hero\'s execution.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.582927566764, 120.976349924615",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Museo Pambata",
        "Description" =>
            "There\'s also more to discover in and about Manila when you visit Museo Pambata.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5792919534028, 120.977033980439",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Left Behind",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "National Museum of Fine Arts",
        "Description" =>
            "One of the three national museums in the area is the National Museum of Fine Arts. It is home to 29 galleries and hallway exhibitions comprising of 19th century Filipino masters, National Artists, leading modern painters, sculptors, and printmakers. Also on view are art loans from other government institutions, organizations, and individuals.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5870394159736, 120.981892825892",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Chinese Garden",
        "Description" =>
            "The same with the Chinese Garden, which aslo features gorgeous Chinese-inspired walkways, statues, arcs, lanterns, fountains, and a pond with lilies and pods.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5832422832894, 120.97780779578",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Ongpin Street",
        "Description" =>
            "Ongpin Street, one of the most popular streets in Binondo, is a vibrant and busy area known for its food, herb shops, ornaments, and other items. Chinese symbols and signages fill Ongpin Street. While the streets look dated, that is in and of itself the street\'s charm and appeal.",
        "Address" => [
            "culture" => "PH",
            "alias" => "",
            "addressType" => "0",
            "street" => "Ongpin street",
            "city" => "Manila",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "coordinates" => "14.6014297501635, 120.976327211121",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Rajah Sulayman Park",
        "Description" =>
            "A public space named in honor of Rajah Sulayman, the last king of Manila, the park itself features an imposing fountain and a life size monument of the Filipino hero.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5689501663666, 120.983494824615",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "f6sVjZPADp8",
        ],
        "OperatingHours" => [
            "isOpen24Hours" => true,
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "9:00 am",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Salcedo Weekend Market",
        "Description" =>
            "On the weekends, definitely a must-visit are the Salcedo Weekend and Legazpi Sunday Markets.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.560221, 121.022984",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "William Jones Bridge",
        "Description" =>
            "Down Escotlta is the William A. Jones Bridge, an arched girder bridge that spans the Pasig River. This bridge was destroyed during the World War II but was eventually restored. Recently, the City of Manila restored the Jones Bridge in its original Beaux-Arts architecture design.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "",
            "street" => "Jones Bridge",
            "district" => "Binondo ",
            "city" => "Metro Manila",
            "country" => "Philippines",
            "coordinates" => "14.5961313285625, 120.977247078593",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "CDn3wiwozVI",
        ],
        "OperatingHours" => [
            "isOpen24Hours" => true,
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "6:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "6:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "6:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "6:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "6:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "6:00 AM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "6:00 AM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Burke Building",
        "Description" =>
            "Another heritage building is the Burke Building. The Building sports a distinct Art-deco design and is notable for having the first ever elevator in Manila.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5996638784064, 120.979159344492",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Gabriela Silang Monument",
        "Description" =>
            "In the corners of the park are the monuments of prominent historical figures, Sultan Kudarat, Gabriela Silang, and Ninoy Aquino.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Legaspi Bas Relief",
        "Description" => "In the same street as the Museo is the Bas Relief of Landing of Don Miguel Lopez de Legazpi and Naming of Makati.

In 1571, Don Miguel Lopez de Legazpi in one of his expeditions saw a settlement near the riverbanks. He then asked the natives the name of the place, due to language barrier the native replied “Makati na! Kumakati na!” referring to the ebbing tide of the river.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5685, 121.03398",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Sto Niño de Paz Chapel",
        "Description" =>
            "Modern dome-shaped catholic church with open sides, set in a tree-filled park amid skyscrapers.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.552952, 121.022374",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Legazpi Sunday Market",
        "Description" =>
            "On the weekends, definitely a must-visit are the Salcedo Weekend and Legazpi Sunday Markets.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.55367, 121.018655",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Legazpi Active Park",
        "Description" =>
            "Another park haven in the middle of the business district is the Legazpi Active Park. It\'s a popular destination for locals and tourists alike who seek to enjoy a peaceful respite from the city’s usual hustle and bustle as well as enjoy activities including jogging, calisthenics, walking, and picnicking.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.554259, 121.016916",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Crane and Turtle Garden",
        "Description" =>
            "Inside the Park is the Crane and Turtle Garden inaugurated in celebration of the lasting legacy of friendship between the Philippines and Japan. It\'s the only oriental garden in the financial district.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.553987, 121.018265",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "168 Shopping Mall",
        "Description" =>
            "168 Shopping Mall is a popular shopping complex in Binondo. Almost everything can be found here, from food, ornaments, furniture, bags, shoes, toys, hardware, RTWs, and others kinds of goods. 168 Shopping Mall is within Divisoria, a budget-friendly market in between Binondo and Tondo, Manila known for its shops that sell low-priced goods and its diverse offerings.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "7:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "7:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "7:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "7:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "7:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "7:30 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "7:30 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Greenbelt Park",
        "Description" =>
            "Also across Glorietta is Greenbelt Park. It\'s surrounded by shops, a church, and a koi fishpond all creating an easy, breezy, and relaxed atmosphere. Inside is a vibrant modern dome chapel, the Sto. Nino de Paz Greenbelt Chapel.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.552825, 121.022268",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "nSovNJIoukE",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Puerta del Parian",
        "Description" =>
            "Main ceremonial gate of Intramuros since the 18th century. Served as the entry point to Intramuros from the Chinese during the Spanish colonial period.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Ayala Triangle",
        "Description" =>
            "The Ayala Triangle is an urban park located at the heart of Makati’s business district. Named after the Ayala Corporation, the leading developer of Makati, and one of the proponents who transformed the city into a bustling business and financial hub, this space is perfect for locals and tourists who want to do various run, eat, or simply take a break in a lush green open space.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.55838, 121.03124",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "St Peter and Paul Parish",
        "Description" =>
            "Down the street is Sts. Peter and Paul Parish Church, a 400-year-old old baroque church is considered as the mother parish of Makati. It was destroyed during the British occupation in 1762, then was reconstructed in 1849, and has undergone numerous renovations.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Nuestra Senoria de Gracia Parish",
        "Description" =>
            "Another piece of history found in Makati is the Nuestra Senora de Garcia Parish Church, a baroque church which features Neo-Romanesque-Gothic styles. The ruins are all that remain of one of the country’s oldest churches. It’s been used as a fortress during the War of Philippine Independence in 1898. It is also a popular wedding venue in the City today.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.56875, 121.04716",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Museo ng Makati and Makati Poblacion Park",
        "Description" => "Going back in time, Museo ng Makati is dedicated to showcasing the rich history and culture of Makati as well as the Philippines. The museum is housed in what once was the city\'s municipal hall. The building features Spanish colonial architecture.

Across Museo ng Makati is Makati Poblacion Park, an urban linear park along the south bank of the Pasig River in Makati.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => " Dr Jose P. Rizal Ave",
            "city" => "Makati",
            "country" => "Philippines",
            "coordinates" => "14.567838, 121.03276",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "ed3qcCIvclE",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Century City Mall",
        "Description" =>
            "Another indicator of Makati\'s progress and development is in Makati\'s Century City. The Century City Mall is also a popular attraction in the area as it features high-end retail shops, restaurants, and entertainment centers. It boasts a number of international brands, making it a shopping destination for both locals and tourists.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.565608, 121.027717",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Poblacion",
        "Description" => "A few steps down Century City is Poblacion, Makati. It\'s is a vibrant and diverse district known for its lively nightlife, trendy restaurants, and street art. This goes to show that Makati is known, not just as a business district, but also as a lively and dynamic spot.

Whether you plan to explore this party of the city in the morning or at night, best prepare a couple of hours or more, especially when the fun does not seem to end!",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "Poblacion",
            "city" => "Makati",
            "country" => "Philippines",
            "coordinates" => "14.565694418477952, 121.02773618357988",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "kFVJXSDWyUs",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Our Lady of Remedios Parish Church",
        "Description" =>
            "Our Lady of Remedios Parish Church, also known as Malate Church, boasts of a Mexican-Baroque style architecture and is overlooking the Rajah Sulayman Park that stretches towards the Manila Bay. The church is dedicated to Nuestra Señora de los Remedios, the patroness of childbirth, a revered statue of the Virgin Mary in her role as Our Lady of Remedies that dates back to 1624 Spain.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5694541142135, 120.984419382285",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Ayala Legaspi Underpass",
        "Description" =>
            "Along Ayala Avenue, is a busy underpass, but it is also a public art installation with vibrant murals across its walls and ceilings. Regularly, local artists share their talents and keep the walls fresh with Filipino talent.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Robinsons Manila",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "National Shrine Of Guadalupe",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Circuit Makati",
        "Description" =>
            "Another one of Makati’s innovative spaces, Circuit Makati, was once the Sta. Ana horse race track, which is part of Makati’s heritage. Now, it has been transformed into a mixed-use development, featuring Broadway-like entertainment and FIFA-sized football facilities. Circuit Makati offers diverse shopping and entertainment options.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "Carmona",
            "city" => "Makati",
            "country" => "Philippines",
            "coordinates" => "14.575733, 121.017357",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "PjVB63lhnFE",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Plazuela de Sta Isabel -Memore - Manila 1945",
        "Description" =>
            "The Plazuela (literally small plaza) hosts the Memorare, a monument to the civilians who died in Manila during the Second World War.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Chinatown Museum",
        "Description" =>
            "Inside Lucky Chinatown Mall is the Chinatown Museum. This museum is unique compared to other museums around Manila as it tells the history of Manila from world’s oldest Chinatown, Binondo\'s perspective. The museum showcases beautiful curated exhibits utilizing both traditional and digital installations guaranteed to indulge goers.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Lucky Chinatown Mall",
        "Description" =>
            "A more recent mall established in Binondo is Lucky Chinatown Mall. It offers a more modern approach to shopping in Binondo, while still incorporating history, culture, and tradition Binondo has always been known for.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "10:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Palacio del Gobernador",
        "Description" =>
            "Present condominium building was constructed in 1976 on the site of the original palace of the governor-general during the Spanish colonial period.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                ],
            ],
        ],
    ],
    [
        "Name" => "Postigo de Palacio",
        "Description" =>
            "Present building built in 2011 as a reconstruction of the original 19th c. Ayuntamiento building on site. The Ayuntamiento (literally City Hall) was the central office of the Spanish colonial government.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "North Sy-Quia Apartments",
        "Description" =>
            "Located on M.H. Del Pilar, the Syquia Apartments were once among the most prime apartments in the country. Divided into North Syquia Apartments and South Syquia Apartments, they are popularly known as an enclave for artists, storied photographers, publishers, designers, as among those who have called Syquia home in its 81-year history.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5682677154999, 120.984722126461",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Balikbayan Handikrafts",
        "Description" =>
            "Across Glorietta stands Balikbayan Handicrafts, a handicraft store and museum featuring a wide array of Philippine handicrafts ranging from wood furniture pieces to shell and pearl collections.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Glorietta 2",
        "Description" =>
            "Glorietta is a shopping mall complex in Ayala Center, Makati, and has wide selection of shopping, dining, and entertainment offerings.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "Palm Dr",
            "district" => "",
            "city" => "Makati",
            "country" => "Philippines",
            "coordinates" => "14.551077, 121.0244787",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "nSovNJIoukE",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Ayala Malls Circuit Roof Deck",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Lagusnilad Underpass",
        "Description" =>
            "Before visitors visit the national museums, they can pass through the newly rennovated Lagunsilad Underpass. It has a small vertical garden adding a green touch to the City, wood panels that gives it a contemporary vibe, and colorful and vibrant murals across the walls featuring Manila\'s history and its famous landmarks.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5898901975016, 120.98088833811",
        ],
        "Metadata" => "NULL",
        "OperatingHours" => "",
    ],
    [
        "Name" => "Flower Clock",
        "Description" =>
            "The Flower Clock is one of the most popular spots in the area. It\'s a giant clock made and adorned with beautiful orchids and flowers.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5835191321379, 120.979643609274",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Fort Santiago",
        "Description" =>
            "The Spaniards built the fort in stone in 1590, on the site of Rajah Soliman’s settlement Manila.  Served as headquarters of the Spanish, American, British and Japanese military forces. Declared by the National Museum of the Philippines as a National Cultural Treasure.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "9:00 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Japanese Garden",
        "Description" =>
            "To the side of the Flower Clock is a beautiful landscape called the Japanese Garden. This part of Luneta Park will give you an Oriental feel with its Japanese-inspired art and structures, and different kinds of Japanese bonzai, plants and flowers.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "country" => "Philippines",
            "coordinates" => "14.5844438834641, 120.979273397626",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Bas Relief Of Landing Of Don Miguel",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Ancestral Houses (Cu-Unijeng, Tolentino)",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "GOMO Skate Park",
        "Description" =>
            "Other awesome activities to do in Circuit are going to an interactive museum, Dream Lab, go bouldering at Bouldering Hive, try to escape in Left Behind\'s escape rooms, and go for a stroll at the mall\'s colorful and vibrant skate park.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Ayuntamiento de Manila",
        "Description" =>
            "Present building built in 2011 as a reconstruction of the original 19th c. Ayuntamiento building on site. The Ayuntamiento (literally City Hall) was the central office of the Spanish colonial government.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "5:00 PM",
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "7:30 AM",
                    "closingHours" => "5:00 PM",
                ],
            ],
        ],
    ],
    [
        "Name" => "Washington Sycip Park",
        "Description" =>
            "This park is named after Filipino accountant and banker Washington Sycip. The Washington Sycip Park offers a relaxing place to sit and enjoy a greenscape escape right in the middle of the city.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "Legazpi Street",
            "city" => "Makati",
            "country" => "Philippines",
            "coordinates" => "14.554004, 121.017861",
        ],
        "Metadata" => [
            "adultPrice" => 0,
            "childrenPrice" => 0,
            "youTubeVideoId" => "JMxZqvvcor4",
        ],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "10:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "10:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "10:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "10:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "10:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "10:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "6:00 AM",
                    "closingHours" => "10:00 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Bahay Ugnayan Foundation",
        "Description" =>
            "Explore the captivating timeline of President Bongbong Marcos\'s life, from his humble beginnings to his education, political journey, and personal stories. Witness his remarkable rise to becoming the 17th president of the Philippines, showcasing his unwavering dedication to public service.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Baluarte de San Diego",
        "Description" =>
            "The oldest structure in Intramuros was built as a watchtower in 1587. Declared by the National Museum of the Philippines as a National Cultural Treasure.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "6:00 pm",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Minor Basilica and Nationa Shrine of Saint Lorenzo Ruiz",
        "Description" =>
            "The Minor Basilica and National Shrine of Saint Lorenzo Ruiz or Binondo Church sits in Ongpin St. A landmark granite church and houses a large 16th century bell that can be heard across Binondo.",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "12:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "8:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "8:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "8:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "8:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "8:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "8:30 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Museo de intramuros",
        "Description" =>
            "Reconstruction of the famed Jesuit church and convent in Intramuros. Features one of the finest and largest collections of church-related antiques in the country.",
        "Address" => [
            "culture" => "PH",
            "addressType" => "0",
            "addressLine" => "Corner Arzobispo",
            "street" => "Anda st",
            "district" => "Intramuros",
            "city" => "Manila",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "zipCode" => "1002",
            "coordinates" => "14.58994, 120.97323",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
            "hours" => [
                [
                    "day" => "Monday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Tuesday",
                    "openingHours" => "9:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Wednesday",
                    "openingHours" => "9:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Thursday",
                    "openingHours" => "9:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Friday",
                    "openingHours" => "9:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Saturday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
                [
                    "day" => "Sunday",
                    "openingHours" => "8:00 AM",
                    "closingHours" => "5:00 PM",
                    "isOpen" => true,
                ],
            ],
        ],
    ],
    [
        "Name" => "Teus Museum",
        "Description" =>
            "Teus Mansion was restored by British designer Ronnie Laing in 1975, and use as a guest house to receive guests and visiting dignitaries of the late President Ferdinand E. Marcos Sr.",
        "Address" => [
            "culture" => "PH",
            "alias" => "",
            "addressType" => "0",
            "addressLine" => "Teus Mansion",
            "street" => "Gen. Solano street",
            "district" => "San Miguel",
            "city" => "Manila",
            "region" => "National Capital Region",
            "country" => "Philippines",
            "coordinates" => "14.353012, 120.592184",
        ],
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
    [
        "Name" => "Saint Andrew the Apostle",
        "Description" => "",
        "Address" => "",
        "Metadata" => ["adultPrice" => 0, "childrenPrice" => 0],
        "OperatingHours" => [
            "isOpenOnHolidays" => true,
            "isOpenOnSpecialHolidays" => true,
        ],
    ],
];
    foreach ($arrayVar as $attractionData) {
        $attraction = Attraction::where('Name', $attractionData['Name'])->first();

        if ($attraction) {
            $attraction->description = $attractionData['Description'];
            $attraction->youtube_id = isset($attractionData['Metadata']['youTubeVideoId']) ? $attractionData['Metadata']['youTubeVideoId'] : null;

            $operatingHours = "Monday : Closed\nTuesday : 9:00 am - 8:00 PM\nWednesday : 9:00 am - 8:00 PM\nThursday : 9:00 am - 8:00 PM\nFriday : 9:00 am - 8:00 PM\nSaturday : 9:00 am - 8:00 PM\nSunday : 9:00 am - 8:00 PM";

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

            $attraction->operating_hours = $operatingHours;

            $attraction->save();
        }
    }

    }
}
