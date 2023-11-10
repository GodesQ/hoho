<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

use App\Models\Admin;
use App\Models\Role;

use Yajra\DataTables\DataTables;

class AdminController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Admin::get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/admins/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }
        return view('admin-page.admins.list-admin');
    }

    public function create(Request $request) {
        $roles = Role::get();
        return view('admin-page.admins.create-admin', compact('roles'));
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'password');

        $admin = Admin::create(array_merge($data, [
            'password' => Hash::make($request->password)
        ]));

        if($admin) return redirect()->route('admin.admins.edit', $admin->id)->withSuccess('Admin created successfully');
    }

    public function edit(Request $request) {
        $roles = Role::get();
        $admin = Admin::where('id', $request->id)->first();

        return view('admin-page.admins.edit-admin', compact('admin', 'roles'));
    }

    public function update(Request $request) {
        $data = $request->except('_token');
        $admin = Admin::where('id', $request->id)->first();

        $image_name = $admin->username;

        if($request->hasFile('admin_profile')) {
            $old_upload_image = public_path('/assets/img/admin_profiles') . $admin->admin_profile;
            @unlink($old_upload_image);
            $file = $request->file('admin_profile');
            $file_name = Str::snake(Str::lower($image_name)) . '.' . $file->getClientOriginalExtension();
            $save_file = $file->move(public_path() . '/assets/img/admin_profiles', $file_name);
        } else {
            $file_name = $admin->admin_profile;
        }

        $update_admin = $admin->update(array_merge($data, [
            'admin_profile' => $file_name
        ]));

        return back()->withSuccess('Admin updated successfully');
    }

    public function destroy(Request $request) {

    }

    public function sendMessageWithSemaphore() {
        $client = new Client();
    
        $parameters = [
            'apikey' => '2e9288e75f56bb100bd53d018142b2e7',
            'number' => '+639633987953',
            'message' => 'I just sent my first message with Semaphore',
            'sendername' => 'PEP',
        ];
    
        try {
            $response = $client->request('POST', 'https://semaphore.co/api/v4/messages', [
                'form_params' => $parameters
            ]);
    
            $output = $response->getBody()->getContents();
    
            // Show the server response
            echo $output;
        } catch (\Exception $e) {
            // Handle any exceptions or errors here
            echo "Error: " . $e->getMessage();
        }
    }
}
