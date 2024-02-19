<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketPassResource;
use App\Models\TicketPass;
use Illuminate\Http\Request;

class TicketPassController extends Controller
{
    public function index(Request $request) {
        return TicketPassResource::collection(TicketPass::get());
    }

    public function show(Request $request, $ticket_pass_id) {
        return TicketPassResource::make(TicketPass::findOrFail($ticket_pass_id));
    }
}