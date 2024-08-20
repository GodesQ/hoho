<?php 

namespace App\Services;
use App\Enum\LoggerActionEnum;
use App\Mail\OrderPaymentRequestMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class OrderService {
    private $aqwireService;

    public function __construct(AqwireService $aqwireService) {
        $this->aqwireService = $aqwireService;
    }

    public function create($request, $data) {
        try {
            DB::beginTransaction();

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
                'order_date' => $request->order_date,
                'transaction_date' => Carbon::now(),
            ]);

            $order = Order::create(array_merge($data, [
                'transaction_id' => $transaction->id,
                'reference_code' => $transaction->reference_no,
                'sub_amount' => $product->price,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]));

            LoggerService::log(LoggerActionEnum::CREATE, Order::class, ['added' => $request->all()]);

            $payment_request_model = $this->aqwireService->createRequestModel($transaction, $user);

            $payment_response = $this->aqwireService->pay($payment_request_model);

            $transaction->update([
                'aqwire_transactionId' => $payment_response['data']['transactionId'] ?? null,
                'payment_url' => $payment_response['paymentUrl'] ?? null,
                'payment_status' => Str::lower($payment_response['data']['status'] ?? ''),
                'payment_details' => json_encode($payment_response),
            ]);

            LoggerService::log(LoggerActionEnum::CREATE, Transaction::class, ['added' => $transaction->toArray()]);

            $details = [
                'product_name' => $product->name,
                'quantity' => $order->quantity,
                'customer' => $order->customer->firstname . ' ' . $order->customer->lastname,
                'payment_expiration' => $payment_response['data']['expiresAt'],
                'reference_no' => $order->reference_code,
                'sub_amount' => $order->sub_amount,
                'total_amount' => $order->total_amount,
                'payment_url' => $payment_response['paymentUrl']
            ];

            $receiver = config('app.env') === "production" ? $order->customer->email : config('mail.test_receiver');
            Mail::to($receiver)->send(new OrderPaymentRequestMail($details));

            DB::commit();

            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($request, $id, $data) {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);

            $product = Product::where('id', $order->product_id)->first();
            $totalAmount = $this->calculateTotalAmount($product->price, $request->quantity);

            $transaction = Transaction::where('id', $order->transaction_id)->first();


            $transaction = $transaction->update([
                'payment_amount' => $totalAmount,
                'order_date' => $request->order_date,
            ]);

            $order = $order->update(array_merge($data, [
                'total_amount' => $totalAmount,
            ]));

            DB::commit();

            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function generateDataTable($request, $orders) {
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
                ->editColumn('status', function ($order) {
                    if($order->status === 'pending') {
                        return "<div class='badge bg-label-warning'>Pending</div>";
                    } elseif ($order->status === 'processing') {
                        return "<div class='badge bg-label-warning'>Processing</div>";
                    } elseif ($order->status === 'received') {
                        return "<div class='badge bg-label-success'>Received</div>";
                    } elseif ($order->status === 'cancelled') {
                        return "<div class='badge bg-label-danger'>Cancelled</div>";
                    }
                })
                ->addColumn('actions', function ($row) {
                    $output = '<div class="dropdown">';

                    $output .= '<a title="View Order" href="'. route('admin.orders.show', $row->id) .'" class="btn btn-outline-primary btn-sm">
                                    <i class="bx bx-file me-1"></i>
                                </a> ';

                    if ($row->status != 'approved') {
                        $output .= '<button title="Delete Order" type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm">
                                        <i class="bx bx-trash me-1"></i>
                                    </button>';
                    }

                    $output .= '</div>';

                    return $output;
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
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