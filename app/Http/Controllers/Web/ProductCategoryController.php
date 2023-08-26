<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ProductCategory;

use DataTables;

class ProductCategoryController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = ProductCategory::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('organizations', function ($row) {
                        return null;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/product_categories/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions', 'actions'])
                    ->make(true);
        }
        return view('admin-page.product_categories.list_product_category');
    }

    public function create(Request $request) {
        // return view();
    }

    public function store(Request $request) {

    }

    public function edit(Request $request) {

    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {

    }
}
