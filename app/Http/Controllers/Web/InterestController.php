<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Interest;
use Yajra\DataTables\DataTables;


class InterestController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Interest::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/interests/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <button type="button" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.interests.list-interest');
    }

    public function create(Request $request) {
        return view('admin-page.interests.create-interest');
    }

    public function store(Request $request) {

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $file_name = Str::snake(Str::lower($request->name)) . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/interests', $file_name);
        } else {
            $file_name = null;
        }

        $interest = Interest::create([
            'name' => $request->name,
            'image' => $file_name
        ]);

        if($interest) return redirect()->route('admin.interests.edit', $interest->id)->withSuccess('Interest Created Successfully');

        abort(404);
    }

    public function edit(Request $request) {
        $interest = Interest::where('id', $request->id)->firstOrFail();
        return view('admin-page.interests.edit-interest', compact('interest'));
    }

    public function update(Request $request) {
        $interest = Interest::where('id', $request->id)->firstOrFail();
        $data = $request->all();

        $update_interest = $interest->update($data);

        if($update_interest) return back()->withSuccess('Referral updated successfully');
    }

    public function destroy(Request $request) {
        $interests = Interest::findOrFail($request->id);

        $upload_image = public_path('assets/img/interests/') . $interests->id . '/' . $interests->featured_image;

        if($upload_image) {
             @unlink($upload_image);
        }

        $remove = $interests->delete();

        if($remove) {
            return response([
                'status' => true,
                'message' => 'Interest Deleted Successfully'
            ]);
        }
    }
}
