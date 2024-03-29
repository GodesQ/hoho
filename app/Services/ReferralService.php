<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Referral;

use Yajra\DataTables\DataTables;

class ReferralService
{
    public function RetrieveAllReferralsList(Request $request)
    {
        $data = Referral::latest();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('actions', function ($row) {
                return '<div class="dropdown">
                                    <a href="/admin/referrals/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function RetrieveMerchantReferralsList(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        // dd($merchant_data->merchant_id);

        $data = Referral::where('merchant_id', $admin->merchant_id ?? 0)->latest();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('actions', function ($row) {
                return '<div class="dropdown">
                                    <a href="/admin/referrals/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}

?>