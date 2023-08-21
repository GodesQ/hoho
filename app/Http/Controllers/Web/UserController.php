<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use DataTables;

class UserController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = User::latest('created_at');
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('username', function($row) {
                        return '<a href="/admin/users/edit/' .$row->id. '">'. $row->username .'</a>';
                    })
                    ->addColumn('status', function($row) {
                        if($row->status == 'active') {
                            return '<span class="badge bg-label-success me-1">Active</span>';
                        } else {
                            return '<span class="badge bg-label-warning me-1">In Active</span>';
                        }
                    })
                    ->addColumn('actions', function($row) {
                        return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/users/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['status', 'username', 'actions'])
                    ->make(true);
        }

        return view('admin-page.users.list-user');
    }

    public function lookup(Request $request) {
        $query = $request->input('q'); // Get the user input

        // Use the input to filter users
        $users = User::where('email', 'LIKE', "%$query%")
                     ->select('id', 'email')
                     ->get();

        $formattedUsers = [];

        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->id,
                'text' => $user->email,
            ];
        }
        return response()->json($formattedUsers);
    }

    public function create(Request $request) {
        return view('admin-page.users.create-user');
    }

    public function store(Request $request) {
        dd($request->all());
    }

    public function edit(Request $request) {
        return view('admin-page.users.edit-user');
    }

    public function update(Request $request) {
        dd($request->all());
    }

    public function destroy(Request $request) {

    }
}
