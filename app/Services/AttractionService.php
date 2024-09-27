<?php

namespace App\Services;

use App\Models\Attraction;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class AttractionService
{
    public function getAttractions(Request $request) {
        return Attraction::with('organization')->get();
    }

    public function createAttraction(Request $request) {
        try {
            DB::beginTransaction();

            $data = $request->except('_token', 'organization_ids', 'images', 'featured_image', 'interests');

            $attraction = Attraction::create(array_merge($data, [
                'interest_ids' => $request->has('interests') ? json_encode($request->interests) : null,
                'product_category_ids' => $request->has('product_categories') ? json_encode($request->product_categories) : null,
                'nearest_attraction_ids' => $request->has('nearest_attraction_ids') ? json_encode($request->nearest_attraction_ids) : null,
                'nearest_store_ids' => $request->has('nearest_store_ids') ? json_encode($request->nearest_store_ids) : null,
                'nearest_restaurant_ids' => $request->has('nearest_restaurant_ids') ? json_encode($request->nearest_restaurant_ids) : null,
                'nearest_hotel_ids' => $request->has('nearest_hotel_ids') ? json_encode($request->nearest_hotel_ids) : null,
                'is_cancellable' => $request->has('is_cancellable'),
                'is_refundable' => $request->has('is_refundable'),
                'is_featured' => $request->has('is_featured'),
                'status' => $request->has('is_active'),
            ]));

            $file_name = null;
    
            if($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $file_name = Str::snake(Str::lower($request->name)) . '.' . $file->getClientOriginalExtension();
                $path = "attractions/" . $attraction->id . "/";
                FileService::upload($path, $file_name, $file);
            }
    
            $images = [];

            if($request->images && is_array($request->images)) {
                foreach ($request->images as $key => $image) {
                    $uniqueId = Str::random(5) . time();
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image->getClientOriginalExtension();
                    $path = "attractions/" . $attraction->id . "/";
                    FileService::upload($path, $image, $image_file_name);
                    
                    array_push($images, $image_file_name);
                }
            }
    
            $attraction->update([
                'featured_image' => $file_name,
                'images' => count($images) > 0 ? json_encode($images) : null,
            ]);

            DB::commit();
            
            return $attraction;
        } catch (ErrorException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateAttraction(Request $request) {
        try {
            $data = $request->except("_token", "images");
            $attraction = Attraction::findOrFail($request->id);

            if($request->hasFile('featured_image')) {
                $uniqueId = Str::random(5);

                $file = $request->file('featured_image');
                $file_name = Str::snake(Str::lower($request->name)) . '_featured_image_' . $uniqueId . '.' . $file->getClientOriginalExtension();

                $old_upload_image = public_path('assets/img/attractions/') . $attraction->id . '/' . $attraction->featured_image;

                if ($old_upload_image) {
                    @unlink($old_upload_image);
                }

                $file->move(public_path() . '/assets/img/attractions/' . $attraction->id, $file_name);
            } else {
                $file_name = $attraction->featured_image;
            }

            $images = $attraction->images ? json_decode($attraction->images) : [];

            if($request->has('images')) {
                foreach ($request->images as $key => $image) {
                    $uniqueId = Str::random(5);
                    $image_file = $image;
                    $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $uniqueId . '.' . $image_file->getClientOriginalExtension();
                    $image_file->move(public_path() . '/assets/img/attractions/' . $attraction->id, $image_file_name);
                    array_push($images, $image_file_name);
                }
            }

            $update_attraction = $attraction->update(array_merge($data, [
                'featured_image' => $file_name,
                'interest_ids' => $request->has('interests') ? json_encode($request->interests) : null,
                'product_category_ids' => $request->has('product_categories') ? json_encode($request->product_categories) : $attraction->product_category_ids,
                'nearest_attraction_ids' => $request->has('nearest_attraction_ids') ? json_encode($request->nearest_attraction_ids) : null,
                'nearest_store_ids' => $request->has('nearest_store_ids') ? json_encode($request->nearest_store_ids) : null,
                'nearest_restaurant_ids' => $request->has('nearest_restaurant_ids') ? json_encode($request->nearest_restaurant_ids) : null,
                'nearest_hotel_ids' => $request->has('nearest_hotel_ids') ? json_encode($request->nearest_hotel_ids) : null,
                'images' => count($images) > 0 ? json_encode($images) : $attraction->images,
                'is_cancellable' => $request->has('is_cancellable'),
                'is_refundable' => $request->has('is_refundable'),
                'is_featured' => $request->has('is_featured'),
                'status' => $request->has('is_active'),
            ]));

            return $update_attraction;
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', $e->getMessage());
        }
    }

    public function destroyAttraction(Request $request) {
        try {
            $attraction = Attraction::where('id', $request->id)->firstOr(function () {
                return response()->json([
                    'status' => false,
                    'message' => 'Not Found'
                ]);
            });

            $old_upload_image = public_path('assets/img/attractions/') . $attraction->id . '/' . $attraction->featured_image;
            if($old_upload_image) {
                $remove_image = @unlink($old_upload_image);
            }

            // Remove all files from the directory
            $directory = public_path('assets/img/attractions/') . $attraction->id;
            
            $files = glob($directory . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }

            // Now remove the directory
            if (is_dir($directory)) @rmdir($directory);
    
            return $attraction->delete();
            
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', $e->getMessage());
        }
    }

    public function generateDataTables($data) {
        // dd($data);
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
}