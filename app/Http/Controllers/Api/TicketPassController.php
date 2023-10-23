<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TicketPass;

class TicketPassController extends Controller
{
    public function getTicketPasses(Request $request) {
        $ticket_passes = TicketPass::get();

        return response([
            'status' => TRUE,
            'ticket_passes' => $ticket_passes
        ]);

    }

    
}
