<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Referral;

use DataTables;

class ReferralController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Referral::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/referrals/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.referrals.list-referral');
    }

    public function create(Request $request) {
        return view('admin-page.referrals.create-referral');
    }
}
