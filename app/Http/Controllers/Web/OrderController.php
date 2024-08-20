<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Models\Order;
use App\Models\Role;
use App\Services\AqwireService;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(AqwireService $aqwireService, OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Retrieves a list of orders and returns it as a DataTables response.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::guard('admin')->user();
            $orders = Order::query();

            $orders = $orders->with('product', 'customer');

            if($user->role === Role::MERCHANT_STORE_ADMIN || $user->role === Role::MERCHANT_STORE_EMPLOYEE) {
                $orders = $orders->whereHas('product', function($q) use ($user) {
                    $q->where('merchant_id', $user->merchant_id);
                });
            }

            return $this->orderService->generateDataTable($request, $orders);
        }

        return view('admin-page.orders.list-order');
    }

    /**
     * Create order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin-page.orders.create-order');
    }

    /**
     * Store order
     * @param \App\Http\Requests\Order\StoreRequest $request
     * @return mixed
     */
    public function store(StoreRequest $request)
    {   
        try {
            $data = $request->validated();
            $order = $this->orderService->create($request, $data);

            return redirect()->route('admin.orders.show', $order->id)->withSuccess('Order successfully submitted.');
 
        } catch (Exception $e) {
            return redirect()->back()->with('fail', $e->getMessage());
        }
    }

    /**
     * Show order
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('admin-page.orders.show-order', compact('order'));
    }

    /**
     * Edit order
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        return view('admin-page.orders.edit-order', compact('order'));
    }

    /**
     * Update order
     * @param \App\Http\Requests\Order\UpdateRequest $request
     * @param mixed $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {   
        try {
            $data = $request->validated();
            $this->orderService->update($request, $id, $data);

            return back()->withSuccess('Order updated successfully');   
        } catch (Exception $e) {
            return back()->with('fail', $e->getMessage());
        }
    }

    /**
     * Delete order
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response([
            'status' => TRUE,
            'message' => 'Order deleted successfully'
        ]);
    }
}
