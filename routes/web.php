<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Auth\AdminAuthController;
use App\Http\Controllers\Web\Auth\UserAuthController;

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\TourController;
use App\Http\Controllers\Web\TourReservationController;
use App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\TransportController;
use App\Http\Controllers\Web\AttractionController;
use App\Http\Controllers\Web\MerchantController;
use App\Http\Controllers\Web\MerchantHotelController;
use App\Http\Controllers\Web\MerchantStoreController;
use App\Http\Controllers\Web\MerchantRestaurantController;
use App\Http\Controllers\Web\MerchantTourProviderController;

use App\Http\Controllers\Web\InterestController;
use App\Http\Controllers\Web\OrganizationController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\ProductCategoryController;
use App\Http\Controllers\Web\ReferralController;

use App\Http\Controllers\Web\AqwireController;

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

Route::get('test_location', [DashboardController::class, 'testLocation']);
Route::get('test_location2', [DashboardController::class, 'testLocation2']);

Route::view('user/success_verification_message', 'misc.success_verification_message')->name('user.success_verification_message');

Route::get('aqwire/payment/success/{id}', [AqwireController::class, 'success']);
Route::get('aqwire/payment/view_success', [AqwireController::class, 'viewSuccess']);
Route::get('aqwire/payment/cancel/{id}', [AqwireController::class, 'cancel']);
Route::get('aqwire/payment/view_cancel', [AqwireController::class, 'viewCancel']);
Route::post('aqwire/payment/callback/{id}', [AqwireController::class, 'callback']);

Route::get('user/verify_email', [UserAuthController::class, 'verifyEmail']);

