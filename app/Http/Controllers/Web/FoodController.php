<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Food\StoreRequest;
use App\Http\Requests\Food\UpdateRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\Merchant;

class FoodController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Food::where('is_active', 1)->with('merchant', 'food_category');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('merchant', function ($row) {
                    return optional($row->merchant)->name;
                })
                ->addColumn('food_category', function ($row) {
                    return optional($row->food_category)->title;
                })
                ->addColumn('status', function ($row) {
                    if ($row->is_active) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-warning">Inactive</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                <a href="' . route('admin.foods.edit', $row->id) . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                            </div>';
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('admin-page.foods.list-food');
    }

    public function create()
    {
        $user = Auth::guard('admin')->user();

        $merchants = Merchant::where('type', 'Restaurant')
            ->when($user->role === Role::MERCHANT_RESTAURANT_ADMIN, function ($query) use ($user) {
                $query->where('id', $user->merchant_id);
            })
            ->get();

        $foodCategories = FoodCategory::with('merchant')->get();
        return view('admin-page.foods.create-food', compact('merchants', 'foodCategories'));
    }

    public function store(Request $request)
    {

        $data = $request->except('image', 'other_images', '_token');

        $food = Food::create(
            array_merge($data, [
                'is_active' => $request->has('is_active') ? true : false
            ])
        );

        if ($request->has('image')) {
            $file = $request->file('image');
            $name = Str::snake(Str::lower($request->titleg)) . '_' . time();
            $filename = $name . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/img/foods/' . $food->id, $filename);

            $food->update([
                'image' => $filename,
            ]);
        }

        $images = [];

        if ($request->has('other_images')) {
            foreach ($request->other_images as $key => $image) {
                $name = Str::snake(Str::lower($request->title)) . '_' . 'other_image' . '_' . time();
                $filename = $name . '.' . $image->getClientOriginalExtension();
                $image->move(public_path() . '/assets/img/foods/' . $food->id, $filename);

                array_push($images, $filename);
            }

            $food->update([
                'other_images' => count($images) > 0 ? json_encode($images) : null,
            ]);
        }

        return redirect()->route('admin.foods.edit', $food->id)->with('success', 'Food Added Successfully');
    }

    public function show($id)
    {

    }

    public function edit(Request $request, $id)
    {
        $food = Food::findOrFail($id);
        $user = Auth::guard('admin')->user();

        if ($user->role == Role::MERCHANT_RESTAURANT_ADMIN) {
            $merchants = Merchant::where('id', $user->merchant_id)->where('type', 'Restaurant')->get();
        } else {
            $merchants = Merchant::where('type', 'Restaurant')->get();
        }

        $foodCategories = FoodCategory::with('merchant')->get();
        return view('admin-page.foods.edit-food', compact('food', 'merchants', 'foodCategories'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->except('image', 'other_images', '_token');
        $food = Food::findOrFail($id);

        $food->update(array_merge($data, [
            'is_active' => $request->has('is_active') ? true : false
        ]));
        if ($request->has('image')) {
            // remove old image
            $old_upload_image = public_path('assets/img/foods/') . $food->id . '/' . $food->image;
            @unlink($old_upload_image);

            $file = $request->file('image');
            $name = Str::snake(Str::lower($request->title)) . '_' . time();
            $filename = $name . '.' . $file->getClientOriginalExtension();
            $path_folder = 'foods/' . $food->id . '/';

            Storage::disk('public')->putFileAs($path_folder, $file, $filename);

            $food->update([
                'image' => $filename,
            ]);
        }

        $images = $food->other_images ? json_decode($food->other_images) : [];

        // Other Images
        if ($request->has('other_images')) {
            foreach ($request->other_images as $key => $image) {
                $time = time() . $key;
                $name = Str::snake(Str::lower($request->title)) . '_' . 'other_image' . '_' . $time;
                $filename = $name . '.' . $image->getClientOriginalExtension();
                $image->move(public_path() . '/assets/img/foods/' . $food->id, $filename);

                is_array($images) ? array_push($images, $filename) : false;
            }

            $food->update([
                'other_images' => count($images) > 0 ? json_encode($images) : null,
            ]);
        }

        return back()->withSuccess('Food Updated Successfully')->with('success', 'Food Updated Successfully');
    }

    public function destroy($id)
    {
        $food = Food::findOrFail($id);
        $food->delete();

        return response([
            'status' => TRUE,
            'message' => 'Food Removed Successfully'
        ]);
    }

    public function removeImage(Request $request)
    {
        $food = Food::where('id', $request->id)->first();
        $images = json_decode($food->other_images);
        $image_path = $request->image_path;

        if (is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/foods/') . $food->id . '/' . $image_path;
                $remove_image = @unlink($old_upload_image);
            }
        }

        $update = $food->update([
            'other_images' => json_encode(array_values($images))
        ]);

        if ($update) {
            return response([
                'status' => TRUE,
                'message' => 'Image successfully remove'
            ]);
        }
    }
}
