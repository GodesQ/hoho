<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Merchant;
use App\Models\MerchantHotel;

use DB;
use Yajra\DataTables\DataTables;


class MerchantHotelService
{   
    public function generateDataTable(Request $request, $data) {
        return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('featured_image', function ($row) {
                        $defaultImagePath = asset('assets/img/default-image.jpg');
                    
                        if ($row->merchant && $row->merchant->featured_image) {
                            $path = asset('assets/img/stores/' . $row->merchant->id . '/' . $row->merchant->featured_image);
                        } else {
                            $path = $defaultImagePath;
                        }            
                    
                        return '<img src="' . $path . '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
                    })
                    ->editColumn('name', function ($row) {
                        return ($row->merchant)->name;
                    })
                    ->editColumn('location', function($row) {
                        if($row->merchant->address) {
                            return view('components.merchant-location', ['data' => $row]);
                        }

                        return '-';
                    })
                    ->editColumn('nature_of_business', function($row) {
                        return optional($row->merchant)->nature_of_business;
                    })
                    ->editColumn('is_featured', function($row) {
                        if ($row->merchant->is_featured) {
                            return '<span class="badge bg-label-success me-1">Yes</span>';
                        } else {
                            return '<span class="badge bg-label-secondary me-1">No</span>';
                        }
                    })
                    ->editColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/merchants/hotels/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions', 'featured_image', 'is_featured'])
                    ->make(true);
    }

    public function CreateMerchantHotel(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'featured_image', 'main_featured_image', 'images');
            $merchant = Merchant::create(array_merge($data, ['is_active' => $request->has('is_active'), 'is_featured' => $request->has('is_featured')]));
            $file_name = null;
            $path_folder = 'hotels/' . $merchant->id . '/';

            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = Str::snake(Str::lower($request->name));
                $file_name = $name . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs($path_folder, $file, $file_name);

                $merchant->update([
                    'featured_image' => $file_name,
                ]);
            }

            if ($request->hasFile('main_featured_image')) {
                $main_featured_file = $request->file('main_featured_image');
                $name = Str::snake(Str::lower($request->name)) . '_main_featured_image';
                $file_name = $name . '.' . $main_featured_file->getClientOriginalExtension();
                $main_featured_file->move(public_path() . '/assets/img/hotels/' . $merchant->id, $file_name);

                $merchant->update([
                    'main_featured_image' => $file_name,
                ]);
            }

            if ($request->hasFile('brochure')) {
                $brochure_file = $request->file('brochure');
                $name = Str::snake(Str::lower($request->name)) . '_brochure_' . time();
                $brochure_file_name = $name . '.'. $brochure_file->getClientOriginalExtension();
                $brochure_file->move(public_path() . '/assets/img/hotels/' . $merchant->id, $brochure_file_name);
            }

            $images = array();

            if ($request->has('images')) {
                foreach ($request->file('images') as $count => $image) {
                    $uniqueId = Str::random(5);
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    $images[] = $image_file_name;
                }
            }

            $merchant_hotel_data = array_merge($data, [
                'merchant_id' => $merchant->id,
                'brochure' => $brochure_file_name ?? null,
                'images' => count($images) > 0 ? json_encode($images) : null,
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]);

            $merchant_hotel = MerchantHotel::create($merchant_hotel_data);

            if ($merchant_hotel) {
                return [
                    'status' => TRUE,
                    'merchant' => $merchant,
                    'merchant_hotel' => $merchant_hotel
                ];
            }

            return [
                'status' => FALSE,
                'merchant' => null,
                'merchant_hotel' => null
            ];
        });
    }

    public function UpdateMerchantHotel(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->except('_token', 'images', 'featured_image');

            $hotel = MerchantHotel::where('id', $request->id)->with('merchant')->firstOrFail();

            $update_hotel = $hotel->update(array_merge($data, [
                'interests' => $request->has('interests') ? json_encode($request->interests) : null
            ]));

            $images = $hotel->images ? json_decode($hotel->images) : [];

            $path_folder = 'hotels/' . $hotel->merchant->id . '/';

            if ($request->has('images')) {
                foreach ($request->images as $key => $image) {
                    $uniqueId = Str::random(5);
                    $image_file = $image;
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                    $save_file = Storage::disk('public')->putFileAs($path_folder, $image, $image_file_name);
                    $images[] = $image_file_name;
                }

                $update_hotel = $hotel->update([
                    'images' => count($images) > 0 ? json_encode($images) : $hotel->images,
                ]);
            }

            if ($request->hasFile('brochure')) {
                $brochure_file = $request->file('brochure');
                $name = Str::snake(Str::lower($request->name)) . '_brochure_' . time();
                $brochure_file_name = $name . '.'. $brochure_file->getClientOriginalExtension();
                $old_upload_brochure = public_path('assets/img/hotels/') . $hotel->merchant->id . '/' . $hotel->brochure;

                if($old_upload_brochure) {
                    $remove_image = @unlink($old_upload_brochure);
                }

                $brochure_file->move(public_path() . '/assets/img/hotels/' . $hotel->merchant->id, $brochure_file_name);

                $update_hotel = $hotel->update([
                    'brochure' => $brochure_file_name,
                ]);
            }

            // Save if the featured image exist in request
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = Str::snake(Str::lower($request->name));
                $file_name = $name . '.' . $file->getClientOriginalExtension();

                $old_upload_image = public_path('assets/img/hotels/') . $hotel->merchant->id . '/' . $hotel->merchant->featured_image;

                if ($old_upload_image) {
                    $remove_image = @unlink($old_upload_image);
                }

                $save_file = Storage::disk('public')->putFileAs($path_folder, $file, $file_name);

            } else {
                $file_name = $hotel->merchant->featured_image;
            }

            if($request->hasFile('main_featured_image')) {
                $file = $request->file('main_featured_image');
                $name = Str::snake(Str::lower($request->name)) . '_main_featured_image';
                $main_featured_file_name = $name . '.' . $file->getClientOriginalExtension();
                $old_upload_image = public_path('assets/img/restaurants/') . $hotel->merchant->id . '/' . $hotel->merchant->main_featured_image;

                if($old_upload_image) {
                    $remove_image = @unlink($old_upload_image);
                }

                $save_file = $file->move(public_path() . '/assets/img/hotels/' . $hotel->merchant->id, $main_featured_file_name);
            } else {
                $main_featured_file_name = $hotel->merchant->main_featured_image;
            }

            $update_merchant = $hotel->merchant->update(array_merge($data, [
                'featured_image' => $file_name, 
                'main_featured_image' => $main_featured_file_name,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
            ]));

            if ($update_hotel && $update_merchant) {
                return [
                    'status' => TRUE,
                    'merchant' => $hotel->merchant,
                    'merchant_hotel' => $hotel
                ];
            }

            return [
                'status' => FALSE,
                'merchant' => null,
                'merchant_hotel' => null
            ];
        });
    }
}
?>