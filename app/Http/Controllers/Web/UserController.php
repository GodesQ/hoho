<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Interest;

use DataTables;

class UserController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest('created_at');
            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('username', function($row) {
                //     return '<a href="/admin/users/edit/' .$row->id. '">'. $row->username .'</a>';
                // })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<span class="badge bg-label-success me-1">Active</span>';
                    } else {
                        return '<span class="badge bg-label-warning me-1">In Active</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                    <a href="/admin/users/edit/' .
                        $row->id .
                        '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm" id="' .
                        $row->id .
                        '"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                })
                ->rawColumns(['status', 'username', 'actions'])
                ->make(true);
        }

        return view('admin-page.users.list-user');
    }

    public function lookup(Request $request)
    {
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

    public function create(Request $request)
    {
        $interests = Interest::get();
        return view('admin-page.users.create-user', compact('interests'));
    }

    public function store(Request $request)
    {
        $account_uid = $this->generateRandomUuid();
        $user = User::create(
            array_merge($request->all(), [
                'account_uid' => $account_uid,
                'password' => Hash::make($request->password),
                'interests' => $request->has('interest_ids') ? json_encode($request->interest_ids) : null,
            ]),
        );

        if ($user) {
            return redirect()
                ->route('admin.users.edit', $user->id)
                ->withSuccess('User created successfully');
        }
    }

    public function edit(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $interests = Interest::get();
        return view('admin-page.users.edit-user', compact('user', 'interests'));
    }

    public function update(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        $update_user = $user->update(
            array_merge($request->all(), [
                'interest_ids' => $request->has('interest_ids') ? json_encode($request->interest_ids) : null,
            ]),
        );

        if ($update_user) {
            return back()->withSuccess('User updated successfully');
        }
    }

    public function destroy(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        if ($user) {
            $delete_user = $user->delete();
            if ($delete_user) {
                return response()->json(
                    [
                        'status' => true,
                        'message' => 'User deleted successfully',
                    ],
                    200,
                );
            }
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User not found',
                ],
                200,
            );
        }
    }

    private function generateRandomUuid()
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // Version 4 (random)
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // Variant (RFC 4122)

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return $uuid;
    }

    public function updateUserContacts(Request $request)
    {
        $jsonData = '[]';

        // $userData = json_decode($jsonData, true);
        // // dd($userData);

        // foreach ($userData as $user) {
        //     $email = $user['Email'];
        //     $account_uid = $user['AccountUID'];
        //     $mobileNumber = $user['MobileNumber'];

        //     if ($mobileNumber !== 'NULL') {
        //         $contactNo = $mobileNumber['countryCode'] . ' ' . $mobileNumber['number'];
        //     } else {
        //         $contactNo = null;
        //     }

        //     $birthdate = null;
        //     if ($user['DateOfBirth'] !== '-infinity') {
        //         // Extract only the date part and format it as 'Y-m-d'
        //         $birthdate = date('Y-m-d', strtotime($user['DateOfBirth']));
        //     }

        //     // dd($birthdate);

        //     $userData = [
        //         'account_uid' => $user['AccountUID'],
        //         'username' => $user['UserName'],
        //         'email' => $email,
        //         'password' => Hash::make('Test123!'),
        //         'firstname' => $user['GivenName'],
        //         'middlename' => $user['MiddleName'],
        //         'lastname' => $user['LastName'],
        //         'gender' => $user['Gender'],
        //         'birthdate' => $birthdate,
        //         'contact_no' => $contactNo,
        //         'is_old_user' => true,
        //         'is_verify' => true,
        //     ];

        //     $existingUser = User::where('email', $email)->first();

        //     if ($existingUser) {
        //         // Email exists, update the record
        //         $existingUser->update($userData);
        //     } else {
        //         // Email doesn't exist, insert a new record
        //         User::create($userData);
        //     }
        // }

        $users = User::select('id', 'contact_no', 'birthdate', 'firstname', 'lastname', 'middlename')->get();

        foreach ($users as $user) {
            // Remove spaces from contact_no if it has a value
            if ($user->contact_no) {
                $user->contact_no = str_replace(' ', '', $user->contact_no);
            }
        
            // Calculate age using birthdate
            if ($user->birthdate) {
                $user->age = now()->diff($user->birthdate)->y;
            }
        
            // Save the updated user model
            $user->save();
        }

        return 'User updated successfully';

        
    }
}
