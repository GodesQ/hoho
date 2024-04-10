<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;

use Yajra\DataTables\DataTables;

class UserService
{
    public function getUsersList(Request $request)
    {
        $users = User::query();

        if ($request->search['value'] && $request->ajax()) {
            $searchValue = $request->search['value'];
            $users = $users->where('username', 'LIKE', $searchValue . '%')
                ->orWhere('email', 'LIKE', $searchValue . '%')
                ->orWhere('contact_no', 'LIKE', $searchValue . '%');
        }

        return $users;
    }

    public function generateUsersDataTable($data)
    {

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn("contact_no", function ($row) {
                return view('components.user-contact', ['user' => $row]);
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 'active') {
                    return '<span class="badge bg-label-success me-1">Active</span>';
                } else {
                    return '<span class="badge bg-label-warning me-1">Inactive</span>';
                }
            })
            ->addColumn('email_verify', function ($row) {
                if ($row->is_verify) {
                    return '<span class="badge bg-label-success me-1">Yes</span>';
                } else {
                    return '<span class="badge bg-label-warning me-1">No</span>';
                }
            })
            ->addColumn('registered_date', function ($row) {
                return date_format($row->created_at, 'M d, Y h:i A');
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
            ->rawColumns(['status', 'username', 'email_verify', 'actions'])
            ->toJson();
    }

    public function lookupUsers($searchQuery)
    {
        $users = User::orWhere('email', 'LIKE', "%$searchQuery%")
            ->orWhere("username", "LIKE", "%$searchQuery%")
            ->select('id', 'email')
            ->get();

        $formattedUsers = [];

        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->id,
                'text' => $user->email,
            ];
        }

        return $formattedUsers;
    }

    public function createUser($request)
    {
        $data = $request->validated();
        $account_uid = $this->generateRandomUuid();

        $user = User::create(
            array_merge($data, [
                'account_uid' => $account_uid,
                'password' => Hash::make($request->password),
                'interests' => $request->has('interest_ids') ? json_encode($request->interest_ids) : null,
                'is_first_time_philippines' => $request->has('is_first_time_philippines') ? true : false,
                'is_international_tourist' => $request->has('is_international_tourist') ? true : false,
            ]),
        );

        return $user;
    }

    public function updateUser($request)
    {
        $user = User::where('id', $request->id)->first();
        $data = $request->validated();

        $update_user = $user->update(
            array_merge($data, [
                'is_verify' => $request->has('is_verify') ? true : false,
                'is_old_user' => $request->has('is_old_user') ? true : false,
                'is_first_time_philippines' => $request->has('is_first_time_philippines') ? true : false,
                'is_international_tourist' => $request->has('is_international_tourist') ? true : false,
                'interest_ids' => $request->has('interest_ids') ? json_encode($request->interest_ids) : null,
            ]),
        );

        return $update_user;
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $image_name = $request->username;

            if ($request->hasFile('user_profile')) {
                $old_upload_image = public_path('/assets/img/user_profiles') . $user->user_profile;
                @unlink($old_upload_image);
                $file = $request->file('user_profile');
                $file_name = Str::snake(Str::lower($image_name)) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . '/assets/img/user_profiles', $file_name);
            } else {
                $file_name = $user->user_profile;
            }

            $result = $this->parseContactNumber($request, $request->contact_no);

            $update_user = $user->update([
                'user_profile' => $file_name,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'birthdate' => $request->birthdate,
                'country_of_residence' => $request->country_of_residence,
                'contact_no' => $result['contactNumber'] ?? null,
                'countryCode' => $result['countryCode'] ? preg_replace("/[^0-9]/", "", $result['countryCode']) : null,
                'isoCode' => $result['isoCode'] ?? null,
            ]);

            return $update_user;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateInterest(Request $request)
    {
        try {
            $user = Auth::user();

            $update_interests = $user->update([
                'interest_ids' => $request->has('interest_ids') && $request->interest_ids != 'null' ? json_encode($request->interest_ids) : null,
            ]);

            return $update_interests;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function changePassword(Request $request)
    {
        try {
            \Validator::make($request->all(), [
                'username' => ['required'],
                'new_password' => ['required', 'min:8'],
                'confirm_password' => ['required', 'same:new_password']
            ]);

            $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $user_password = User::where($fieldType, $request->username)->update([
                'password' => Hash::make($request->new_password),
                'is_old_user' => FALSE,
            ]);

            return $user_password;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteUser($user)
    {
        try {
            $user->tokens()->delete();
            $delete_user = $user->delete();
            return $delete_user;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateRandomUuid()
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // Version 4 (random)
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // Variant (RFC 4122)

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return $uuid;
    }

    public function parseContactNumber(Request $request, $contactNo): array
    {
        try {
            if (is_string($contactNo) && is_array(json_decode($contactNo, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                $data = is_string($contactNo) ? json_decode($contactNo, true) : null;
                $countryCode = $data['countryCode'] ?? null;
                $isoCode = $data['isoCode'] ?? null;
                $number = $data['number'] ?? null;
                $contactNo = $number;
            } else {
                $countryCode = null;
                $isoCode = null;
                $contactNo = $request->input('contact_no');
            }

            return [
                'countryCode' => $countryCode,
                'isoCode' => $isoCode,
                'contactNumber' => $contactNo
            ];
        } catch (\Exception $e) {
            // Handle JSON decoding or other exceptions here, possibly log the error.
            return [
                'countryCode' => null,
                'isoCode' => null,
                'contactNumber' => null
            ];
        }
    }

}
?>