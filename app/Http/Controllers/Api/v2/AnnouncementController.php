<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index() {
        return AnnouncementResource::collection(Announcement::get());
    }

    public function show(Request $request, $id) {
        try {
            return new AnnouncementResource(Announcement::findOrFail($id));
        } catch (\Error $e) {
            return response([
                'error' => 'Announcement not found', // Provide a generic error message
            ], 404);
        }
    }
    
}
