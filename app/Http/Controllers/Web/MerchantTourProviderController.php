<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\MerchantTourProvider;
use App\Models\Merchant;

use App\Services\MerchantTourProviderService;

use DataTables;

class MerchantTourProviderController extends Controller
{   
    protected $merchantTourProviderService;

    public function __construct(MerchantTourProviderService $merchantTourProviderService) {
        $this->merchantTourProviderService = $merchantTourProviderService;
    }

    public function list(Request $request) {
        if($request->ajax()) {
            $data = MerchantTourProvider::latest()->with('merchant');
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name', function ($row) {
                        return optional($row->merchant)->name;
                    })
                    ->addColumn('nature_of_business', function($row) {
                        return optional($row->merchant)->nature_of_business;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/merchants/tour_providers/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <button id="'. $row->id .'" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }
        return view('admin-page.merchants.tour_providers.list-tour-provider');
    }

    public function create(Request $request) {
        return view('admin-page.merchants.tour_providers.create-tour-provider');
    }

    public function store(Request $request) {
        $result = $this->merchantTourProviderService->CreateMerchantTourProvider($request);

        if($result['status']) {
            $previousUrl = \URL::previous();
            $previousPath = parse_url($previousUrl, PHP_URL_PATH);

            if ($previousPath === '/merchant_form/tour_provider') {
                $admin = Auth::guard('admin')->user();

                if($admin->is_merchant) {
                    $admin->update([
                        'merchant_data_id' =>  $result['merchant_tour_provider']->id
                    ]);
                }

                return redirect()->route('admin.dashboard')->withSuccess('Merchant Tour Provider Created Successfully');
            }

            return redirect()->route('admin.merchants.tour_providers.edit', $result['merchant_tour_provider']->id)->withSuccess('Merchant Tour Provider Created Successfully');
        }
    }

    public function edit(Request $request) {
        $tour_provider = MerchantTourProvider::where('id', $request->id)->with('merchant')->first();
        return view('admin-page.merchants.tour_providers.edit-tour-provider', compact('tour_provider'));
    }

    public function update(Request $request) {
        $result = $this->merchantTourProviderService->UpdateMerchantTourProvider($request);

        if($result['status']) {
            return back()->with('success', 'Tour Provider updated successfully');
        }

        return back()->with('fail', 'Tour Provider Failed to Update');
    }

    public function destroy(Request $request) {
        $tour_provider = MerchantTourProvider::where('id', $request->id)->with('merchant')->first();

        if(!$tour_provider) {
            return response([
                'status' => false,
                'message' => 'Tour Provider Not Found'
            ]);
        }

        $old_upload_image = public_path('assets/img/tour_providers/') . $tour_provider->merchant->id . '/' . $tour_provider->merchant->featured_image;

        if($old_upload_image) {
            $remove_image = @unlink($old_upload_image);
            $directory = public_path('assets/img/tour_providers/') . $tour_provider->merchant->id;
            if (is_dir($directory)) {
                @rmdir($directory);
            }
        }

        $delete_merchant = $tour_provider->merchant->delete();

        if($delete_merchant) {
            $delete_tour_provider = $tour_provider->delete();
            if($delete_tour_provider) {
                return response([
                    'status' => true,
                    'message' => 'Tour Provider Deleted Successfully'
                ]);
            }
        }
    }
}
