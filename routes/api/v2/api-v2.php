<?php

use App\Http\Controllers\Api\v2\AnnouncementController;
use App\Http\Controllers\Api\v2\AttractionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('announcements', [AnnouncementController::class, 'index']);
Route::get('announcements/{id}', [AnnouncementController::class, 'show']);

Route::get('attractions', [AttractionController::class, 'index']);
Route::get('attractions/organizations/{organization_id}', [AttractionController::class, 'getByOrganization']);