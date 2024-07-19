<?php

namespace App\Http\Controllers\Web\AdminAccounts;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MerchantAccountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Admin::whereIn('role', [
                Role::MERCHANT_RESTAURANT_ADMIN,
                Role::MERCHANT_HOTEL_ADMIN,
                Role::MERCHANT_STORE_ADMIN,
                Role::TOUR_OPERATOR_ADMIN
            ])->with('merchant');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('merchant', function ($row) {
                    return view('components.merchant', ['merchant' => $row->merchant]);
                })
                ->addColumn('is_approved', function($row) {
                    if ($row->is_approved) {
                        return '<span class="badge bg-label-success me-1">Yes</span>';
                    } else {
                        return '<span class="badge bg-label-secondary me-1">No</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                <a href="/admin/merchant-accounts/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <a href="#" id="' . $row->id . '" class="btn btn-outline-danger btn-sm remove-btn"><i class="bx bx-trash me-1"></i></a>
                            </div>';
                })
                ->rawColumns(['actions', 'is_approved'])
                ->make(true);
        }

        return view("admin-page.merchant-accounts.list-merchant-account");
    }

    public function create()
    {   
        $roles = Role::all();
        return view('admin-page.merchant-accounts.create-merchant-account', compact('roles'));
    }

    public function store(Request $request)
    {

    }

    public function edit(Request $request, $id)
    {   
        $merchant_account = Admin::where("id", $id)->with('merchant')->first();
        return view('admin-page.merchant-accounts.edit-merchant-account', compact('merchant_account'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('_token', 'username');
        $merchant_account = Admin::where("id", $id)->with('merchant')->first();

        $merchant_account->update($data);

        return back()->withSuccess('Merchant account updated successfully.');
    }

    public function destroy(Request $request, $id)
    {

    }

    public function updateMerchant(Request $request) {
        $admin = Admin::findOrFail($request->id);

        $admin->update([
            'merchant_id' => $request->merchant_id,
        ]);

        return back()->withSuccess('Merchant sync successfully.');
    }

    public function unsyncMerchant(Request $request) {
        $admin = Admin::findOrFail($request->id);
        
        $admin->update([
            'merchant_id' => null,
        ]);

        return response()->json([
            'status' => true,
            'message'=> 'Merchant successfully unsync.'
        ]);
    }
}
