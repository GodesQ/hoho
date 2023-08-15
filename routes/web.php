<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Auth\AdminAuthController;
use App\Http\Controllers\Web\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('admin-page.dashboard.dashboard');
});

Route::get('/login', [AdminAuthController::class, 'login']);
Route::post('/login', [AdminAuthController::class, 'saveLogin'])->name('admin.saveLogin');

Route::group(['prefix'=> 'admin', 'as' => 'admin.'], function(){
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

});
