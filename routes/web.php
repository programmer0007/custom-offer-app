<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\storeController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/dashboard', function () {
    return view('welcome');
});

// @Author : Abhishek

Route::any('/', [storeController::class, 'access'])->name('app.base')->middleware('CSP');
Route::any('/installApp',[storeController::class,'access'])->name('app.access');
Route::any('auth', [storeController::class, 'auth'])->name('app.auth');
Route::any('redirect', [storeController::class, 'authCallback'])->name('app.authcallback');
Route::get('approve_charge/{shop}', [storeController::class, 'approveRecurringCharge'])->name('app.approvecharge');
Route::get('recur_accept/{id}', [storeController::class, 'recurAccept'])->name('app.recurAccept');

Route::any('welcome/{id}',[storeController::class, 'welcome']);

Route::get('redirect_approval', [storeController::class, 'redirectApproval'])->name('app.redirect_approval');
Route::any('/uninstall',[storeController::class,'uninstall'])->name('uninstall');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('CSP');


Route::post('/Createcollections', [DashboardController::class, 'Createcollections'])->name('collections');

Route::get('/get-single-discount', [DashboardController::class, 'getSingleDiscount'])->name('getSingleDiscount');
Route::post('/delete-item', [DashboardController::class, 'deleteRule'])->name('delete-item');
Route::get('/get-discount', [DashboardController::class, 'getDiscount'])->name('get-discount');




// @end Author : Abhishek

Route::get('/test', function() {
     dd('test');
});