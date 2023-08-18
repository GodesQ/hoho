<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Auth\AdminAuthController;

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\TourController;
use App\Http\Controllers\Web\TransportController;
use App\Http\Controllers\Web\AttractionController;
use App\Http\Controllers\Web\MerchantController;
use App\Http\Controllers\Web\MerchantHotelController;
use App\Http\Controllers\Web\MerchantStoreController;

use App\Http\Controllers\Web\InterestController;
use App\Http\Controllers\Web\OrganizationController;

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

Route::get('admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'saveLogin'])->name('admin.saveLogin');

Route::group(['prefix'=> 'admin', 'as' => 'admin.', 'middleware' => ['auth:admin']], function(){
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('users', [UserController::class, 'list'])->name('users.list');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/destroy', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('organizations', [OrganizationController::class, 'list'])->name('organizations.list');
    Route::get('organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('organizations/store', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::get('organizations/edit/{id}', [OrganizationController::class, 'edit'])->name('organizations.edit');
    Route::post('organizations/update/{id}', [OrganizationController::class, 'update'])->name('organizations.update');
    Route::delete('organizations/destroy', [OrganizationController::class, 'destroy'])->name('organizations.destroy');

    Route::get('tours', [TourController::class, 'list'])->name('tours.list');
    Route::get('tours/create', [TourController::class, 'create'])->name('tours.create');
    Route::post('tours/store', [TourController::class, 'store'])->name('tours.store');
    Route::get('tours/edit/{id}', [TourController::class, 'edit'])->name('tours.edit');
    Route::post('tours/update/{id}', [TourController::class, 'update'])->name('tours.update');
    Route::delete('tours/destroy', [TourController::class, 'destroy'])->name('tours.destroy');

    Route::get('transports', [TransportController::class, 'list'])->name('transports.list');
    Route::get('transports/create', [TransportController::class, 'create'])->name('transports.create');
    Route::post('transports/store', [TransportController::class, 'store'])->name('transports.store');
    Route::get('transports/edit/{id}', [TransportController::class, 'edit'])->name('transports.edit');
    Route::post('transports/update/{id}', [TransportController::class, 'update'])->name('transports.update');
    Route::delete('transports/destroy', [TransportController::class, 'destroy'])->name('transports.destroy');

    Route::get('attractions', [AttractionController::class, 'list'])->name('attractions.list');
    Route::get('attractions/create', [AttractionController::class, 'create'])->name('attractions.create');
    Route::post('attractions/store', [AttractionController::class, 'store'])->name('attractions.store');
    Route::get('attractions/edit/{id}', [AttractionController::class, 'edit'])->name('attractions.edit');
    Route::post('attractions/update/{id}', [AttractionController::class, 'update'])->name('attractions.update');
    Route::delete('attractions/destroy', [AttractionController::class, 'destroy'])->name('attractions.destroy');

    Route::get('merchants', [MerchantController::class, 'list'])->name('merchants.list');
    Route::get('merchants/create', [MerchantController::class, 'create'])->name('merchants.create');
    Route::post('merchants/store', [MerchantController::class, 'store'])->name('merchants.store');
    Route::get('merchants/edit/{id}', [MerchantController::class, 'edit'])->name('merchants.edit');
    Route::post('merchants/update/{id}', [MerchantController::class, 'update'])->name('merchants.update');
    Route::delete('merchants/destroy', [MerchantController::class, 'destroy'])->name('merchants.destroy');

    Route::prefix('merchants')->as('merchants.')->group(function () {
        Route::get('hotels', [MerchantHotelController::class, 'list'])->name('hotels.list');
        Route::get('hotels/create', [MerchantHotelController::class, 'create'])->name('hotels.create');
        Route::post('hotels/store', [MerchantHotelController::class, 'store'])->name('hotels.store');
        Route::get('hotels/edit/{id}', [MerchantHotelController::class, 'edit'])->name('hotels.edit');
        Route::post('hotels/update/{id}', [MerchantHotelController::class, 'update'])->name('hotels.update');
        Route::delete('hotels/destroy', [MerchantHotelController::class, 'destroy'])->name('hotels.destroy');

        Route::get('stores', [MerchantStoreController::class, 'list'])->name('stores.list');
        Route::get('stores/create', [MerchantStoreController::class, 'create'])->name('stores.create');
        Route::post('stores/store', [MerchantStoreController::class, 'store'])->name('stores.store');
        Route::get('stores/edit/{id}', [MerchantStoreController::class, 'edit'])->name('stores.edit');
        Route::post('stores/update/{id}', [MerchantStoreController::class, 'update'])->name('stores.update');
        Route::delete('stores/destroy', [MerchantStoreController::class, 'destroy'])->name('stores.destroy');
    });

    Route::get('interests', [InterestController::class, 'list'])->name('interests.list');
    Route::get('interests/create', [InterestController::class, 'create'])->name('interests.create');
    Route::post('interests/store', [InterestController::class, 'store'])->name('interests.store');
    Route::get('interests/edit/{id}', [InterestController::class, 'edit'])->name('interests.edit');
    Route::post('interests/update/{id}', [InterestController::class, 'update'])->name('interests.update');
    Route::delete('interests/destroy', [InterestController::class, 'destroy'])->name('interests.destroy');
});
