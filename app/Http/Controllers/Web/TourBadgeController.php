<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

use App\Models\TourBadge;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TourBadgeController extends Controller
{   
    public function list(Request $request) {
        if($request->ajax()) {
            $data = TourBadge::get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("actions", function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/tour_badges/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="'.$row->id.'" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.tour_badges.list-tour-badge');
    }

    public function create() {
        $tours = Tour::get();
        return view('admin-page.tour_badges.create-tour-badge', compact('tours'));
    }

    public function store(Request $request) {
        $data = $request->except('badge_img');
        $badge = TourBadge::create($data);

        $path_folder = 'badges/';

        if($request->hasFile('badge_img')) {
            $file = $request->file('badge_img');
            $name = Str::snake(Str::lower($request->badge_name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs($path_folder, $file, $file_name);

            $badge->update([
                'badge_img' => $file_name
            ]);
        }

        return redirect()->route('admin.tour_badges.edit', $badge->id)->with("success", "Badge Created Successfully");
    }

    public function edit(Request $request) {
        $tour_badge = TourBadge::findOrFail($request->id);
        $tours = Tour::get();

        return view('admin-page.tour_badges.edit-tour-badge', compact("tour_badge", "tours"));
    }

    public function update(Request $request) {
        $tour_badge = TourBadge::findOrFail($request->id);
        $data = $request->except('badge_img');

        $tour_badge->update($data);
        return back()->withSuccess('Badge Updated Successfully');
        
    }

    public function destroy(Request $request) {
        $tour_badge = TourBadge::findOrFail($request->id);

        $upload_image = public_path('assets/img/badges/') . $tour_badge->badge_img;

        if($upload_image) {
             @unlink($upload_image);
        }

        $remove = $tour_badge->delete();

        if($remove) {
            return response([
                'status' => true,
                'message' => 'Tour Badge Deleted Successfully'
            ]);
        }
    }
}
