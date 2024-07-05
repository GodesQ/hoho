<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AqwireService;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

use DB;

class OrderController extends Controller
{
    private $aqwireService;

    public function __construct(AqwireService $aqwireService)
    {
        $this->aqwireService = $aqwireService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('product', 'customer');

            return DataTables::of($orders)
                ->editColumn('customer_id', function ($order) {
                    return view('components.user-contact', ['user' => $order->customer]);
                })
                ->editColumn('product_id', function ($order) {
                    return view('components.merchant-product', ['product' => $order->product]);
                })
                ->editColumn('total_amount', function ($order) {
                    return '₱ ' . number_format($order->total_amount, 2);
                })
                ->addColumn('actions', function ($row) {
                    $output = '<div class="dropdown">';

                    $output .= '<a title="View Order" href="/admin/orders/show/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-file me-1"></i></a> ';

                    if ($row->status != 'approved') {
                        $output .= '<button title="Delete Order" type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>';
                    }

                    $output .= '</div>';

                    return $output;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin-page.orders.list-order');
    }

    public function create()
    {
        return view('admin-page.orders.create-order');
    }

    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $product = Product::where('id', $request->product_id)->first();
            $totalAmount = $this->calculateTotalAmount($product->price, $request->quantity);

            $user = User::where('id', $request->customer_id)->first();
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

            $payment_request_model = $this->aqwireService->createRequestModel($transaction, $user);
            $payment_response = $this->aqwireService->pay($payment_request_model);

            $transaction->update([
                'aqwire_transactionId' => $payment_response['data']['transactionId'] ?? null,
                'payment_url' => $payment_response['paymentUrl'] ?? null,
                'payment_status' => Str::lower($payment_response['data']['status'] ?? ''),
                'payment_details' => json_encode($payment_response),
            ]);

            DB::commit();

            return redirect($payment_response['paymentUrl']);
        } catch (ErrorException $e) {
            DB::rollBack();
            return back()->with('fail', $e->getMessage());
        }
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('admin-page.orders.show-order', compact('order'));
    }

    public function edit(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        return view('admin-page.orders.edit-order', compact('order'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $order = Order::findOrFail($id);

        $product = Product::where('id', $order->product_id)->first();
        $totalAmount = $this->calculateTotalAmount($product->price, $request->quantity);

        $transaction = Transaction::where('id', $order->transaction_id)->first();

        DB::transaction(function () use ($request, $product, $totalAmount, $order, $transaction) {
            $transaction = Transaction::where('id', $order->transaction_id)->update([
                'payment_amount' => $totalAmount,
                'order_date' => $request->order_date,
            ]);

            $order = $order->update(array_merge($request->validated(), [
                'total_amount' => $totalAmount,
            ]));
        });

        return back()->withSuccess('Order updated successfully');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response([
            'status' => TRUE,
            'message' => 'Order deleted successfully'
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
