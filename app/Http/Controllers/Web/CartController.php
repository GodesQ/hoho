<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Cart;
use Yajra\DataTables\DataTables;

class CartController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $carts = Cart::with('user', 'tour')->get();

            return DataTables::of($carts)
                    ->addIndexColumn()
                    ->addColumn("user", function ($row) {
                        if($row->user) {
                            return $row->user->email;
                        } else {
                            return "Deleted User";
                        }
                    })
                    ->addColumn("tour", function ($row) {
                        if($row->tour) {
                            return $row->tour->name;
                        } else {
                            return "Deleted Tour";
                        }
                    })
                    ->addColumn("actions", function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/carts/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-file me-1"></i></a>
                                    <a href="javascript:void(0);" id="'.$row->id.'" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view("admin-page.carts.list-cart");
    }

    public function edit(Request $request) {
        $cart = Cart::with('tour', 'user')->findOrFail($request->id);

        return view('admin-page.carts.edit-cart', compact('cart'));
    }

    public function destroy(Request $request) {
        $cart = Cart::find($request->id);

        if($cart) {
            $cart->delete();
            
            return response([
                'status' => TRUE,
                'message' => 'Cart Deleted Successfully'
            ]);
        } else {
            return response([
                'status' => FALSE,
                'message' => 'Cart Not Found'
            ], 400);
        }
    }
}
