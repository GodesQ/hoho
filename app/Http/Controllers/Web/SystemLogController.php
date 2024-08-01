<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SystemLogController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()) {
            $logs = SystemLog::query();
            return DataTables::of($logs)
                        ->addIndexColumn()
                        ->addColumn("user", function ($row) {
                            return $row->admin->firstname . " ". $row->admin->lastname;
                        })
                        ->make(true);
        }

        return view("admin-page.system_logs.list-system-log");
    }

    public function show(Request $request) {

    }

    public function destroy(Request $request) {

    }
}
