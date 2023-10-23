<?php

namespace App\Services;

use App\Models\Attraction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class AttractionService
{
    public function getAttractions(Request $request) {
        return Attraction::with('organization')->get();
    }

    public function createAttraction(Request $request) {
        try {
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
            
            return $attraction;
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', $e->getMessage());
        }
    }

    public function updateAttraction(Request $request) {
        try {
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
    
            return $attraction->delete();
            
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', $e->getMessage());
        }
    }

    public function generateDataTables($data) {
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


?>