<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

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
        $announcement = Announcement::create(
            array_merge(
                $request->all(),
                [
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
        $announcement = Announcement::findOrFail($request->id);
        $announcement->update(array_merge(
            $request->all(),
            [
                'is_active' => $request->has('is_active'),
                'is_important' => $request->has('is_important')
            ]
        ));

        return back()->with('success', 'Announcement Updated Successfully');
    }

    public function destroy(Request $request)
    {
        $announcement = Announcement::findOrFail($request->id);
        $announcement->delete();

        return response(['status' => TRUE, 'message' => 'Announcement Successfully Deleted']);
    }
}