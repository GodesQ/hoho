<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tour;

use DataTables;
class TourController extends Controller
{
    public function list(Request $request) {

        if($request->ajax()) {
            $data = Tour::latest('id');
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/tours/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
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

        return view('admin-page.tours.list-tour');
    }

    public function create(Request $request) {
        return view('admin-page.tours.create-tour');
    }

    public function store(Request $request) {
        $data = $request->except('_token');
        $tour = Tour::create($data, [
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
        ]);

        if($tour) return redirect()->route('admin.tours.edit', $tour->id)->with('success', 'Tour created successfully');
    }

    public function edit(Request $request) {
        $tour = Tour::where('id', $request->id)->firstOrFail();
        return view('admin-page.tours.edit-tour', compact('tour'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $tour = Tour::where('id', $request->id)->firstOrFail();

        $update_tour = $tour->update(array_merge($data, [
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
        ]));

        if($update_tour) return back()->with('success', 'Tour updated successfully');
    }

    public function destroy(Request $request) {
        $tour = Tour::findOrFail($request->id);

        $remove = $tour->delete();
        if($remove) {
            return response([
                'status' => true,
                'message' => 'Tour Deleted Successfully'
            ]);
        }
    }
}
