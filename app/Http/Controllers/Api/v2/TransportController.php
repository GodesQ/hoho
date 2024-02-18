<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransportResource;
use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index(Request $request) {
        return TransportResource::collection(Transport::get());
    } 

    public function show(Request $request, $transport_id) {
        return TransportResource::make(Transport::findOrFail($transport_id));
    }
}
