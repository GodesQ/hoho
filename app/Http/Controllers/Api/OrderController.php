<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(StoreRequest $request) {
        $data = $request->validated();

        $product = Product::where('id', $request->product_id)->first();
        $totalAmount = $this->calculateTotalAmount($product->price, $request->quantity);

        $transaction = DB::transaction(function () use ($request, $product, $totalAmount) {
            $reference_no = $this->generateReferenceNo();
            $transaction = Transaction::create([
                'reference_no' => $reference_no,
                'transaction_by_id' => $request->customer_id,
                'sub_amount' => $product->price,
                'total_additional_charges' => 0,
                'total_discount' => 0,
                'transaction_type' => 'order',
                'payment_amount' => $totalAmount,
                'additional_charges' => null,
                'payment_status' => 'pending',
                'resolution_status' => 'pending',
                'aqwire_paymentMethodCode' => null,
                'order_date' => $request->order_date,
                'transaction_date' => Carbon::now(),
            ]);

            $order = Order::create(array_merge($request->validated(), [
                'transaction_id' => $transaction->id, 
                'reference_code' => $transaction->reference_no,
                'sub_amount' => $product->price,
                'total_amount' => $totalAmount,
                'payment_method' => 'cash',
                'status' => 'pending', 
            ]));

            return [
                'transaction' => $transaction,
                'order' => $order,
            ];
        });

        return response([
            'status' => TRUE,
            'message' => 'Order successfully submitted. Please wait for approval of merchant.',
            'order' => $transaction['order'] ?? null,
        ], 201);
    }

    public function bulk_store(Request $request) {
        $items = json_decode($request->items);
        $orders = [];

        if(is_array($items)) {
            foreach ($items as $key => $item) {
                $itemArray = (array) $item; // Convert stdClass object to array
                $product = Product::where('id', $itemArray['product_id'])->first();
                $totalAmount = $this->calculateTotalAmount($product->price, $itemArray['quantity']);
                $reference_no = $this->generateReferenceNo();
            
                $transaction = Transaction::create([
                    'reference_no' => $reference_no,
                    'transaction_by_id' => $request->customer_id,
                    'sub_amount' => $product->price,
                    'total_additional_charges' => 0,
                    'total_discount' => 0,
                    'transaction_type' => 'order',
                    'payment_amount' => $totalAmount,
                    'additional_charges' => null,
                    'payment_status' => 'pending',
                    'resolution_status' => 'pending',
                    'aqwire_paymentMethodCode' => null,
                    'order_date' => Carbon::parse($itemArray['order_date'])->format('Y-m-d'),
                    'transaction_date' => Carbon::now(),
                ]);
            
                $order = Order::create([
                    'product_id' => $itemArray['product_id'],
                    'customer_id' => $request->customer_id,
                    'quantity' => $itemArray['quantity'],
                    'order_date' => $itemArray['order_date'],
                    'transaction_id' => $transaction->id, 
                    'reference_code' => $transaction->reference_no,
                    'sub_amount' => $product->price,
                    'total_amount' => $totalAmount,
                    'payment_method' => 'cash',
                    'status' => 'pending', 
                ]);
            
                array_push($orders, $order);
            }

            return response([
                'status' => TRUE,
                'message' => 'Order successfully submitted. Please wait for approval of merchant.',
                'orders' => $orders,
            ]);
        }
    }

    public function show(Request $request, $order_id) {
        $order = Order::where('id', $order_id)->with('product')->first();

        return response([
            'status' => TRUE,
            'order' => $order
        ]);
    }

    public function getUserOrders(Request $request, $user_id) {
        $orders = Order::where('customer_id', $user_id)->with('product')->get();

        return response([
            'status' => TRUE,
            'orders' => $orders
        ]);
    }

    private function calculateTotalAmount($price, $quantity) {
        return $price * $quantity;    
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OR' . rand(100000, 10000000);
    }
}
