<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\ProductCategory;
use App\Models\Organization;

use DataTables;

class ProductCategoryController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = ProductCategory::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('organizations', function ($row) {
                        $organizations = '';
                        if(is_array($row->organizations)) {
                            foreach ($row->organizations as $key => $organization) {
                                $organizations .= '<div class="badge bg-primary mb-75 mx-1">' . $organization['name'] . '</div>';
                            }
                        }
                        return $organizations;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/product_categories/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions', 'organizations'])
                    ->make(true);
        }
        return view('admin-page.product_categories.list_product_category');
    }

    public function create(Request $request) {
        $organizations = Organization::get();
        return view('admin-page.product_categories.create_product_category', compact('organizations'));
    }

    public function store(Request $request) {
        $data = $request->except('_token');
        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $file_name = Str::snake(Str::lower($request->name)) . '.' . $file->getClientOriginalExtension();
            // dd($file_name);
            $save_file = $file->move(public_path() . '/assets/img/product_categories', $file_name);
        } else {
            $file_name = null;
        }

        $product_category = ProductCategory::create(array_merge($data, [
            'featured_image' => $file_name,
            'organization_ids' => $request->has('organization_ids') ? json_encode($request->organization_ids) : null
        ]));

        if($product_category) return redirect()->route('admin.product_categories.edit', $product_category->id)->withSuccess('Product Category created successfully');
    }

    public function edit(Request $request) {
        $organizations = Organization::get();
        $product_category = ProductCategory::where('id', $request->id)->firstOrFail();
        return view('admin-page.product_categories.edit-product-category', compact('product_category', 'organizations'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $product_category = ProductCategory::where('id', $request->id)->firstOrFail();

        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $file_name = Str::snake(Str::lower($request->name)) . '.' . $file->getClientOriginalExtension();
            // dd($file_name);
            $save_file = $file->move(public_path() . '/assets/img/product_categories', $file_name);
        } else {
            $file_name = $product_category->featured_image;
        }

        $update_product_category = $product_category->update(array_merge($data, [
            'featured_image' => $file_name
        ]));

        if($update_product_category) return back()->withSuccess('Product Category updated successfully');
    }

    public function destroy(Request $request) {

    }
}
