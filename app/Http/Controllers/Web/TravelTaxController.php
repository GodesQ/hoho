<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelTax\PaymentRequest;
use App\Models\Transaction;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use App\Services\TravelTaxService;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TravelTaxController extends Controller
{   
    public $travelTaxService;

    public function __construct(TravelTaxService $travelTaxService) {
        $this->travelTaxService = $travelTaxService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TravelTaxPayment::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('transaction_at', function ($row) {
                    return Carbon::parse($row->transaction_time)->format('F d, Y h:i A');
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
                    return '<div class="dropdown">
                                <a href="' . route('admin.travel_taxes.edit', $row->id) . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                            </div>';
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

    public function destroy(Request $request)
    {

    }

    public function getPassenger(Request $request, $passenger_id) {
        $passenger = TravelTaxPassenger::where('id', $passenger_id)->first();

        return response([
            'status' => TRUE, 
            'passenger' => $passenger,
        ]);
    }

    public function updatePassenger(Request $request) {
        $passenger = TravelTaxPassenger::where('id', $request->id)->first();
        $passenger->update($request->all());
        
        return back()->withSuccess('Passenger updated successfully.');
    }

    private function getHMACSignatureHash($text, $secret_key)
    {
        $key = $secret_key;
        $message = $text;

        $hex = hash_hmac('sha256', $message, $key);
        $bin = hex2bin($hex);

        return base64_encode($bin);
    }

    private function getLiveHMACSignatureHash($text, $key)
    {
        $keyBytes = utf8_encode($key);
        $textBytes = utf8_encode($text);

        $hashBytes = hash_hmac('sha256', $textBytes, $keyBytes, true);

        $base64Hash = base64_encode($hashBytes);
        $base64Hash = str_replace(['+', '/'], ['-', '_'], $base64Hash);

        return $base64Hash;
    }

    private function computeTotalAmount($amount, $processing_fee, $discount)
    {
        return ($amount + $processing_fee) - $discount;
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OTRX' . rand(100000, 10000000);
    }

    private function generateTransactionNumber()
    {
        return 'TN' . date('Ym') . rand(100000, 10000000);
    }
}
