<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequest;
use App\Http\Requests\Admin\UpdateRequest;
use App\Models\MerchantHotel;
use App\Models\MerchantRestaurant;
use App\Models\MerchantStore;
use App\Models\MerchantTourProvider;
use App\Services\LoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use App\Mail\MerchantAccountApprove;
use App\Mail\NewRegisteredMerchantNotification;

use App\Models\Admin;
use App\Models\Role;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = Admin::whereNotIn('role', [
                'merchant_restaurant_admin',
                'merchant_hotel_admin',
                'merchant_store_admin',
                'tour_operator_admin'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', function ($data) {
                    return Str::title(Str::replace('_', ' ', $data->role));
                })
                ->addColumn('is_approved', function ($row) {
                    if ($row->is_approved) {
                        return '<span class="badge bg-label-success me-1">Yes</span>';
                    } else {
                        return '<span class="badge bg-label-secondary me-1">No</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                    <a href="/admin/admins/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="#" id="' . $row->id . '" class="btn btn-outline-danger btn-sm remove-btn"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                })
                ->rawColumns(['actions', 'is_approved'])
                ->make(true);
        }

        return view('admin-page.admins.list-admin');
    }

    public function create(Request $request)
    {
        $roles = Role::get();
        return view('admin-page.admins.create-admin', compact('roles'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->except('_token', 'password');

        $admin = Admin::create(array_merge($data, [
            'is_approved' => $request->has('is_approved') ? true : false,
            'password' => Hash::make($request->password)
        ]));
        
        // LoggerService::log('create', Admin::class, ['added' => $request->all()]);

        if ($admin)
            return redirect()->route('admin.admins.edit', $admin->id)->withSuccess('Admin created successfully');
    }

    public function edit(Request $request)
    {
        $roles = Role::get();
        $admin = Admin::where('id', $request->id)->firstOrFail();

        return view('admin-page.admins.edit-admin', compact('admin', 'roles'));
    }

    public function update(UpdateRequest $request)
    {
        $data = $request->except('_token');
        $admin = Admin::where('id', $request->id)->first();

        $image_name = $admin->username;

        if ($request->hasFile('admin_profile')) {
            $old_upload_image = public_path('/assets/img/admin_profiles') . $admin->admin_profile;
            @unlink($old_upload_image);
            $file = $request->file('admin_profile');
            $file_name = Str::snake(Str::lower($image_name)) . '.' . $file->getClientOriginalExtension();

            // Save Admin Profile
            $file->move(public_path() . '/assets/img/admin_profiles', $file_name);
        } else {
            $file_name = $admin->admin_profile;
        }

        $admin->update(array_merge($data, [
            'is_approved' => $request->has('is_approved') ? true : false,
            'admin_profile' => $file_name
        ]));

        LoggerService::log('update', Admin::class, ['changes' => $admin->getChanges()]);

        if ($request->has('is_approved') && !$admin->email_approved_at) {
            $details = [
                'email' => $request->email,
            ];

            Mail::to($request->email)->send(new MerchantAccountApprove($details));

            $admin->update([
                'merchant_email_approved_at' => Carbon::now()
            ]);

            LoggerService::log('update', Admin::class, ['changes' => $admin->getChanges()]);
        }

        return back()->withSuccess('Admin updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $admin = Admin::where('id', $id)->first();

        if ($admin->admin_profile) {
            $old_upload_image = public_path('/assets/img/admin_profiles') . $admin->admin_profile;
            @unlink($old_upload_image);
        }

        $admin->delete();

        // LoggerService::log('delete', Admin::class, null);

        return response([
            'status' => TRUE,
            'message' => 'Admin Deleted Successfully'
        ]);
    }

    public function merchantAdmins()
    {

        // $auth = Auth::user();
        $results = Admin::whereNotNull('merchant_data_id')->get();
        foreach ($results as $key => $result) {

            switch ($result->role) {
                case 'merchant_store_admin':
                    $merchant_data = MerchantStore::where('id', $result->merchant_data_id)->first();
                    break;
                case 'merchant_restaurant_admin':
                    $merchant_data = MerchantRestaurant::where('id', $result->merchant_data_id)->first();
                    break;
                case 'merchant_hotel_admin':
                    $merchant_data = MerchantHotel::where('id', $result->merchant_data_id)->first();
                    break;
                case 'tour_operator_admin':
                    $merchant_data = MerchantTourProvider::where('id', $result->merchant_data_id)->first();
                    break;
                default:
                    $merchant_data = null;
                    break;
            }

            $result->update([
                'merchant_id' => $merchant_data->merchant_id ?? null
            ]);
        }

        echo "Success";
    }

    public function operatorAdmins()
    {
        $admins = Admin::whereHas('transport')->with('transport')->get();
        foreach ($admins as $key => $admin) {
            $admin->update([
                'transport_id' => $admin->transport->id
            ]);
        }

        echo "Success";

    }

    public function sendMessageWithSemaphore()
    {
        // $client = new Client();

        // $parameters = [
        //     'apikey' => '2e9288e75f56bb100bd53d018142b2e7',
        //     'number' => '+639633987953',
        //     'message' => 'I just sent my first message with Semaphore',
        // ];

        // try {
        //     $response = $client->request('POST', 'https://semaphore.co/api/v4/messages', [
        //         'form_params' => $parameters
        //     ]);

        //     $output = $response->getBody()->getContents();

        //     // Show the server response
        //     echo $output;
        // } catch (\Exception $e) {
        //     // Handle any exceptions or errors here
        //     echo "Error: " . $e->getMessage();
        // }
    }
}
