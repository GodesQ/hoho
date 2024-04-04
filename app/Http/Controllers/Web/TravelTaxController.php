<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TravelTaxPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TravelTaxController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TravelTaxPayment::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('transaction_at', function ($row) {
                    return Carbon::parse($row->transaction_time)->format('F d, Y h:i A');
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'paid') {
                        return '<div class="badge bg-label-success">Paid</div>';
                    } else {
                        return '<div class="badge bg-label-failed">Unpaid</div>';
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

    public function store(Request $request)
    {

    }

    public function edit(Request $request)
    {

    }

    public function update(Request $request)
    {

    }

    public function destroy(Request $request)
    {

    }
}
