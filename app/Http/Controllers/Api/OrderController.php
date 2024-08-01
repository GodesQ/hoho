<?php

namespace App\Http\Controllers\Api;

use App\Enum\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AqwireService;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{   
    private $aqwireService;

    public function __construct(AqwireService $aqwireService) {
        $this->aqwireService = $aqwireService;
    } 

    public function store(StoreRequest $request)
    {
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

    public function bulk_store(Request $request)
    {
        try {
            DB::beginTransaction();

            $items = json_decode($request->items);
            $orders = [];

            if (json_last_error() === JSON_ERROR_NONE) {
                $user = User::where('id', $request->customer_id)->first();

                $transaction_amount = 0;
                $reference_no = $this->generateReferenceNo();

                if (is_array(($items))) {
                    foreach ($items as $item) {
                        $itemArray = (array) $item; // Convert stdClass object to array
                        $product = Product::where('id', $itemArray['product_id'])->first();
                        $totalAmount = $this->calculateTotalAmount($product->price, $itemArray['quantity']);

                        $transaction_amount += $totalAmount;
                    }
                }

                $transaction = Transaction::create([
                    'reference_no' => $reference_no,
                    'transaction_by_id' => $request->customer_id,
                    'sub_amount' => $transaction_amount,
                    'total_additional_charges' => 0,
                    'total_discount' => 0,
                    'transaction_type' => TransactionTypeEnum::ORDER,
                    'payment_amount' => $transaction_amount,
                    'additional_charges' => null,
                    'payment_status' => 'pending',
                    'resolution_status' => 'pending',
                    'aqwire_paymentMethodCode' => null,
                    'order_date' => Carbon::now(),
                    'transaction_date' => Carbon::now(),
                ]);

                if (is_array($items)) {
                    foreach ($items as $key => $item) {
                        $itemArray = (array) $item; // Convert stdClass object to array
                        $product = Product::where('id', $itemArray['product_id'])->first();
                        $totalAmount = $this->calculateTotalAmount($product->price, $itemArray['quantity']);

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

                    $payment_request_model = $this->aqwireService->createRequestModel($transaction, $user);

                    $payment_response = $this->aqwireService->pay($payment_request_model);

                    $transaction->update([
                        'aqwire_transactionId' => $payment_response['data']['transactionId'],
                        'payment_url' => $payment_response['paymentUrl'],
                        'payment_status' => Str::lower($payment_response['data']['status']),
                        'payment_details' => json_encode($payment_response),
                        'additional_charges' => null,
                    ]);

                    DB::commit();

                    return response([
                        'status' => 'paying',
                        'message' => 'Order successfully submitted. Please wait for approval of merchant.',
                        'payment_url' => $payment_response['paymentUrl'],
                    ]);
                }

            } else {
                DB::rollBack();
                throw new ErrorException("The items are not in a valid JSON format.");
            }

        } catch (ErrorException $e) {
            return response([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(Request $request, $order_id)
    {
        $order = Order::where('id', $order_id)->with('product')->first();

        return response([
            'status' => TRUE,
            'order' => $order
        ]);
    }

    public function getUserOrders(Request $request, $user_id)
    {
        $orders = Order::where('customer_id', $user_id)->with('product')->get();

        return response([
            'status' => TRUE,
            'orders' => $orders
        ]);
    }

    private function calculateTotalAmount($price, $quantity)
    {
        return $price * $quantity;
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OR' . rand(100000, 10000000);
    }
}
