<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $products = Product::query();

            if($request->search['value'] && $request->ajax()) {
                $searchValue = $request->search['value'];
                $products = $products->where('name', 'LIKE', $searchValue . '%');
            }
            
            return DataTables::of($products)
                ->addIndexColumn()
                ->editColumn('product', function ($row) {
                    return view('components.merchant-product', ['product' => $row ]);
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
                                        <a href="/admin/products/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                        <button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                                    </div>';
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view("admin-page.products.list-product");
    }

    public function create(Request $request)
    {   
        $user = Auth::guard('admin')->user();
        if($user->role == Role::MERCHANT_STORE_ADMIN) {
            $merchants = Merchant::where('type','Store')->where('id', $user->merchant_id)->get();
        } else {
            $merchants = Merchant::where('type', 'Hotel')->get();
        }
        return view('admin-page.products.create-product', compact('merchants'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->except('image', 'other_images');

        $product = Product::create(array_merge($data, 
                    [
                        'is_active' => $request->has('is_active'), 
                        'is_best_seller' => $request->has('is_best_seller')
                    ]
                ));

        if ($request->has('image')) {
            $file = $request->file('image');
            $name = Str::snake(Str::lower($request->name)) . '_' . time();
            $filename = $name . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/img/products/' . $product->id, $filename);

            $product->update([
                'image' => $filename,
            ]);
        }

        $images = [];

        if($request->has('other_images')) {
            foreach ($request->other_images as $key => $image) {
                $name = Str::snake(Str::lower($request->name)) . '_' . 'other_image' . '_'  . time();
                $filename = $name . '.' . $image->getClientOriginalExtension();
                $image->move(public_path() . '/assets/img/products/' . $product->id, $filename);

                array_push($images, $filename);
            }

            $product->update([
                'other_images' => count($images) > 0 ? json_encode($images) : null,
            ]);
        }

        return redirect()->route('admin.products.edit', $product->id)->withSuccess('Product Added Successfully');

    }

    public function show(Request $request, $id) {
        $product = Product::findOrFail($id);
        
        if($request->ajax()) {
            return response()->json([
                'message' => 'Product Found',
                'product' => $product,
            ]);
        }

        return;
    } 

    public function edit($id)
    {   
        $user = Auth::guard('admin')->user();
        if($user->role == Role::MERCHANT_STORE_ADMIN) {
            $merchants = Merchant::where('type','Store')->where('id', $user->merchant_id)->get();
        } else {
            $merchants = Merchant::where('type', 'Hotel')->get();
        }

        $product = Product::where('id', $id)->with('merchant')->firstOrFail();
        return view('admin-page.products.edit-product', compact('merchants','product'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->except('image', 'other_images');
        $product = Product::where('id', $id)->firstOrFail();

        $product->update(array_merge($data, [
            'is_active' => $request->has('is_active'),
            'is_best_seller' => $request->has('is_best_seller')
        ]));

        // Primary Image
        if ($request->has('image')) {
            // remove old image
            $old_upload_image = public_path('assets/img/products/') . $product->id . '/' . $product->image;
            @unlink($old_upload_image);

            $file = $request->file('image');
            $name = Str::snake(Str::lower($request->name)) . '_' . time();
            $filename = $name . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . '/assets/img/products/' . $product->id, $filename);

            $product->update([
                'image' => $filename,
            ]);
        }

        $images = $product->other_images ? json_decode($product->other_images) : [];

        // Other Images
        if($request->has('other_images')) {
            foreach ($request->other_images as $key => $image) {
                $name = Str::snake(Str::lower($request->name)) . '_' . 'other_image' . '_'  . time();
                $filename = $name . '.' . $image->getClientOriginalExtension();
                $image->move(public_path() . '/assets/img/products/' . $product->id, $filename);

                is_array($images) ? array_push($images, $filename) : false;
            }

            $product->update([
                'other_images' => count($images) > 0 ? json_encode($images) : null,
            ]);
        }

        return back()->withSuccess('Product Updated Successfully');

    }

    public function destroy($id)
    {
        $room = Product::where('id', $id)->firstOrFail();

        $directory = public_path('assets/img/products/') . $room->id;
        $files = glob($directory . '/*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        // Now remove the directory
        if (is_dir($directory)) @rmdir($directory);

        $room->delete();

        return [
            'status' => TRUE,
            'message' => 'Product Deleted Successfully'
        ];
    }

    public function lookup(Request $request) {
        $user = Auth::guard('admin')->user();

        $searchQuery = $request->input('q');
        $products = Product::orWhere('name', 'LIKE', "%$searchQuery%")
            ->select('id', 'name', 'merchant_id')
            ->when($user->is_merchant, function($q) use ($user) {
                $q->where('merchant_id', $user->merchant_id);
            })
            ->with('merchant')
            ->get();

        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'text' => $product->name . ' - ' . '(' . $product->merchant->name . ')',
            ];
        }

        return response()->json($formattedProducts);
    }

    public function removeImage(Request $request) {
        $product = Product::where('id', $request->id)->first();
        $images = json_decode($product->other_images);
        $image_path = $request->image_path;

        // dd(array_search($image_path, $images));

        if(is_array($images)) {
            if (($key = array_search($image_path, $images)) !== false) {
                unset($images[$key]);
                $old_upload_image = public_path('/assets/img/products/') . $product->id . '/' . $image_path;
                @unlink($old_upload_image);
            }
        }

        $update = $product->update([
            'other_images' => is_array($images) && count($images) > 0 ? json_encode(array_values($images)) : null,
        ]);

        if($update) {
            return response([
                'status' => TRUE,
                'message' => 'Image successfully remove'
            ]);
        }
    } 

}
