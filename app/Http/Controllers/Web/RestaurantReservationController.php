<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantReservation\StoreRequest;
use App\Http\Requests\RestaurantReservation\UpdateRequest;
use App\Models\Merchant;
use App\Models\RestaurantReservation;
use App\Models\Role;
use App\Services\RestaurantReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class RestaurantReservationController extends Controller
{   
    private $restaurantReservationService;

    public function __construct(RestaurantReservationService $restaurantReservationService) {
        $this->restaurantReservationService = $restaurantReservationService;
    }

    /**
     * Retrieves a list of restaurant reservations and returns it as a DataTables response.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request) {
        if($request->ajax()) {
            $user = Auth::guard('admin')->user();

            $restuarant_reservations = RestaurantReservation::query();

            if($user->role === Role::MERCHANT_RESTAURANT_ADMIN || $user->role === Role::MERCHANT_RESTAURANT_EMPLOYEE) {
                $restuarant_reservations = $restuarant_reservations->where('merchant_id', $user->merchant_id);
            }
            
            return $this->restaurantReservationService->generateDataTable($request, $restuarant_reservations);
        }

        return view('admin-page.restaurant_reservations.list-restaurant-reservation');
    }

    /**
     * Create restaurant reservation
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request) {
        $user = Auth::guard('admin')->user();

        if($user->role == Role::MERCHANT_RESTAURANT_ADMIN || $user->role === Role::MERCHANT_RESTAURANT_EMPLOYEE) {
            $merchants = Merchant::where('id', $user->merchant_id)->where('type', 'Restaurant')->get();
        } else {
            $merchants = Merchant::where('type', 'Restaurant')->get();
        }

        return view('admin-page.restaurant_reservations.create-restaurant-reservation', compact('merchants'));
    }

    /**
     * Store restaurant reservation
     * @param \App\Http\Requests\RestaurantReservation\StoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request) {
        $data = $request->validated();

        $reservation = $this->restaurantReservationService->create($request, $data);

        return redirect()->route('admin.restaurant_reservations.edit', $reservation->id)->with('success','Restaurant reservation added successfully.');
    }

    /**
     * Edit restaurant reservation
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Request $request, $id) {
        $user = Auth::guard('admin')->user();
        $reservation = RestaurantReservation::findOrFail($id);

        if($user->role == Role::MERCHANT_RESTAURANT_ADMIN) {
            $merchants = Merchant::where('id', $user->merchant_id)->where('type', 'Restaurant')->get();
        } else {
            $merchants = Merchant::where('type', 'Restaurant')->get();
        }
        
        return view('admin-page.restaurant_reservations.edit-restaurant-reservation', compact('reservation', 'merchants'));
    }

    /**
     * Update restaurant reservation
     * @param \App\Http\Requests\RestaurantReservation\UpdateRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, $id) {
        $data = $request->validated();

        $this->restaurantReservationService->update($request, $id, $data);

        return back()->with('success','Restaurant reservation updated successfully.');
    }

    /**
     * Delete restaurant reservation
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $reservation = RestaurantReservation::findOrFail($id);

        $reservation->delete();

        return response([
            'status' => TRUE,
            'message' => 'Restaurant Reservation deleted successfully'
        ]);
    }
}
