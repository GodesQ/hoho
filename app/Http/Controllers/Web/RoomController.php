<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRequest;
use App\Http\Requests\Room\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Room;
use App\Models\Merchant;
use App\Models\ProductCategory;
use Yajra\DataTables\DataTables;

class RoomController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $rooms = Room::with('merchant');
            return DataTables::of($rooms)
                ->addIndexColumn()
                ->addColumn('merchant', function ($row) {
                    return optional($row->merchant)->name;
                })
                ->addColumn('status', function ($row) {
                    if($row->is_active) {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-warning">Inactive</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                        <a href="/admin/rooms/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                        <button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                                    </div>';
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('admin-page.rooms.list-room');
    }

    public function create(Request $request)
    {
        $merchants = Merchant::where('type', 'Hotel')->get();
        $product_categories = ProductCategory::get();
        return view('admin-page.rooms.create-room', compact('merchants', 'product_categories'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->except('image');

        $room = Room::create(
            array_merge($data, [
                'product_categories' => $request->has('product_categories') ? json_encode($request->product_categories) : null,
                'is_active' => $request->has('is_active'),
                'is_cancellable' => $request->has('is_cancellable')
            ])
        );

        if ($request->has('image')) {
            $file = $request->file('image');
            $name = Str::snake(Str::lower($request->room_name)) . '_' . time();
            $filename = $name . '.' . $file->getClientOriginalExtension();
            $path_folder = 'rooms/' . $room->id . '/';

            Storage::disk('public')->putFileAs($path_folder, $file, $filename);

            $room->update([
                'image' => $filename,
            ]);
        }

        $images = [];

        if($request->has('other_images')) {
            foreach ($request->other_images as $key => $image) {
                $name = Str::snake(Str::lower($request->name)) . '_' . 'other_image' . '_'  . time();
                $filename = $name . '.' . $image->getClientOriginalExtension();
                $image->move(public_path() . '/assets/img/rooms/' . $room->id, $filename);

                array_push($images, $filename);
            }

            $room->update([
                'other_images' => count($images) > 0 ? json_encode($images) : null,
            ]);
        }

        return redirect()->route('admin.rooms.edit', $room->id)->with('success', 'Room Added Successfully');

    }

    public function show(Request $request)
    {

    }

    public function edit(Request $request)
    {
        $merchants = Merchant::where('type', 'Hotel')->get();
        $product_categories = ProductCategory::get();

        $room = Room::where('id', $request->id)->with('merchant')->firstOrFail();

        return view('admin-page.rooms.edit-room', compact('merchants', 'product_categories', 'room'));
    }

    public function update(UpdateRequest $request)
    {
        $data = $request->except('image');
        $room = Room::where('id', $request->id)->firstOrFail();

        $room->update(
            array_merge($data, [
                'product_categories' => $request->has('product_categories') ? json_encode($request->product_categories) : null,
                'is_active' => $request->has('is_active'),
                'is_cancellable' => $request->has('is_cancellable')
            ])
        );

        if ($request->has('image')) {
            // remove old image
            $old_upload_image = public_path('assets/img/rooms/') . $room->id . '/' . $room->image;
            @unlink($old_upload_image);

            $file = $request->file('image');
            $name = Str::snake(Str::lower($request->room_name)) . '_' . time();
            $filename = $name . '.' . $file->getClientOriginalExtension();
            $path_folder = 'rooms/' . $room->id . '/';

            Storage::disk('public')->putFileAs($path_folder, $file, $filename);

            $room->update([
                'image' => $filename,
            ]);
        }

        $images = $room->other_images ? json_decode($room->other_images) : [];

        // Other Images
        if($request->has('other_images')) {
            foreach ($request->other_images as $key => $image) {
                $name = Str::snake(Str::lower($request->name)) . '_' . 'other_image' . '_'  . time();
                $filename = $name . '.' . $image->getClientOriginalExtension();
                $image->move(public_path() . '/assets/img/rooms/' . $room->id, $filename);

                is_array($images) ? array_push($images, $filename) : false;
            }

            $room->update([
                'other_images' => count($images) > 0 ? json_encode($images) : null,
            ]);
        }

        return back()->with('success', 'Room Updated Successfully');
    }

    public function destroy(Request $request, Room $room)
    {
        $room = Room::where('id', $request->id)->firstOrFail();

        $directory = public_path('assets/img/rooms/') . $room->id;
        $files = glob($directory . '/*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        // Now remove the directory
        if (is_dir($directory)) @rmdir($directory);

        $room->delete();

        return [
            'status' => TRUE,
            'message' => 'Room Deleted Successfully'
        ];
    }

    public function lookup(Request $request) {
        $type = $request->type;

        $rooms = Room::where('room_name', $request->q)->where('is_active', true)->get();

        // Look up by merchant
        if($type == 'merchant') {
            $rooms = Room::where('merchant_id', $request->q)->where('is_active', true)->get();
        }

        return response([
            'status' => TRUE,
            'rooms' => $rooms
        ]);


    }

}