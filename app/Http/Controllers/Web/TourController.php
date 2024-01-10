<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Tour;
use App\Models\Attraction;
use App\Models\MerchantTourProvider;
use App\Models\TourBadge;

use App\Services\TourService;

use DataTables;
use Carbon\Carbon;

class TourController extends Controller
{
    protected $tourService;

    public function __construct(TourService $tourService) {
        $this->tourService = $tourService;
    }

    public function list(Request $request) {

        if($request->ajax()) {
            $admin =  Auth::guard('admin')->user();

            if(in_array($admin->role, ['tour_operator_admin', 'tour_operator_employee'])) {
                return $this->tourService->RetrieveTourProviderToursList($request);
            } 

            return $this->tourService->RetrieveAllToursList($request);
        }

        return view('admin-page.tours.list-tour');
    }

    public function getDiyTours(Request $request) {
        $diy_tours = Tour::where('type', 'DIY Tour')->get();
        return response($diy_tours);
    }

    public function getGuidedTours(Request $request) {
        $guided_tours = Tour::where('type', 'Guided Tour')->get();
        return response($guided_tours);
    }

    public function create(Request $request) {
        $attractions = Attraction::get();
        $interests = Interest::get();
        $admin =  Auth::guard('admin')->user();

        if(in_array($admin->role, ['tour_operator_admin', 'tour_operator_employee'])) {
            $tour_providers = MerchantTourProvider::where('id', $admin->merchant_data_id)->get();
        } else {
            $tour_providers = MerchantTourProvider::get();
        }

        return view('admin-page.tours.create-tour', compact('attractions', 'tour_providers', 'interests'));
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'featured_image');

        $tour = Tour::create(array_merge($data, [
            'interests' => $request->has('interests') ? json_encode($request->interests) : null,
            'attractions_assignments_ids' => $request->has('attractions_assignments_ids') ? json_encode($request->attractions_assignments_ids) : null,
            'disabled_days' => $request->has('disabled_days') ? json_encode($request->disabled_days) : null,
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
        ]));

        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $outputString = str_replace(array(":", ";"), " ", $request->name);

            $name = Str::snake(Str::lower($outputString));
            $featured_file_name = $name . '.' . $file->getClientOriginalExtension();

            $file->move(public_path() . '/assets/img/tours/' . $tour->id, $featured_file_name);

            $tour->update([
                'featured_image' => $featured_file_name
            ]);
        }

        if($tour) return redirect()->route('admin.tours.edit', $tour->id)->with('success', 'Tour created successfully');
    }

    public function edit(Request $request) {
        $attractions = Attraction::get();
        $tour = Tour::where('id', $request->id)->firstOrFail();
        $tour_providers = MerchantTourProvider::get();
        $tour_badges = TourBadge::where('tour_id', $tour->id)->get();
        $interests = Interest::get();

        return view('admin-page.tours.edit-tour', compact('tour', 'attractions', 'tour_providers', 'tour_badges', 'interests'));
    }

    public function update(Request $request) {
        $data = $request->except('_token', 'featured_image', 'attractions_assignments_ids');
        $tour = Tour::where('id', $request->id)->firstOrFail();

        $update_tour = $tour->update(array_merge($data, [
            'interests' => $request->has('interests') ? json_encode($request->interests) : null,
            'attractions_assignments_ids' => $request->has('attractions_assignments_ids') ? json_encode($request->attractions_assignments_ids) : null,
            'disabled_days' => $request->has('disabled_days') ? json_encode($request->disabled_days) : null,
            'start_date_duration' => $request->has('start_date_duration') ? Carbon::create($request->start_date_duration) : null,
            'end_date_duration' => $request->has('end_date_duration') ? Carbon::create($request->end_date_duration) : null,
            'is_cancellable' => $request->has('is_cancellable'),
            'is_refundable' => $request->has('is_refundable'),
        ]));

        if($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $outputString = str_replace(array(":", ";"), " ", $request->name);

            $name = Str::snake(Str::lower($outputString));
            $featured_file_name = $name . '.' . $file->getClientOriginalExtension();

            $old_upload_image = public_path('assets/img/tours/') . $tour->id . '/' . $tour->featured_image;
            if($old_upload_image) {
                @unlink($old_upload_image);
            }
            $save_file = $file->move(public_path() . '/assets/img/tours/' . $tour->id, $featured_file_name);
        } else {
            $featured_file_name = $tour->featured_image;
        }

        $update_tour = $tour->update([
            'featured_image' => $featured_file_name
        ]);

        if($update_tour) return back()->with('success', 'Tour updated successfully');
    }

    public function destroy(Request $request) {
        $tour = Tour::findOrFail($request->id);

        $upload_image = public_path('assets/img/tours/') . $tour->id . '/' . $tour->featured_image;

        if($upload_image) {
             @unlink($upload_image);
        }

        $remove = $tour->delete();

        if($remove) {
            return response([
                'status' => true,
                'message' => 'Tour Deleted Successfully'
            ]);
        }
    }
}
