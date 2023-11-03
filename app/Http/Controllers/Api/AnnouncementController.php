<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function getImportantAnnouncements(Request $request)
    {
        $announcements = Announcement::select('id', 'announcement_image', 'type', 'name', 'message', 'is_active', 'is_important')->where('is_important', 1)->get();

        if (!$announcements || $announcements->count() == 0) {
            return response()->json([
                'status' => FALSE,
                'message' => 'No Important Announcements',
                'announcements' => [],
            ], 404);
        }

        return response()->json([
            'status' => TRUE,
            'message' => 'Announcements Found',
            'announcements' => $announcements,
        ]);
    }

    public function getAnnouncements(Request $request) {
        $announcements = Announcement::select('id', 'announcement_image', 'type', 'name', 'message', 'is_active', 'is_important')->where('is_active', 1)->get();

        if (!$announcements || $announcements->count() == 0) {
            return response()->json([
                'status' => FALSE,
                'message' => 'No Announcements Found',
                'announcements' => [],
            ], 404);
        }

        return response()->json([
            'status' => TRUE,
            'message' => 'Announcements Found',
            'announcements' => $announcements,
        ]);
    }
}