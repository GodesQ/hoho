<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()) {
            $orders = Order::with('product', 'customer');

            return DataTables::of($orders)
            ->editColumn('buyer_id', function ($order) {
                return view('components.user-contact', ['user' => $order->customer]);
            })
            ->editColumn('product_id', function ($order) {
                return $order->product->name;
            })
            ->addColumn('actions', function ($row) {
                $output = '<div class="dropdown">';
                    
                $output .= '<a href="/admin/orders/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>';

                if($row->status != 'approved') {
                    $output .= '<button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>';
                }
                
                $output .= '</div>';

                return $output;
            })
            ->rawColumns(['actions'])
            ->make(true);
        }

        return view('admin-page.orders.list-order');
    }

    public function create() {
        $products = Product::get();
        $customers = User::get();

        return view('admin-page.orders.create-order', compact('products', 'customers'));
    }

    public function store(StoreRequest $request) {
        $product = Product::where('id', $request->product_id)->first();
        $totalAmount = $this->calculateTotalAmount($product->price, $request->quantity);

        
    }

    public function show($id) { 
    
    }

    public function edit(Request $request, $id) {
        
    }

    public function update(Request $request, $id) { 
        
    }
    
    public function destroy($id) {
    
    }

    private function calculateTotalAmount($price, $quantity) {
        return $price * $quantity;    
    }
}
