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
            $data = Attraction::latest('id');
            return DataTables::of($data)
                ->addIndexColumn()
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
                ->rawColumns(['actions', 'status'])
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

        $count = 1;
        $images = [];
        if($request->images) {
            foreach ($request->images as $key => $image) {
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $count . '.' . $image_file->getClientOriginalExtension();
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
        $data = $request->except("_token");
        $attraction = Attraction::findOrFail($request->id);
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $file_name = Str::snake(Str::lower($request->name)) . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/attractions/' . $attraction->id, $file_name);
        } else {
            $file_name = $attraction->featured_image;
        }

        $images = $attraction->images ? json_decode($attraction->images) : [];
        $count = $attraction->images ? count(json_decode($attraction->images)) : 1;

        if($request->has('images')) {
            foreach ($request->images as $key => $image) {
                $count++;
                $image_file = $image;
                $image_file_name = Str::snake(Str::lower($request->name)) . '_image_' . $count . '.' . $image_file->getClientOriginalExtension();
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
}
