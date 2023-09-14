<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\PromoCode;

use DataTables;

class PromoCodeController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = PromoCode::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('is_need_requirement', function ($row) {
                        if($row->is_need_requirement) {
                            return '<div class="badge bg-success">Yes</div>';
                        } else {
                            return '<div class="badge bg-primary">No</div>';
                        }
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/promo_codes/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions', 'is_need_requirement'])
                    ->make(true);
        }

        return view('admin-page.promo_codes.list-promo-codes');
    }

    public function create(Request $request) {
        return view('admin-page.promo_codes.create-promo-codes');
    }

    public function store(Request $request) {
        $data = $request->except('_token');
        $promocode = PromoCode::create(array_merge($data, [
            'is_need_requirement' => $request->has('is_need_requirement'),
            'is_need_approval' => $request->has('is_need_approval')
        ]));

        if($promocode) return redirect()->route('admin.promo_codes.edit', $promocode->id)->withSuccess('PromoCode Created Successfully');
    }

    public function edit(Request $request) {
        $promocode = PromoCode::where('id', $request->id)->firstOrFail();
        return view('admin-page.promo_codes.edit-promo-codes', compact('promocode'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $promocode = PromoCode::where('id', $request->id)->firstOrFail();

        $update_promocode = $promocode->update(array_merge($data, [
            'is_need_requirement' => $request->has('is_need_requirement'),
            'is_need_approval' => $request->has('is_need_approval')
        ]));

        if($update_promocode) return back()->withSuccess('PromoCode Updated Successfully');
    }

    public function destroy(Request $request) {

    }

}
