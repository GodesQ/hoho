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
                    // ->addColumn('username', function($row) {
                    //     return '<a href="/admin/users/edit/' .$row->id. '">'. $row->username .'</a>';
                    // })
                    ->addColumn('status', function($row) {
                        if($row->status == 'active') {
                            return '<span class="badge bg-label-success me-1">Active</span>';
                        } else {
                            return '<span class="badge bg-label-warning me-1">In Active</span>';
                        }
                    })
                    ->addColumn('actions', function($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/users/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm"><i class="bx bx-trash me-1"></i></a>
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
       // $data = $request->except
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
