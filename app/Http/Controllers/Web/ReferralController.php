<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Referral;
use App\Models\TourReservation;

use DataTables;
use DB;

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

    public function generateCSV(Request $request) {
        $referralCodesWithCounts = TourReservation::select('referral_code', DB::raw('count(*) as total_reservations'))
                                    ->where('status', 'approved')
                                    ->OrWhere('status', 'done')
                                    ->groupBy('referral_code')
                                    ->get();

        $csvData = "Referral Code,Total Reservations\n"; // Header row

        foreach ($referralCodesWithCounts as $referral) {
            $exists = Referral::where('referral_code', $referral->referral_code)->exists();

            if ($exists) {
                $csvData .= "{$referral->referral_code},{$referral->total_reservations}\n";
            }
        }

        return response($csvData)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="referral_data.csv"');
    }

    public function getReservationsByRefCode(Request $request) {
        $reservations = TourReservation::where('referral_code', $request->code)->with('tour', 'user', 'referral')->latest()->get();

        return DataTables::of($reservations)
                ->addIndexColumn()
                ->addColumn('tour_name', function($row) {
                    return optional($row->tour)->name;
                })
                ->addColumn('reserved_user_name', function($row) {
                    return optional($row->user)->firstname . ' ' . optional($row->user)->lastname;
                })
                ->addColumn('amount', function($row) {
                    return number_format($row->amount, 2);
                })
                ->addColumn('total_commision', function($row) {
                    $commission_percentage = optional($row->referral)->commision / 100;
                    return number_format($row->amount * $commission_percentage, 2);
                })
                ->make(true);
    }
}
