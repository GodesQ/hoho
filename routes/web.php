<?php

use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\DeleteAccountController;
use App\Http\Controllers\Web\FoodCategoryController;
use App\Http\Controllers\Web\FoodController;
use App\Http\Controllers\Web\HotelReservationController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\RestaurantReservationController;
use App\Http\Controllers\Web\RoomController;
use App\Http\Controllers\Web\TourBadgeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Auth\AdminAuthController;
use App\Http\Controllers\Web\Auth\UserAuthController;

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\TourController;
use App\Http\Controllers\Web\TicketPassController;
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
use App\Http\Controllers\Web\PermissionController;
use App\Http\Controllers\Web\ProductCategoryController;
use App\Http\Controllers\Web\ReferralController;
use App\Http\Controllers\Web\DataReportController;
use App\Http\Controllers\Web\PromoCodeController;
use App\Http\Controllers\Web\ForgotPasswordController;
use App\Http\Controllers\Web\LogController;
use App\Http\Controllers\Web\AnnouncementController;
use App\Http\Controllers\Web\TourUnavailableDateController;

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

Route::get('delete-account', [DeleteAccountController::class, 'index'])->name('delete-account.index');
Route::post('delete-account', [DeleteAccountController::class,'confirmDeleteAccountEmail'])->name('delete-account.post');
Route::get('delete-account/otp/{token}/{email}', [DeleteAccountController::class,'showOTPInputForm'])->name('delete-account.otp');
Route::post('delete-account/otp/confirm', [DeleteAccountController::class,'confirmOTP'])->name('delete-account.otp.confirm');

Route::view('deleted-account/message', 'misc.deleted-account-message')->name('delete-account.message');