Route::group(['prefix'=> 'admin', 'as' => 'admin.', 'middleware' => ['auth:admin']], function(){
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('admins', [AdminController::class, 'list'])->name('admins.list');
    Route::get('admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('admins/store', [AdminController::class, 'store'])->name('admins.store');
    Route::get('admins/edit/{id}', [AdminController::class, 'edit'])->name('admins.edit');
    Route::post('admins/update/{id}', [AdminController::class, 'update'])->name('admins.update');
    Route::delete('admins/destroy', [AdminController::class, 'destroy'])->name('admins.destroy');

    Route::get('users', [UserController::class, 'list'])->name('users.list');
    Route::get('users/lookup', [UserController::class, 'lookup'])->name('users.lookup');
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
    Route::delete('organizations/remove_image', [OrganizationController::class, 'removeImage'])->name('organizations.remove_image');

    Route::get('tours', [TourController::class, 'list'])->name('tours.list');
    Route::get('tours/diy', [TourController::class, 'getDiyTours'])->name('tours.diy');
    Route::get('tours/guided', [TourController::class, 'getGuidedTours'])->name('tours.guided');
    Route::get('tours/create', [TourController::class, 'create'])->name('tours.create');
    Route::post('tours/store', [TourController::class, 'store'])->name('tours.store');
    Route::get('tours/edit/{id}', [TourController::class, 'edit'])->name('tours.edit');
    Route::post('tours/update/{id}', [TourController::class, 'update'])->name('tours.update');
    Route::delete('tours/destroy', [TourController::class, 'destroy'])->name('tours.destroy');

    Route::get('transports', [TransportController::class, 'list'])->name('transports.list');
    Route::get('transports/create', [TransportController::class, 'create'])->name('transports.create');
    Route::post('transports/store', [TransportController::class, 'store'])->name('transports.store');
    Route::post('transport/updateLocation', [TransportController::class, 'updateLocation'])->name('transports.updateLocation');
    Route::get('transports/edit/{id}', [TransportController::class, 'edit'])->name('transports.edit');
    Route::post('transports/update/{id}', [TransportController::class, 'update'])->name('transports.update');
    Route::delete('transports/destroy', [TransportController::class, 'destroy'])->name('transports.destroy');

    Route::get('attractions', [AttractionController::class, 'list'])->name('attractions.list');
    Route::get('attractions/create', [AttractionController::class, 'create'])->name('attractions.create');
    Route::post('attractions/store', [AttractionController::class, 'store'])->name('attractions.store');
    Route::get('attractions/edit/{id}', [AttractionController::class, 'edit'])->name('attractions.edit');
    Route::post('attractions/update/{id}', [AttractionController::class, 'update'])->name('attractions.update');
    Route::delete('attractions/destroy', [AttractionController::class, 'destroy'])->name('attractions.destroy');
    Route::delete('attractions/remove_image', [AttractionController::class, 'removeImage'])->name('attractions.remove_image');

    Route::get('merchants', [MerchantController::class, 'list'])->name('merchants.list');
    Route::get('merchants/create', [MerchantController::class, 'create'])->name('merchants.create');
    Route::post('merchants/store', [MerchantController::class, 'store'])->name('merchants.store');
    Route::get('merchants/edit/{id}', [MerchantController::class, 'edit'])->name('merchants.edit');
    Route::post('merchants/update/{id}', [MerchantController::class, 'update'])->name('merchants.update');
    Route::delete('merchants/destroy', [MerchantController::class, 'destroy'])->name('merchants.destroy');

    Route::get('tour_reservations', [TourReservationController::class, 'list'])->name('tour_reservations.list');
    Route::get('tour_reservations/create', [TourReservationController::class, 'create'])->name('tour_reservations.create');
    Route::post('tour_reservations/store', [TourReservationController::class, 'store'])->name('tour_reservations.store');
    Route::get('tour_reservations/edit/{id}', [TourReservationController::class, 'edit'])->name('tour_reservations.edit');
    Route::post('tour_reservations/update/{id}', [TourReservationController::class, 'update'])->name('tour_reservations.update');
    Route::delete('tour_reservations/destroy', [TourReservationController::class, 'destroy'])->name('tour_reservations.destroy');

    Route::prefix('merchants')->as('merchants.')->group(function () {
        Route::get('hotels', [MerchantHotelController::class, 'list'])->name('hotels.list');
        Route::get('hotels/create', [MerchantHotelController::class, 'create'])->name('hotels.create');
        Route::post('hotels/store', [MerchantHotelController::class, 'store'])->name('hotels.store');
        Route::get('hotels/edit/{id}', [MerchantHotelController::class, 'edit'])->name('hotels.edit');
        Route::post('hotels/update/{id}', [MerchantHotelController::class, 'update'])->name('hotels.update');
        Route::delete('hotels/destroy', [MerchantHotelController::class, 'destroy'])->name('hotels.destroy');

        Route::get('restaurants', [MerchantRestaurantController::class, 'list'])->name('restaurants.list');
        Route::get('restaurants/create', [MerchantRestaurantController::class, 'create'])->name('restaurants.create');
        Route::post('restaurants/store', [MerchantRestaurantController::class, 'store'])->name('restaurants.store');
        Route::get('restaurants/edit/{id}', [MerchantRestaurantController::class, 'edit'])->name('restaurants.edit');
        Route::post('restaurants/update/{id}', [MerchantRestaurantController::class, 'update'])->name('restaurants.update');
        Route::delete('restaurants/destroy', [MerchantRestaurantController::class, 'destroy'])->name('restaurants.destroy');

        Route::get('tour_providers', [MerchantTourProviderController::class, 'list'])->name('tour_providers.list');
        Route::get('tour_providers/create', [MerchantTourProviderController::class, 'create'])->name('tour_providers.create');
        Route::post('tour_providers/store', [MerchantTourProviderController::class, 'store'])->name('tour_providers.store');
        Route::get('tour_providers/edit/{id}', [MerchantTourProviderController::class, 'edit'])->name('tour_providers.edit');
        Route::post('tour_providers/update/{id}', [MerchantTourProviderController::class, 'update'])->name('tour_providers.update');
        Route::delete('tour_providers/destroy', [MerchantTourProviderController::class, 'destroy'])->name('tour_providers.destroy');

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

    Route::get('roles', [RoleController::class, 'list'])->name('roles.list');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('roles/update/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/destroy', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('transactions', [TransactionController::class, 'list'])->name('transactions.list');
    Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('transactions/store', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/edit/{id}', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::post('transactions/update/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('transactions/destroy', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('product_categories', [ProductCategoryController::class, 'list'])->name('product_categories.list');
    Route::get('product_categories/create', [ProductCategoryController::class, 'create'])->name('product_categories.create');
    Route::post('product_categories/store', [ProductCategoryController::class, 'store'])->name('product_categories.store');
    Route::get('product_categories/edit/{id}', [ProductCategoryController::class, 'edit'])->name('product_categories.edit');
    Route::post('product_categories/update/{id}', [ProductCategoryController::class, 'update'])->name('product_categories.update');
    Route::delete('product_categories/destroy', [ProductCategoryController::class, 'destroy'])->name('product_categories.destroy');

    Route::get('referrals', [ReferralController::class, 'list'])->name('referrals.list');
    Route::get('referrals/create', [ReferralController::class, 'create'])->name('referrals.create');
    Route::post('referrals/store', [ReferralController::class, 'store'])->name('referrals.store');
    Route::get('referrals/edit/{id}', [ReferralController::class, 'edit'])->name('referrals.edit');
    Route::post('referrals/update/{id}', [ReferralController::class, 'update'])->name('referrals.update');
    Route::delete('referrals/destroy', [ReferralController::class, 'destroy'])->name('referrals.destroy');
});
