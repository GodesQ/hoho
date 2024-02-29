<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Food\StoreRequest;
use Illuminate\Http\Request;
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
                    if($row->is_active) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-warning">Inactive</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                    <a href="/admin/foods/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
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
        $merchants = Merchant::where('type', 'Restaurant')->where('is_active', 1)->get();
        $foodCategories = FoodCategory::with('merchant')->get();
        return view('admin-page.foods.create-food', compact('merchants', 'foodCategories'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $food = Food::create(
            array_merge($data, [
                'is_active' => $request->has('is_active') ? true : false
            ])
        );

        return redirect()->route('admin.foods.edit', $food->id)->with('success', 'Food Added Successfully');
    }

    public function show($id)
    {
        
    }

    public function edit(Request $request, $id)
    {
        $food = Food::findOrFail($id);
        $merchants = Merchant::where('type', 'Restaurant')->where('is_active', 1)->get();
        $foodCategories = FoodCategory::with('merchant')->get();
        return view('admin-page.foods.edit-food', compact('food', 'merchants', 'foodCategories'));
    }

    public function update(StoreRequest $request, $id)
    {
        $data = $request->validated();
        $food = Food::findOrFail($id);

        $food->update(array_merge($data, [
            'is_active' => $request->has('is_active') ? true : false
        ]));

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
}
           