Route::get('admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'saveLogin'])->name('admin.saveLogin');

Route::get('register', [AdminAuthController::class, 'register'])->name('admin.register');
Route::post('register', [AdminAuthController::class, 'saveRegister'])->name('admin.saveRegister');

Route::get('test_location', [DashboardController::class, 'testLocation']);
Route::get('test_location2', [DashboardController::class, 'testLocation2']);

Route::get('/send_message', [AdminController::class, 'sendMessageWithSemaphore']);

Route::view('user/success_verification_message', 'misc.success_verification_message')->name('user.success_verification_message');
Route::view('merchant-account-registered-message', 'misc.merchant-registered-message')->name('merchant_account_registered_message');

Route::get('aqwire/payment/success/{id}', [AqwireController::class, 'success']);
Route::get('aqwire/payment/view_success', [AqwireController::class, 'viewSuccess']);

Route::get('aqwire/payment/cancel/{id}', [AqwireController::class, 'cancel']);
Route::get('aqwire/payment/view_cancel', [AqwireController::class, 'viewCancel']);

Route::post('aqwire/payment/callback/{id}', [AqwireController::class, 'callback']);

Route::get('user/verify_email', [UserAuthController::class, 'verifyEmail']);

Route::view('test_email', 'emails.delete-account-otp');

Route::get('user/reset_password_form', [ForgotPasswordController::class, 'resetPasswordForm'])->name('user.reset_password_form');
Route::post('user/reset_password_form', [ForgotPasswordController::class, 'postResetPasswordForm'])->name('user.post_reset_password_form');
Route::view('user/reset_password_success', 'misc.success-reset-password-message')->name('user.reset_password_success');

Route::get('merchant_form/{type}', [MerchantController::class, 'merchant_form'])->name('merchant_form')->middleware('auth:admin');

Route::group(['prefix'=> 'admin', 'as' => 'admin.', 'middleware' => ['auth:admin']], function(){
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('merchant_created');
    Route::get('profile', [DashboardController::class, 'adminProfile'])->name('profile');
    Route::post('profile', [DashboardController::class, 'saveProfile'])->name('profile.post');
    Route::post('change_password', [DashboardController::class, 'changePassword'])->name('change_password.post');

    Route::get('admins', [AdminController::class, 'list'])->name('admins.list')->can('view_admins_list');
    Route::get('admins/create', [AdminController::class, 'create'])->name('admins.create')->can('create_admin');
    Route::post('admins/store', [AdminController::class, 'store'])->name('admins.store')->can('create_admin');
    Route::get('admins/edit/{id}', [AdminController::class, 'edit'])->name('admins.edit')->can('edit_admin');
    Route::post('admins/update/{id}', [AdminController::class, 'update'])->name('admins.update')->can('update_admin');
    Route::delete('admins/destroy', [AdminController::class, 'destroy'])->name('admins.destroy')->can('delete_admin');
    Route::get('admins/merchantAdmins', [AdminController::class, 'merchantAdmins']);
    Route::get('admins/operatorAdmins', [AdminController::class, 'operatorAdmins']);
    
    Route::get('users', [UserController::class, 'list'])->name('users.list')->can('view_users_list');
    Route::get('users/lookup', [UserController::class, 'lookup'])->name('users.lookup');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->can('create_user');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store')->can('create_user');
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('users.edit')->can('edit_user');
    Route::post('users/update/{id}', [UserController::class, 'update'])->name('users.update')->can('update_user');
    Route::delete('users/destroy', [UserController::class, 'destroy'])->name('users.destroy')->can('delete_user');
    Route::get('users/update_contacts', [UserController::class, 'updateUserContacts'])->can('view_users_list');
    Route::get('users/resend_email/{username}/{email}', [UserController::class, 'resend_email'])->name('users.resend_email')->can('view_users_list');

    Route::get('organizations', [OrganizationController::class, 'list'])->name('organizations.list')->can('view_organizations_list');
    Route::get('organizations/create', [OrganizationController::class, 'create'])->name('organizations.create')->can('create_organization');
    Route::post('organizations/store', [OrganizationController::class, 'store'])->name('organizations.store')->can('create_organization');
    Route::get('organizations/edit/{id}', [OrganizationController::class, 'edit'])->name('organizations.edit')->can('edit_organization');
    Route::post('organizations/update/{id}', [OrganizationController::class, 'update'])->name('organizations.update')->can('update_organization');
    Route::delete('organizations/destroy', [OrganizationController::class, 'destroy'])->name('organizations.destroy')->can('delete_organization');
    Route::delete('organizations/remove_image', [OrganizationController::class, 'removeImage'])->name('organizations.remove_image')->can('update_organization');

    Route::get('tours', [TourController::class, 'list'])->name('tours.list')->can('view_tours_list')->middleware('merchant_created');
    Route::get('tours/diy', [TourController::class, 'getDiyTours'])->name('tours.diy')->middleware('merchant_created');
    Route::get('tours/guided', [TourController::class, 'getGuidedTours'])->name('tours.guided')->middleware('merchant_created');
    Route::get('tours/create', [TourController::class, 'create'])->name('tours.create')->middleware('merchant_created');
    Route::post('tours/store', [TourController::class, 'store'])->name('tours.store')->middleware('merchant_created');
    Route::get('tours/edit/{id}', [TourController::class, 'edit'])->name('tours.edit')->middleware('merchant_created');
    Route::post('tours/update/{id}', [TourController::class, 'update'])->name('tours.update')->middleware('merchant_created');
    Route::delete('tours/destroy', [TourController::class, 'destroy'])->name('tours.destroy');

    Route::get('tour_badges', [TourBadgeController::class, 'list'])->name('tour_badges.list');
    Route::get('tour_badges/create', [TourBadgeController::class, 'create'])->name('tour_badges.create');
    Route::post('tour_badges/store', [TourBadgeController::class, 'store'])->name('tour_badges.store');
    Route::get('tour_badges/edit/{id}', [TourBadgeController::class, 'edit'])->name('tour_badges.edit');
    Route::post('tour_badges/update/{id}', [TourBadgeController::class, 'update'])->name('tour_badges.update');
    Route::delete('tour_badges/destroy', [TourBadgeController::class, 'destroy'])->name('tour_badges.destroy');

    // Route::post('tour_badges/store', [TourBadgeController::class, 'store'])->name('tour_badges.store');

    Route::get('transports', [TransportController::class, 'list'])->name('transports.list')->can('view_transports_list');
    Route::get('transports/create', [TransportController::class, 'create'])->name('transports.create');
    Route::post('transports/store', [TransportController::class, 'store'])->name('transports.store');
    Route::post('transport/updateLocation', [TransportController::class, 'updateLocation'])->name('transports.updateLocation');
    Route::get('transports/edit/{id}', [TransportController::class, 'edit'])->name('transports.edit');
    Route::post('transports/update/{id}', [TransportController::class, 'update'])->name('transports.update');
    Route::delete('transports/destroy', [TransportController::class, 'destroy'])->name('transports.destroy');

    Route::get('attractions', [AttractionController::class, 'list'])->name('attractions.list')->can('view_attractions_list');
    Route::get('attractions/create', [AttractionController::class, 'create'])->name('attractions.create');
    Route::post('attractions/store', [AttractionController::class, 'store'])->name('attractions.store');
    Route::get('attractions/edit/{id}', [AttractionController::class, 'edit'])->name('attractions.edit');
    Route::post('attractions/update/{id}', [AttractionController::class, 'update'])->name('attractions.update');
    Route::delete('attractions/destroy', [AttractionController::class, 'destroy'])->name('attractions.destroy');
    Route::delete('attractions/remove_image', [AttractionController::class, 'removeImage'])->name('attractions.remove_image');
    // Route::get('attractions/update_attractions', [AttractionController::class, 'update_attractions']);

    Route::get('tour_reservations', [TourReservationController::class, 'list'])->name('tour_reservations.list')->can('view_tour_reservations_list')->middleware('merchant_created');
    Route::get('tour_reservations/create', [TourReservationController::class, 'create'])->name('tour_reservations.create')->middleware('merchant_created');
    Route::post('tour_reservations/store', [TourReservationController::class, 'store'])->name('tour_reservations.store');
    Route::get('tour_reservations/edit/{id}', [TourReservationController::class, 'edit'])->name('tour_reservations.edit');
    Route::post('tour_reservations/update/{id}', [TourReservationController::class, 'update'])->name('tour_reservations.update');
    Route::delete('tour_reservations/destroy', [TourReservationController::class, 'destroy'])->name('tour_reservations.destroy');

    Route::get('tour_reservations/reservation_codes/{reservation_id}', [TourReservationController::class, 'get_tour_reservation_codes'])->name('tour_reservations.reservation_codes');

    Route::get('merchants', [MerchantController::class, 'list'])->name('merchants.list');
    Route::get('merchants/create', [MerchantController::class, 'create'])->name('merchants.create');
    Route::post('merchants/store', [MerchantController::class, 'store'])->name('merchants.store');
    Route::get('merchants/edit/{id}', [MerchantController::class, 'edit'])->name('merchants.edit');
    Route::post('merchants/update/{id}', [MerchantController::class, 'update'])->name('merchants.update');
    Route::delete('merchants/destroy', [MerchantController::class, 'destroy'])->name('merchants.destroy');


    Route::prefix('merchants')->as('merchants.')->group(function () {
        Route::get('hotels', [MerchantHotelController::class, 'list'])->name('hotels.list')->can('view_merchant_hotels_list');
        Route::get('hotels/create', [MerchantHotelController::class, 'create'])->name('hotels.create');
        Route::post('hotels/store', [MerchantHotelController::class, 'store'])->name('hotels.store');
        Route::get('hotels/edit/{id}', [MerchantHotelController::class, 'edit'])->name('hotels.edit');
        Route::post('hotels/update/{id}', [MerchantHotelController::class, 'update'])->name('hotels.update');
        Route::delete('hotels/destroy', [MerchantHotelController::class, 'destroy'])->name('hotels.destroy');
        Route::delete('hotels/remove_image', [MerchantHotelController::class, 'removeImage'])->name('hotels.remove_image');

        Route::get('restaurants', [MerchantRestaurantController::class, 'list'])->name('restaurants.list')->can('view_merchant_restaurants_list');
        Route::get('restaurants/create', [MerchantRestaurantController::class, 'create'])->name('restaurants.create');
        Route::post('restaurants/store', [MerchantRestaurantController::class, 'store'])->name('restaurants.store');
        Route::get('restaurants/edit/{id}', [MerchantRestaurantController::class, 'edit'])->name('restaurants.edit');
        Route::post('restaurants/update/{id}', [MerchantRestaurantController::class, 'update'])->name('restaurants.update');
        Route::delete('restaurants/destroy', [MerchantRestaurantController::class, 'destroy'])->name('restaurants.destroy');
        Route::delete('restaurants/remove_image', [MerchantRestaurantController::class, 'removeImage'])->name('restaurants.remove_image');
        Route::get('restaurants/update_restaurants', [MerchantRestaurantController::class, 'update_restaurants'])->name('merchants.update_restaurants');

        Route::get('tour_providers', [MerchantTourProviderController::class, 'list'])->name('tour_providers.list')->can('view_merchant_tour_providers_list');
        Route::get('tour_providers/create', [MerchantTourProviderController::class, 'create'])->name('tour_providers.create');
        Route::post('tour_providers/store', [MerchantTourProviderController::class, 'store'])->name('tour_providers.store');
        Route::get('tour_providers/edit/{id}', [MerchantTourProviderController::class, 'edit'])->name('tour_providers.edit');
        Route::post('tour_providers/update/{id}', [MerchantTourProviderController::class, 'update'])->name('tour_providers.update');
        Route::delete('tour_providers/destroy', [MerchantTourProviderController::class, 'destroy'])->name('tour_providers.destroy');

        Route::get('stores', [MerchantStoreController::class, 'list'])->name('stores.list')->can('view_merchant_stores_list');
        Route::get('stores/create', [MerchantStoreController::class, 'create'])->name('stores.create');
        Route::post('stores/store', [MerchantStoreController::class, 'store'])->name('stores.store');
        Route::get('stores/edit/{id}', [MerchantStoreController::class, 'edit'])->name('stores.edit');
        Route::post('stores/update/{id}', [MerchantStoreController::class, 'update'])->name('stores.update');
        Route::delete('stores/destroy', [MerchantStoreController::class, 'destroy'])->name('stores.destroy');
        Route::delete('stores/remove_image', [MerchantStoreController::class, 'removeImage'])->name('stores.remove_image');
        Route::get('stores/update_stores', [MerchantStoreController::class, 'update_stores'])->name('merchants.update_stores');
    });

    Route::get('food-categories', [FoodCategoryController::class,'index'])->name('food_categories.index');
    Route::get('food-categories/create', [FoodCategoryController::class,'create'])->name('food_categories.create');
    Route::post('food-categories/store', [FoodCategoryController::class,'store'])->name('food_categories.store');
    Route::get('food-categories/edit/{id}', [FoodCategoryController::class,'edit'])->name('food_categories.edit');
    Route::post('food-categories/update/{id}', [FoodCategoryController::class,'update'])->name('food_categories.update');
    Route::delete('food-categories/destroy/{id?}', [FoodCategoryController::class,'destroy'])->name('food_categories.destroy');
    Route::get('food-categories/select/{merchant_id?}', [FoodCategoryController::class, 'getFoodCategorySelect'])->name('food_categories.select');

    Route::get('foods', [FoodController::class,'index'])->name('foods.index');
    Route::get('foods/create', [FoodController::class,'create'])->name('foods.create');
    Route::post('foods/store', [FoodController::class,'store'])->name('foods.store');
    Route::get('foods/edit/{id}', [FoodController::class,'edit'])->name('foods.edit');
    Route::post('foods/update/{id}', [FoodController::class,'update'])->name('foods.update');
    Route::delete('foods/destroy/{id?}', [FoodController::class,'destroy'])->name('foods.destroy');

    Route::get('rooms', [RoomController::class,'index'])->name('rooms.index');
    Route::get('rooms/create', [RoomController::class,'create'])->name('rooms.create');
    Route::post('rooms/store', [RoomController::class,'store'])->name('rooms.store');
    Route::get('rooms/edit/{id}', [RoomController::class,'edit'])->name('rooms.edit');
    Route::post('rooms/update/{id}', [RoomController::class,'update'])->name('rooms.update');
    Route::delete('rooms/destroy/{id?}', [RoomController::class,'destroy'])->name('rooms.destroy');

    Route::get('rooms/lookup/{q?}', [RoomController::class,'lookup'])->name('rooms.lookup');

    Route::get('products', [ProductController::class,'index'])->name('products.index');
    Route::get('products/create', [ProductController::class,'create'])->name('products.create');
    Route::post('products/store', [ProductController::class,'store'])->name('products.store');
    Route::get('products/edit/{id}', [ProductController::class,'edit'])->name('products.edit');
    Route::post('products/update/{id}', [ProductController::class,'update'])->name('products.update');
    Route::delete('products/destroy/{id?}', [ProductController::class,'destroy'])->name('products.destroy');
    Route::delete('products/remove_image', [ProductController::class, 'removeImage'])->name('products.remove_image');

    Route::get('hotel-reservations', [HotelReservationController::class,'index'])->name('hotel_reservations.index');
    Route::get('hotel-reservations/create', [HotelReservationController::class,'create'])->name('hotel_reservations.create');
    Route::post('hotel-reservations/store', [HotelReservationController::class,'store'])->name('hotel_reservations.store');
    Route::get('hotel-reservations/edit/{id}', [HotelReservationController::class,'edit'])->name('hotel_reservations.edit');
    Route::put('hotel-reservations/update/{id}', [HotelReservationController::class,'update'])->name('hotel_reservations.update');
    Route::delete('hotel-reservations/destroy/{id}', [HotelReservationController::class,'destroy'])->name('hotel_reservations.destroy');

    Route::get('restaurant-reservations', [RestaurantReservationController::class,'index'])->name('restaurant_reservations.index');
    Route::get('restaurant-reservations/create', [RestaurantReservationController::class,'create'])->name('restaurant_reservations.create');
    Route::post('restaurant-reservations/store', [RestaurantReservationController::class,'store'])->name('restaurant_reservations.store');
    Route::get('restaurant-reservations/edit/{id}', [RestaurantReservationController::class,'edit'])->name('restaurant_reservations.edit');
    Route::put('restaurant-reservations/update/{id}', [RestaurantReservationController::class,'update'])->name('restaurant_reservations.update');
    Route::delete('restaurant-reservations/destroy/{id}', [RestaurantReservationController::class,'destroy'])->name('restaurant_reservations.destroy');

    Route::get('interests', [InterestController::class, 'list'])->name('interests.list')->can('view_interests_list');
    Route::get('interests/create', [InterestController::class, 'create'])->name('interests.create');
    Route::post('interests/store', [InterestController::class, 'store'])->name('interests.store');
    Route::get('interests/edit/{id}', [InterestController::class, 'edit'])->name('interests.edit');
    Route::post('interests/update/{id}', [InterestController::class, 'update'])->name('interests.update');
    Route::delete('interests/destroy', [InterestController::class, 'destroy'])->name('interests.destroy');

    Route::get('roles', [RoleController::class, 'list'])->name('roles.list')->can('view_roles_list');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('roles/update/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/destroy', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('permissions', [PermissionController::class, 'list'])->name('permissions.list')->can('view_permissions_list');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('permissions/edit/{id}', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('permissions/update/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/destroy', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::get('transactions', [TransactionController::class, 'list'])->name('transactions.list')->can('view_transactions_list');
    Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('transactions/store', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/edit/{id}', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::post('transactions/update/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('transactions/destroy', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('transactions/print/{id}', [TransactionController::class, 'print'])->name('transactions.print');

    Route::get('product_categories', [ProductCategoryController::class, 'list'])->name('product_categories.list')->can('view_product_categories_list');
    Route::get('product_categories/create', [ProductCategoryController::class, 'create'])->name('product_categories.create');
    Route::post('product_categories/store', [ProductCategoryController::class, 'store'])->name('product_categories.store');
    Route::get('product_categories/edit/{id}', [ProductCategoryController::class, 'edit'])->name('product_categories.edit');
    Route::post('product_categories/update/{id}', [ProductCategoryController::class, 'update'])->name('product_categories.update');
    Route::delete('product_categories/destroy', [ProductCategoryController::class, 'destroy'])->name('product_categories.destroy');

    Route::get('carts', [CartController::class, 'list'])->name('carts.list');
    Route::get('carts/create', [CartController::class, 'create'])->name('carts.create');
    Route::post('carts/store', [CartController::class, 'store'])->name('carts.store');
    Route::get('carts/edit/{id}', [CartController::class, 'edit'])->name('carts.edit');
    Route::post('carts/update/{id}', [CartController::class, 'update'])->name('carts.update');
    Route::delete('carts/destroy', [CartController::class, 'destroy'])->name('carts.destroy');

    Route::get('referrals', [ReferralController::class, 'list'])->name('referrals.list')->can('view_referrals_list')->middleware('merchant_created');
    Route::get('referrals/create', [ReferralController::class, 'create'])->name('referrals.create')->middleware('merchant_created');
    Route::post('referrals/store', [ReferralController::class, 'store'])->name('referrals.store')->middleware('merchant_created');
    Route::get('referrals/edit/{id}', [ReferralController::class, 'edit'])->name('referrals.edit')->middleware('merchant_created');
    Route::post('referrals/update/{id}', [ReferralController::class, 'update'])->name('referrals.update')->middleware('merchant_created');
    Route::delete('referrals/destroy', [ReferralController::class, 'destroy'])->name('referrals.destroy');
    Route::get('referrals/generate_csv', [ReferralController::class, 'generateCSV'])->name('referrals.generate_csv');
    Route::get('referrals/tour_reservations/{code}', [ReferralController::class, 'getReservationsByRefCode'])->name('referrals.tour_reservations.list');

    Route::get('promo_codes', [PromoCodeController::class, 'list'])->name('promo_codes.list')->can('view_promo_codes_list');
    Route::get('promo_codes/create', [PromoCodeController::class, 'create'])->name('promo_codes.create');
    Route::post('promo_codes/store', [PromoCodeController::class, 'store'])->name('promo_codes.store');
    Route::get('promo_codes/edit/{id}', [PromoCodeController::class, 'edit'])->name('promo_codes.edit');
    Route::post('promo_codes/update/{id}', [PromoCodeController::class, 'update'])->name('promo_codes.update');
    Route::delete('promo_codes/destroy', [PromoCodeController::class, 'destroy'])->name('promo_codes.destroy');

    Route::get('ticket_passes', [TicketPassController::class, 'list'])->name('ticket_passes.list');
    Route::get('ticket_passes/create', [TicketPassController::class, 'create'])->name('ticket_passes.create');
    Route::post('ticket_passes/store', [TicketPassController::class, 'store'])->name('ticket_passes.store');
    Route::get('ticket_passes/edit/{id}', [TicketPassController::class, 'edit'])->name('ticket_passes.edit');
    Route::post('ticket_passes/update/{id}', [TicketPassController::class, 'update'])->name('ticket_passes.update');
    Route::delete('ticket_passes/destroy', [TicketPassController::class, 'destroy'])->name('ticket_passes.destroy');

    Route::get('logs', [LogController::class, 'list'])->name('logs.list');
    Route::get('logs/create', [LogController::class, 'create'])->name('logs.create');
    Route::post('logs/store', [LogController::class, 'store'])->name('logs.store');
    Route::get('logs/edit/{id}', [LogController::class, 'edit'])->name('logs.edit');
    Route::post('logs/update/{id}', [LogController::class, 'update'])->name('logs.update');
    Route::delete('logs/destroy', [LogController::class, 'destroy'])->name('logs.destroy');

    Route::get('announcements', [AnnouncementController::class, 'list'])->name('announcements.list');
    Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create')->can('create_announcement');
    Route::post('announcements/store', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('announcements/edit/{id}', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::post('announcements/update/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('announcements/destroy', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('unavailable_dates', [TourUnavailableDateController::class, 'list'])->name('unavailable_dates.list');
    Route::get('unavailable_dates/create', [TourUnavailableDateController::class, 'create'])->name('unavailable_dates.create')->can('create_announcement');
    Route::post('unavailable_dates/store', [TourUnavailableDateController::class, 'store'])->name('unavailable_dates.store');
    Route::get('unavailable_dates/edit/{id}', [TourUnavailableDateController::class, 'edit'])->name('unavailable_dates.edit');
    Route::post('unavailable_dates/update/{id}', [TourUnavailableDateController::class, 'update'])->name('unavailable_dates.update');
    Route::delete('unavailable_dates/destroy', [TourUnavailableDateController::class, 'destroy'])->name('unavailable_dates.destroy');

    Route::prefix('reports')->as('reports.')->group(function () {
        Route::get('user_demographics', [DataReportController::class, 'user_demographics'])->name('user_demographics');
        Route::get('sales_report', [DataReportController::class, 'sales_report'])->name('sales_report')->can('view_sales_report');
        Route::get('tour_reservations_report', [DataReportController::class, 'tour_reservations_report'])->name('tour_reservations_report');
        Route::get('tour_reservations_report/get_data', [DataReportController::class, 'getTourReservationData'])->name('tour_reservations_report.get_data');

        Route::get('get_profit', [DataReportController::class, 'getCurrentMonthProfit'])->name('get_profit')->can('view_sales_report');
        Route::get('get_top_selling_tours', [DataReportController::class, 'getTopSellingTours'])->name('get_top_selling_tours');
        Route::get('get_total_bookings_per_type', [DataReportController::class, 'getTotalBookingsPerType'])->name('get_total_bookings_per_type')->can('view_sales_report');
        Route::get('get_overall_sales', [DataReportController::class, 'getSalesData'])->name('get_overall_sales')->can('view_sales_report');
        Route::get('get_transaction_status_data', [DataReportController::class, 'getTransactionStatusData'])->name('get_transaction_status_data')->can('view_sales_report');

    });
});
