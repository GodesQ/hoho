<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class AnnouncementController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {
            $data = Announcement::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("status", function ($row) {
                    if ($row->is_active) {
                        return '<div class="badge bg-label-success">Active</div>';
                    } else {
                        return '<div class="badge bg-label-warning">In Active</div>';
                    }
                })
                ->addColumn("actions", function ($row) {
                    return '<div class="dropdown">
                                <a href="/admin/announcements/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <a href="javascript:void(0);" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                            </div>';
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('admin-page.announcements.list-announcement');
    }

    public function create(Request $request)
    {
        return view('admin-page.announcements.create-announcement');
    }

    public function store(Request $request)
    {   
        $data = $request->except('announcement_image');

        if($request->hasFile('announcement_image')) {
            $file = $request->file('announcement_image');
            $outputString = str_replace(array(":", ";"), " ", $request->name);

            $name = Str::snake(Str::lower($outputString));
            $featured_file_name = $name . '.' . $file->getClientOriginalExtension();

            $file->move(public_path() . '/assets/img/announcements', $featured_file_name);
        } else {
            $featured_file_name = null;
        }
        
        $announcement = Announcement::create(
            array_merge($data, [
                    'announcement_image' => $featured_file_name,
                    'is_active' => $request->has('is_active'),
                    'is_important' => $request->has('is_important')
                ]
            )
        );

        return redirect()->route('admin.announcements.edit', $announcement->id)->with('success', 'New Announcement Added Successfully');
    }

    public function edit(Request $request)
    {
        $announcement = Announcement::findOrFail($request->id);
        return view('admin-page.announcements.edit-announcement', compact('announcement'));
    }

    public function update(Request $request)
    {   
        $data = $request->except('announcement_image');
        $announcement = Announcement::findOrFail($request->id);

        $announcement->update(array_merge(
            $data,
            [
                'is_active' => $request->has('is_active'),
                'is_important' => $request->has('is_important')
            ]
        ));

        $announcement_file_image = $announcement->announcement_image;

        if($request->hasFile('announcement_image')) {
            $file = $request->file('announcement_image');
            $outputString = str_replace(array(":", ";"), " ", $request->name);

            $name = Str::snake(Str::lower($outputString));
            $announcement_file_image = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/announcements/') . $announcement->announcement_image;
            // Remove old image
            if($old_upload_image) @unlink($old_upload_image);
            $file->move(public_path() . '/assets/img/announcements', $announcement_file_image);
        }

        $announcement->update([
            'announcement_image' => $announcement_file_image
        ]);

        return back()->with('success', 'Announcement Updated Successfully');
    }

    public function destroy(Request $request)
    {
        $announcement = Announcement::findOrFail($request->id);
        $announcement->delete();

        return response(['status' => TRUE, 'message' => 'Announcement Successfully Deleted']);
    }
}