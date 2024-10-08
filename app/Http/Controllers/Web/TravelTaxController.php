<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelTax\PaymentRequest;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use App\Services\LoggerService;
use App\Services\TravelTaxService;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables;

class TravelTaxController extends Controller
{
    public $travelTaxService;

    public function __construct(TravelTaxService $travelTaxService)
    {
        $this->travelTaxService = $travelTaxService; 
    }
 
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TravelTaxPayment::query();
            $admin = Auth::guard("admin")->user();
            
            if(in_array($admin->role, [Role::MERCHANT_HOTEL_ADMIN, Role::MERCHANT_RESTAURANT_ADMIN, Role::MERCHANT_STORE_ADMIN, Role::TOUR_OPERATOR_ADMIN])) {
                $data->where("created_by", $admin->id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("total_passengers", function ($row) {
                    return $row->passengers->count();
                })
                ->addColumn('transaction_at', function ($row) {
                    return Carbon::parse($row->transaction_time)->format('M d, Y h:i A');
                })
                ->editColumn('total_amount', function ($row) {
                    return '₱ ' . number_format($row->total_amount, 2);
                })                                                                                                                                                                                                                                                                                          
                ->editColumn('status', function ($row) {
                    if ($row->status == 'paid') {
                        return '<div class="badge bg-label-success">Paid</div>';
                    } else {
                        return '<div class="badge bg-label-danger">Unpaid</div>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $output = '';

                    $output .= '<div class="dropdown">';
                    $output .= '<a href="' . route('admin.travel_taxes.edit', $row->id) . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>';
                                
                    if($row->status != 'paid') {
                        $output .= '<button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>';
                    }

                    $output .= '</div>';

                    return $output;
                })
                ->filter(function ($query) use ($request) {
                    $status_query = $request->query('status');
                    $transaction_date_query = $request->query('transaction_date');
                    $search_query = $request->query('search_value');

                    if ($search_query) {
                        $query->where('transaction_number', $search_query)
                            ->orWhere('reference_number', 'like', '%' . $search_query . '%');
                    }

                    if ($status_query) {
                        $query->where('status', $status_query);
                    }

                    if ($transaction_date_query) {

                        $start_date = '';
                        $end_date = '';

                        // Check if the query contains the word "to"
                        if (strpos($transaction_date_query, 'to') !== false) {
                            $dates = explode(' to ', $transaction_date_query);
                            $start_date = $dates[0] ?? '';
                            $end_date = $dates[1] ?? '';

                            $query->where(function ($query) use ($start_date, $end_date) {
                                $query->whereDate('transaction_time', '>=', $start_date)
                                ->whereDate('transaction_time', '<=', $end_date);
                            });

                        } else {
                            $start_date = $transaction_date_query;
                            $end_date = '';

                            $query->whereDate('transaction_time', $start_date);
                        }
                    }
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('admin-page.travel-taxes.list-travel-tax');
    }

    public function create(Request $request)
    {
        return view('admin-page.travel-taxes.create-travel-tax');
    }

    public function store(PaymentRequest $request)
    {
        try {
            $travelTax = $this->travelTaxService->createTravelTax($request);
            return redirect($travelTax['url']);

        } catch (ErrorException $e) {
            return back()->with('fail', $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        $travel_tax = TravelTaxPayment::where('id', $id)->with('passengers')->firstOrFail();
        return view('admin-page.travel-taxes.edit-travel-tax', compact('travel_tax'));
    }

    public function update(Request $request)
    {
        
    }

    public function destroy(Request $request, $id)
    {   
        try {
            $travel_tax_payment = TravelTaxPayment::where('id', $id)->with('transaction')->first();
            
            $copy = $travel_tax_payment->replicate();

            if($travel_tax_payment->status != 'paid') {
                $travel_tax_payment->transaction()->delete();
                LoggerService::log('delete', TravelTaxPayment::class, $copy);
            }

            return response()->json([
                'status'=> true,
                'message'=> 'Travel Tax Payment Successfully Deleted',
            ]);


        } catch (ErrorException $e) {
            return response()->json([
                'status' => false,
                'message'=> $e->getMessage()
            ], 400);
        }   
    }

    public function getPassenger(Request $request, $passenger_id)
    {
        $passenger = TravelTaxPassenger::where('id', $passenger_id)->first();

        return response([
            'status' => TRUE,
            'passenger' => $passenger,
        ]);
    }

    public function updatePassenger(Request $request)
    {
        $passenger = TravelTaxPassenger::where('id', $request->id)->first();
        $passenger->update($request->all());

        return back()->withSuccess('Passenger updated successfully.');
    }
}
