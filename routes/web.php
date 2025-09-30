<?php

use App\Http\Controllers\System\CkFinderController;
use Illuminate\Support\Facades\Route;

// Import Middleware
use App\Http\Middleware\Roles\AdminAuthMiddleware;
use App\Http\Middleware\Roles\CompanyAuthMiddleware;
use App\Http\Middleware\Roles\CustomerAuthMiddleware;

// Auth Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Auth\LogoutController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WebProfileController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\DistrictTypeController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\StopController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\BusServiceController;

// Company Controllers
use App\Http\Controllers\Company\DashboardController as CompanyDashboardController;
use App\Http\Controllers\Company\ProfileController as CompanyProfileController;
use App\Http\Controllers\Company\BusController as CompanyBusController;
use App\Http\Controllers\Company\RouteController as CompanyRouteController;
use App\Http\Controllers\Company\BusRouteController as CompanyBusRouteController;
use App\Http\Controllers\Company\BookingController as CompanyBookingController;

// Client Controllers
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\RouteController as ClientRouteController;
use App\Http\Controllers\Client\CompanyController as ClientCompanyController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\PageController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES: Homepage, contact page, find bus route page (with detail information of bus and route), bus route detail page, booking page, company list, company detail page, login, register, user profile page, ...
|--------------------------------------------------------------------------
*/
Route::name('client.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/tim-kiem', [ClientRouteController::class, 'search'])->name('routes.search');
    Route::get('/tuyen-duong/{slug}', [ClientRouteController::class, 'show'])->name('routes.show');

    Route::get('/nha-xe', [ClientCompanyController::class, 'index'])->name('companies.index');
    Route::get('/nha-xe/{slug}', [ClientCompanyController::class, 'show'])->name('companies.show');

    Route::get('/dat-ve', [ClientBookingController::class, 'create'])->name('booking.create');
    Route::post('/dat-ve', [ClientBookingController::class, 'store'])->name('booking.store');
    Route::get('/dat-ve/thanh-cong', [ClientBookingController::class, 'success'])->name('booking.success');

    Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
    Route::get('/trang/{slug}', [PageController::class, 'show'])->name('page.show');

    Route::get('/dang-nhap', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [ClientAuthController::class, 'login'])->name('login.submit');
    Route::get('/dang-ky', [ClientAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/dang-ky', [ClientAuthController::class, 'register'])->name('register.submit');
    Route::post('/dang-xuat', [ClientAuthController::class, 'logout'])->name('logout');

    Route::middleware(CustomerAuthMiddleware::class)->group(function () {
        Route::get('/tai-khoan', [ClientProfileController::class, 'index'])->name('profile.index');
    });
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/
Route::get("/examples/{name}", [\App\Http\Controllers\Examples\ExamplesController::class, "index"]);
Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('authenticate', [AuthenticateController::class, 'authenticate'])->name('authenticate');
Route::get('logout', [LogoutController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(AdminAuthMiddleware::class)
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');
        Route::resource('web_profiles', WebProfileController::class);
        Route::patch('/web_profiles/{web_profile}/set-default', [WebProfileController::class, 'setDefault'])->name('web_profiles.setDefault');
        Route::resource('menus', MenuController::class)->except(['show']);
        Route::post('/menus/update-order', [MenuController::class, 'updateOrder'])->name('menus.updateOrder');
        Route::post('/menus/add-item', [MenuController::class, 'addItem'])->name('menus.addItem');
        Route::resource('provinces', ProvinceController::class);
        Route::resource('district-types', DistrictTypeController::class);
        Route::resource('districts', DistrictController::class);
        Route::post('/districts/update-order', [DistrictController::class, 'updateOrder'])->name('districts.updateOrder');
        Route::resource('stops', StopController::class);
        Route::post('/stops/update-order', [StopController::class, 'updateOrder'])->name('stops.updateOrder');
        Route::resource('routes', AdminRouteController::class);
        Route::post('/routes/update-order', [AdminRouteController::class, 'updateOrder'])->name('routes.updateOrder');
        Route::resource('companies', AdminCompanyController::class);
        Route::resource('bus-services', BusServiceController::class);
        Route::resource('bookings', AdminBookingController::class);
    });

/*
|--------------------------------------------------------------------------
| COMPANY ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('company')
    ->name('company.')
    ->middleware(CompanyAuthMiddleware::class)
    ->group(function () {
        Route::get('/dashboard', [CompanyDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/profile', [CompanyProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [CompanyProfileController::class, 'update'])->name('profile.update');
        Route::get('/buses/list', [CompanyBusController::class, 'list'])->name('buses.list');
        Route::get('/buses/all', [CompanyBusController::class, 'all'])->name('buses.all');
        Route::resource('buses', CompanyBusController::class)->except(['create']);
        Route::get('/company-routes/all', [CompanyRouteController::class, 'all'])->name('company-routes.all');
        Route::post('company-routes/update-order', [CompanyRouteController::class, 'updateOrder'])->name('company-routes.updateOrder');
        Route::resource('company-routes', CompanyRouteController::class)->except(['create']);
        Route::post('bus-routes/update-order', [CompanyBusRouteController::class, 'updateOrder'])->name('bus-routes.updateOrder');
        Route::resource('bus-routes', CompanyBusRouteController::class)->except(['create']);
        Route::get('/bookings/list', [CompanyBookingController::class, 'list'])->name('bookings.list');
        Route::get('/bookings', [CompanyBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [CompanyBookingController::class, 'show'])->name('bookings.show');
        Route::put('/bookings/{booking}/update-status', [CompanyBookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    });


/*
|--------------------------------------------------------------------------
| OTHER UTILITY ROUTES
|--------------------------------------------------------------------------
*/
Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
    ->name('ckfinder_connector')
    ->middleware('auth');

Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
    ->name('ckfinder_browser')
    ->middleware('auth');

Route::post('/ckfinder/delete-file', [CkFinderController::class, 'deleteFile'])
    ->name('ckfinder_delete_file')
    ->middleware('auth');

Route::post('/ckfinder/upload', [CkFinderController::class, 'upload'])
    ->name('ckfinder_upload')
    ->middleware('auth');
