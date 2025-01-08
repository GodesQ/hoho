<?php

namespace App\Http\Controllers;

use App\Models\TravelTaxAPILog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TravelTaxLogController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax())
        {
            $data = TravelTaxAPILog::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('ar_number', function ($row) {
                    return $row->travel_tax->ar_number ?? 'N/A';
                })
                ->editColumn('date_of_submission', function ($row) {
                    return Carbon::parse($row->date_of_submission)->format('M d, Y h:i A');
                })
                ->editColumn('status_code', function ($row) {
                    $statuscode = (int) $row->status_code;
                    if ($row->status_code >= 200 && $row->status_code < 300)
                    {
                        return "<div class='badge bg-success'>$row->status_code</div>";
                    } else
                    {
                        return "<div class='badge bg-danger'>$row->status_code</div>";

                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                <a href="/admin/travel-tax-logs/show/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-file me-1"></i></a>
                            </div>';
                })
                ->rawColumns(['actions', 'status_code'])
                ->make(true);
        }

        return view('admin-page.travel_tax_logs.list-travel-tax-logs');
    }

    public function show(Request $request, $id)
    {
        $travel_tax_log = TravelTaxAPILog::findOrFail($id);

        return view('admin-page.travel_tax_logs.show-travel-tax-log', compact('travel_tax_log'));
    }
}
