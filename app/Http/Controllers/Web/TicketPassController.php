<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\TicketPass;

use Yajra\DataTables\DataTables;

class TicketPassController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = TicketPass::get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row) {
                        return '<div class="dropdown">
                        <a href="/admin/ticket_passes/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                        <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                    </div>';
                    })
                    ->rawColumns(['actions', 'organizations'])
                    ->make(true);
        }

        return view('admin-page.ticket_passes.list-ticket-pass');
    }

    public function create(Request $request) {
        return view('admin-page.ticket_passes.create-ticket-pass');
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'ticket_image');
        $ticket_pass = TicketPass::create($data);

        $path_folder = 'ticket_passes/';

        if($request->hasFile('ticket_image')) {
            $file = $request->file('ticket_image');
            $name = Str::snake(Str::lower($request->name));
            $file_name = $name . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs($path_folder, $file, $file_name);

            $ticket_pass->update([
                'ticket_image' => $file_name
            ]);
        }

        if($ticket_pass) {
            return redirect()->route('admin.ticket_passes.edit', $ticket_pass->id)->withSuccess('Ticket Pass Created Successfully');
        }
    }

    public function edit(Request $request) {
        $ticket_pass = TicketPass::where('id', $request->id)->firstOrFail();

        return view('admin-page.ticket_passes.edit-ticket-pass', compact('ticket_pass'));
    }

    public function update(Request $request) {
        $data = $request->except('_token', 'ticket_image');
        $ticket_pass = TicketPass::where('id', $request->id)->firstOrFail();

        $path_folder = 'ticket_passes/';

        $file_name = null;

        if($request->hasFile('ticket_image')) {
            $file = $request->file('ticket_image');
            $name = Str::snake(Str::lower($request->name));
            $old_upload_image = public_path('/assets/img/ticket_passes') . $ticket_pass->ticket_image;
            @unlink($old_upload_image);

            $file_name = $name . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs($path_folder, $file, $file_name);

            $ticket_pass->update([
                'ticket_image' => $file_name
            ]);
        } else {
            $file_name = $ticket_pass->ticket_image;
        }

        $update_ticket_pass = $ticket_pass->update(array_merge($data, [
            'ticket_image' => $file_name
        ]));

        if($update_ticket_pass) {
            return back()->withSuccess('Ticket Pass Updated Successfully');
        }
    }

    public function destroy(Request $request) {
        
    }
}
