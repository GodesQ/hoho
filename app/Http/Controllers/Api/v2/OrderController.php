<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request) {
        $orders = Order::all();
        return OrderResource::collection($orders);
    }

    public function show(Request $request, $order_id) {
        $order = Order::where("id", $order_id)->with('product', 'customer')->firstOrFail();
        return OrderResource::make($order);
    }

    public function userOrders(Request $request) {
        $userOrders = Order::where('id', $request->user_id)->with('product')->get();
        return OrderResource::collection($userOrders);
    }


}
