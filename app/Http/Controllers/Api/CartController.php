<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Cart;

class CartController extends Controller
{
    public function storeCart(Request $request) {
        $data = $request->all();

        $cart = Cart::create($data);

        if($cart) {
            return response([
                'status' => TRUE,
                'message' => 'Cart added successfully'
            ]);
        }
    }

    public function getUserCarts(Request $request) {
        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->with('tour')->latest('created_at')->get();

        return response([
            'status' => TRUE,
            'carts' => $carts
        ], 200);
    }

    public function getUserCart(Request $request) {
        $user = Auth::user();
        $cart = Cart::where('id', $request->id)->where('user_id', $user->id)->first();

        if($cart) {
            return response([
                'status' => TRUE,
                'cart' => $cart
            ], 200);
        } else {
            return response([
                'status' => TRUE,
                'cart' => null,
                'message' => 'Cart Not Found'
            ], 400);
        }

    }

    public function removeCart(Request $request) {
        $user = Auth::user();
        // return response($user);
        $cart = Cart::where('id', $request->id)->where('user_id', $user->id)->first();
        if($cart) {
            $cart->delete();
            return response([
                'status' => TRUE,
                'message' => 'Cart deleted successfully'
            ]);
        }

        return response([
            'status' => FALSE,
            'message' => 'Cart Not Found'
        ], 400);

    }
}
