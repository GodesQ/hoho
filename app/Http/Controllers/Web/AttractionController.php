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
    // $arrayVar = [];
    // foreach ($arrayVar as $attractionData) {
    //     $attraction = Attraction::where('Name', $attractionData['Name'])->first();

    //     if ($attraction) {
    //         $attraction->description = $attractionData['Description'];
    //         $attraction->youtube_id = isset($attractionData['Metadata']['youTubeVideoId']) ? $attractionData['Metadata']['youTubeVideoId'] : null;

    //         $operatingHours = "Monday : Closed\nTuesday : 9:00 am - 8:00 PM\nWednesday : 9:00 am - 8:00 PM\nThursday : 9:00 am - 8:00 PM\nFriday : 9:00 am - 8:00 PM\nSaturday : 9:00 am - 8:00 PM\nSunday : 9:00 am - 8:00 PM";

    //         // Additional conditions for Operating Hours
    //         if (isset($attractionData['OperatingHours']['isOpenOnHolidays'])) {
    //             $operatingHours .= "\n\nOpen On Holidays: Yes";
    //         } else {
    //             $operatingHours .= "\n\nOpen On Holidays: No";
    //         }

    //         if (isset($attractionData['OperatingHours']['isOpen24Hours'])) {
    //             $operatingHours .= "\nOpen 24 Hours: Yes";
    //         } else {
    //             $operatingHours .= "\nOpen 24 Hours: No";
    //         }

    //         if (isset($attractionData['OperatingHours']['isOpenOnSpecialHolidays'])) {
    //             $operatingHours .= "\nOpen On Special Holidays: Yes";
    //         } else {
    //             $operatingHours .= "\nOpen On Special Holidays: No";
    //         }

    //         if (isset($attractionData['OperatingHours']['isClosedOnWeekends'])) {
    //             $operatingHours .= "\nClosed On Weekends: Yes";
    //         } else {
    //             $operatingHours .= "\nClosed On Weekends: No";
    //         }

    //         $attraction->operating_hours = $operatingHours;

    //         $attraction->save();
    //     }
    //   }

    }
}
