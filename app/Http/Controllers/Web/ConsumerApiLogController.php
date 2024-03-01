<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ConsumerApiLog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ConsumerApiLogController extends Controller
{
    public function index(Request $request) {

        if($request->ajax()) {
            $data = ConsumerApiLog::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('consumer', function ($row) {
                    return $row->consumer->consumer_name;
                })
                ->addColumn('request_timestamp', function ($row) {
                    return $row->request_timestamp->format('F d, Y H:i:s');
                })
                ->addColumn("actions", function ($row) {
                    return '<div class="dropdown">
                                <a href="/admin/consumer-api-logs/show/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-file me-1"></i></a>
                            </div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin-page.api_logs.list-api-logs');
    }

    public function show(Request $request) {
        
    }
}
