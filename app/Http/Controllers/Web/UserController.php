<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Mail\EmailVerification;
use App\Models\Admin;
use App\Models\Interest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $users = $this->userService->getUsersList($request);
            return $this->userService->generateUsersDataTable($users);
        }

        return view('admin-page.users.list-user');
    }

    public function lookup(Request $request)
    {
        $query = $request->input('q'); // Get the user input
        $formattedUsers = $this->userService->lookupUsers($query);
        return response()->json($formattedUsers);
    }

    public function create(Request $request)
    {
        $interests = Interest::get();
        return view('admin-page.users.create-user', compact('interests'));
    }

    public function store(StoreRequest $request)
    {   
        $user = $this->userService->createUser($request);

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

    public function update(UpdateRequest $request)
    {
        $this->userService->updateUser($request);
        return back()->withSuccess('User updated successfully');
    }

    public function destroy(Request $request)
    {
        $user = User::find($request->id);
        
        if (!$user) {
            return response([
                'status' => false,
                'message' => 'User Not Found',
            ], 404);
        }

        $delete_user = $this->userService->deleteUser($user);

        return $delete_user
            ? response([
                'status' => true,
                'message' => 'User deleted successfully',
            ], 200)
            : response([
                'status' => false,
                'message' => 'User failed to delete',
            ], 500);
    }


    public function resend_email(Request $request)
    {
        # details for sending email to worker
        $details = [
            'title' => 'Verification email from HOHO',
            'email' => $request->email,
            'username' => $request->username,
        ];

        // SEND EMAIL FOR VERIFICATION
        Mail::to($request->email)->send(new EmailVerification($details));


        return back()->withSuccess('Resend Verification Email');
    }

    public function updateUserContacts(Request $request)
    {
        $userData = User::select('contact_no', 'email', 'id', 'countryCode', 'isoCode')->get();
        
        foreach ($userData as $key => $user) {
            $phoneNumber = $user->contact_no;
            if($phoneNumber) {
                $countryCode = substr($phoneNumber, 0, 2);
                $number = substr($phoneNumber, 2);

                if($countryCode == '63') {
                    $user->update([
                        'countryCode' => $countryCode,
                        'isoCode' => 'PH',
                        'contact_no' => $number
                    ]);
                } else {
                    $user->update([
                        'countryCode' => $user->countryCode,
                        'isoCode' => $user->isoCode,
                        'contact_no' => $phoneNumber
                    ]);
                }
            }
        }

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

        // $users = User::select('id', 'contact_no', 'birthdate', 'firstname', 'lastname', 'middlename')
        //     ->where('firstname', 'NULL')
        //     ->where('lastname', 'NULL')
        //     ->where('middlename', 'NULL')
        //     ->get();

        // foreach ($users as $user) {
        //     // Remove spaces from contact_no if it has a value
        //     if ($user->contact_no) {
        //         $user->contact_no = str_replace(' ', '', $user->contact_no);
        //     }

        //     // Calculate age using birthdate
        //     if ($user->birthdate) {
        //         $user->age = now()->diff($user->birthdate)->y;
        //     }

        //     $user->firstname = null;
        //     $user->lastname = null;
        //     $user->middlename = null;

        //     // Save the updated user model
        //     $user->save();
        // }

        return 'User updated successfully';
    }

    
}