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
                                    <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
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

    public function store(Request $request) {
        $data = $request->all();

        $referral = Referral::create($data);

        if($referral) return redirect()->route('admin.referrals.edit', $referral->id)->withSuccess('Referral created successfully');
    }

    public function edit(Request $request) {
        $referral = Referral::where('id', $request->id)->firstOrFail();

        return view('admin-page.referrals.edit-referral', compact('referral'));
    }

    public function update(Request $request) {
        $referral = Referral::where('id', $request->id)->firstOrFail();
        $data = $request->all();

        $update_referral = $referral->update($data);

        if($update_referral) return back()->withSuccess('Referral updated successfully');
    }

    public function destroy(Request $request) {
        $referral = Referral::where('id', $request->id)->firstOrFail();

        $delete = $referral->delete();

        if($delete) return response([
            'status' => TRUE,
            'message' => 'Referral deleted successfully'
        ]);

    }
}
