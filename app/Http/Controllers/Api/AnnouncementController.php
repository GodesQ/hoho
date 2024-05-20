<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function getImportantAnnouncements(Request $request)
    {
        $announcements = Announcement::where('is_important', 1)->get();

        if (!$announcements || $announcements->count() == 0) {
            return response()->json([
                'status' => true,
                'message' => 'No important announcement available.',
                'announcements' => [],
            ], 204);
        }

        return response()->json([
            'status' => TRUE,
            'announcements' => AnnouncementResource::collection($announcements),
        ], 200);
    }

    public function getAnnouncements(Request $request) {
        $announcements = Announcement::where('is_active', 1)->get();

        if (!$announcements || $announcements->count() == 0) {
            return response()->json([
                'status' => FALSE,
                'message' => 'No announcement available.',
                'announcements' => [],
            ], 204);
        }

        return response()->json([
            'status' => TRUE,
            'message' => 'Announcements found.',
            'announcements' => AnnouncementResource::collection($announcements),
        ]);
    }
}