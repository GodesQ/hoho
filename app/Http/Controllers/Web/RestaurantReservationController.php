<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestaurantReservationController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()) {
        }

        return view('admin-page.restaurant_reservations.list-restaurant-reservation');
    }

    public function create(Request $request) {

    }

    public function store(Request $request) {
    
    }

    public function edit(Request $request, $id) {
    
    }

    public function update(Request $request, $id) {
    
    }

    public function destroy(Request $request, $id) {
    
    }
}
