<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelReservation\StoreRequest;
use App\Http\Requests\HotelReservation\UpdateRequest;
use App\Models\HotelReservation;
use App\Models\Merchant;
use App\Models\Role;
use App\Services\AqwireService;
use App\Services\HotelReservationService;
use App\Services\LoggerService;
use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelReservationController extends Controller
{   
    private $aqwireService;
    private $hotelReservationService;

    public function __construct(AqwireService $aqwireService, HotelReservationService $hotelReservationService) {
        $this->aqwireService = $aqwireService;
        $this->hotelReservationService = $hotelReservationService;
    }

    /**
     * Retrieves a list of hotel reservations and returns it as a DataTables response.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::guard('admin')->user();

            $hotel_reservations = HotelReservation::query();

            $hotel_reservations = $hotel_reservations->with('reserved_user', 'room');

            // Check if the authenticated user is hotel admin or hotel employee
            if(in_array($user->role, [Role::MERCHANT_HOTEL_ADMIN, Role::MERCHANT_HOTEL_EMPLOYEE])) {
                $hotel_reservations = HotelReservation::whereHas('room', function ($q) use ($user) {
                    $q->where('merchant_id', $user->merchant_id);    
                });
            }

            return $this->hotelReservationService->generateDataTable($request, $hotel_reservations);
        }

        return view("admin-page.hotel_reservations.list-hotel-reservation");
    }

    /**
     * Create hotel reservation
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {   
        $user = Auth::guard('admin')->user();
        $merchant_hotels = Merchant::where('type', 'Hotel')
                            ->when(in_array($user->role, [Role::MERCHANT_HOTEL_ADMIN, Role::MERCHANT_HOTEL_EMPLOYEE]), function ($query) use($user) {
                                return $query->where('id', $user->merchant_id);
                            })
                            ->get();
                            
        return view('admin-page.hotel_reservations.create-hotel-reservation', compact('merchant_hotels'));
    }

    /**
     * Store hotel reservation
     * @param \App\Http\Requests\HotelReservation\StoreRequest $request
     * @return mixed
     */
    public function store(StoreRequest $request)
    {   
        try {
            $data = $request->validated();
            $reservation = $this->hotelReservationService->create($request, $data);

            return redirect()->route('admin.hotel_reservations.edit', $reservation->id)->withSuccess('Hotel reservation added successfully.');
        } catch (Exception $e) {
            return back()->with('fail', $e->getMessage());
        }
    }

    /**
     * Edit hotel reservation
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Request $request, $id)
    {   
        $user = Auth::guard('admin')->user();   
        $merchant_hotels = Merchant::where('type', 'Hotel')
                            ->when(in_array($user->role, [Role::MERCHANT_HOTEL_ADMIN, Role::MERCHANT_HOTEL_EMPLOYEE]), function ($query) use($user) {
                                return $query->where('id', $user->merchant_id);
                            })
                            ->get();
                            
        $reservation = HotelReservation::where('id', $id)->with('reserved_user', 'room')->firstOrFail();

        return view('admin-page.hotel_reservations.edit-hotel-reservation', compact('reservation', 'merchant_hotels'));
    }

    /**
     * Update hotel reservation
     * @param \App\Http\Requests\HotelReservation\UpdateRequest $request
     * @param mixed $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {   
        try {
            $data = $request->validated();

            $this->hotelReservationService->update($request, $id, $data);

            return back()->withSuccess('Hotel reservation updated successfully');

        } catch (Exception $e) {
            return back()->with('fail', $e->getMessage());
        }
    }

    /**
     * Delete hotel reservation
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {   
        try {
            $reservation = HotelReservation::findOrFail($id);

            $reservation_data = $reservation->toArray();

            $reservation->delete();

            LoggerService::log('delete', HotelReservation::class, ['reservation' => $reservation_data]);

            return response([
                'status' => TRUE,
                'message' => 'Hotel Reservation deleted successfully'
            ]);

        } catch (ErrorException $e) {
            return response([
                'status' => FALSE,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}