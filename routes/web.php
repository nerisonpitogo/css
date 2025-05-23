<?php

use App\Http\Controllers\ReportController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Artisan;
use App\Livewire\LibRegion\LibRegions;

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


// reidrect main url to /dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});



// group all routes that require authentication
Route::group(['middleware' => 'auth'], function () {
    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/permissions', 'rbac.permissions')->name('permissions')->middleware('can:Manage Settings');
    Volt::route('/roles', 'rbac.roles')->name('roles')->middleware('can:Manage Settings');
    Volt::route('/users', 'users')->name('users')->middleware('can:Manage Users');
    Route::view('/profile', 'profile')->name('profile');

    Volt::route('/offices', 'offices')->name('offices');
    Volt::route('/libservices', 'lib_service.lib-services')->name('libservices')->middleware('can:Manage Settings');
    Volt::route('/officeservices/{office_id}', 'office_service.office-services')->name('officeservices');
    Volt::route('/sqds', 'sqd.sqds')->name('sqds');
    Volt::route('/libregions', 'lib_region.lib-regions')->name('libregions')->middleware('can:Manage Settings');
    // Volt::route('/report', 'report')->name('report');


    // reports
    Route::get('/report', [ReportController::class, 'generate_report'])->name('report');
});

Volt::route('/form/{is_onsite}/{with_sub}/{is_external}/{office_id}', 'csm-form')->name('csmform');

// remigrate database
Route::get('/remigrate', function () {
    Artisan::call('migrate:refresh');
    return 'Database remigrated';
});






require __DIR__ . '/auth.php';
