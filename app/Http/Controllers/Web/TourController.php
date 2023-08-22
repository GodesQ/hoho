<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Tour;
use App\Models\Attraction;

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

    public function getDiyTours(Request $request) {
        $diy_tours = Tour::where('type', 'DIY Tour')->get();
        return response($diy_tours);
    }

    public function getGuidedTours(Request $request) {
        $guided_tours = Tour::where('type', 'Guided Tour')->get();
        return response($guided_tours);
    }

    public function create(Request $request) {
        $attractions = Attraction::get();
        return view('admin-page.tours.create-tour', compact('attractions'));
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
        $attractions = Attraction::get();
        $tour = Tour::where('id', $request->id)->firstOrFail();
        return view('admin-page.tours.edit-tour', compact('tour', 'attractions'));
    }

    public function update(Request $request) {
        $data = $request->except('_token', 'featured_image');
        $tour = Tour::where('id', $request->id)->firstOrFail();

        $update_tour = $tour->update(array_merge($data, [
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
        ]));

        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $outputString = str_replace(array(":", ";"), " ", $request->name);

            $name = Str::snake(Str::lower($outputString));
            $featured_file_name = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/tours/') . $tour->id . '/' . $tour->featured_image;
            if($old_upload_image) {
                $remove_image = @unlink($old_upload_image);
            }
            $save_file = $file->move(public_path() . '/assets/img/tours/' . $tour->id, $featured_file_name);
        } else {
            $featured_file_name = $tour->featured_image;
        }

        $update_tour = $tour->update([
            'featured_image' => $featured_file_name
        ]);

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
