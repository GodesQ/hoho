<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Auth\AdminAuthController;

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\TourController;

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
    if(Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('admin.login');
    }
});

Route::get('login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('login', [AdminAuthController::class, 'saveLogin'])->name('admin.saveLogin');

Route::group(['prefix'=> 'admin', 'as' => 'admin.'], function(){
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('users', [UserController::class, 'list'])->name('users.list');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/destroy', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('tours', [TourController::class, 'list'])->name('tours.list');
    Route::get('tours/create', [TourController::class, 'create'])->name('tours.create');
    Route::post('tours/store', [TourController::class, 'store'])->name('tours.store');
    Route::get('tours/edit/{id}', [TourController::class, 'edit'])->name('tours.edit');
    Route::post('tours/update/{id}', [TourController::class, 'update'])->name('tours.update');
    Route::delete('tours/destroy', [TourController::class, 'destroy'])->name('tours.destroy');
});
