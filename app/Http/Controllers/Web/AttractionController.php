<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attraction;
use App\Models\Organization;
use DataTables;

class AttractionController extends Controller
{
    public function list(Request $request) {

        if($request->ajax()) {
            $data = Attraction::latest('id');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                <a href="/admin/attractions/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                            </div>';
                })
                ->addColumn('status', function($row) {
                    if($row->status) {
                        return '<div class="badge bg-label-success">Active</div>';
                    } else {
                        return '<div class="badge bg-label-warning">InActive</div>';

                    }
                })
                ->rawColumns(['actions', 'status'])
                    ->make(true);
        }

        return view('admin-page.attractions.list-attraction');
    }

    public function create(Request $request) {
        $organizations = Organization::get();
        return view('admin-page.attractions.create-attraction', compact('organizations'));
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'organization_ids');
        $attraction = Attraction::create(array_merge($data, [
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
            'status' => $request->has('is_active'),
        ]));

        if($attraction) return redirect()->route('admin.attractions.edit', $attraction->id)->withSuccess('Attraction Created Successfully');
    }

    public function edit(Request $request) {
        $attraction = Attraction::findOrFail($request->id);
        $organizations = Organization::get();
        return view('admin-page.attractions.edit-attraction', compact('attraction', 'organizations'));
    }

    public function update(Request $request) {
        $data = $request->except("_token");
        $attraction = Attraction::findOrFail($request->id);

        $update_attraction = $attraction->update(array_merge($data, [
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
            'status' => $request->has('is_active'),
        ]));

        if($update_attraction) return back()->withSuccess('Attraction Updated Successfully');
    }

    public function destroy(Request $request) {
        $attraction = Attraction::findOrFail($request->id);

        $remove_attraction = $attraction->delete();
        if($remove_attraction) {
            return response([
                'status' => true,
                'message' => 'Attraction deleted successfully'
            ]);
        }
    }
}
