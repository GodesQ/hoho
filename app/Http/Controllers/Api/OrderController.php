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

    public function show(Request $request, $order_id) {
        $order = Order::findOrFail($order_id);

        return response([
            'status' => TRUE,
            'order' => $order
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
