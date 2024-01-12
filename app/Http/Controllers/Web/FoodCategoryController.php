<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\FoodCategory\StoreRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Models\FoodCategory;
use App\Models\Merchant;

class FoodCategoryController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = FoodCategory::select('id', 'merchant_id', 'title')->with('merchant')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('merchant', function ($row) {
                    return optional($row->merchant)->name;
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                    <a href="/admin/food-categories/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                                </div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view("admin-page.food_categories.list-food-category");
    }

    public function create()
    {
        $merchants = Merchant::where('type', 'Restaurant')->where('is_active', 1)->get();
        return view("admin-page.food_categories.create-food-category", compact('merchants'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $foodCategory = FoodCategory::create($data);

        return redirect()->route('admin.food_categories.edit', $foodCategory->id)->with('success', 'Food Category Added Successfully');
    }

    public function show($id)
    {

    }

    public function edit(Request $request, $id)
    {
        $merchants = Merchant::where('type', 'Restaurant')->where('is_active', 1)->get();
        $foodCategory = FoodCategory::findOrFail($id);
        return view("admin-page.food_categories.edit-food-category", compact('foodCategory', 'merchants'));
    }

    public function update(StoreRequest $request, $id)
    {
        $data = $request->validated();
        $foodCategory = FoodCategory::findOrFail($id);

        $foodCategory->update($data);

        return back()->withSuccess('Food Category Updated Successfully')->with('success', 'Food Category Updated Successfully');
    }

    public function destroy($id)
    {
        $foodCategory = FoodCategory::findOrFail($id);
        $foodCategory->delete();

        return response([
            'status' => TRUE,
            'message' => 'Food Category Deleted Successfully'
        ]);
    }

    public function getFoodCategorySelect(Request $request, $merchant_id)
    {
        $foodCategories = FoodCategory::where('merchant_id', $request->merchant_id)->select('id', 'title', 'merchant_id')->with('merchant')->get();
        $foodCategories = $foodCategories->map(function ($foodCategory) {
            return [
                'id' => $foodCategory->id,
                'text' => $foodCategory->title . ' ' . "( " . $foodCategory->merchant->name . " )",
            ];
        });

        return $foodCategories;
    }
}
