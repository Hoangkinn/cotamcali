<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeliveryHistoryController;

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

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('checkLogin')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('welcome');

    Route::middleware('role:1')->group(function () {
        //Account
        Route::get('/account', [AccountController::class, 'index'])->name('account');
        Route::get('/them-moi-account', [AccountController::class, 'create'])->name('addaccount');
        Route::post('/them-moi-account', [AccountController::class, 'store'])->name('account.store');
        Route::get('/account/{id}/edit', [AccountController::class, 'edit'])->name('editaccount');
        Route::put('/account/{id}', [AccountController::class, 'update'])->name('account.update');
        Route::delete('/account/{id}', [AccountController::class, 'destroy'])->name('account.destroy');
    });

    Route::middleware('role:1,2')->group(function () {
        //khách hàng
        Route::get('/khach-hang', [CustomerController::class, 'index'])->name('khachhang');
        Route::get('/khachhang/communes/{districtId}', [CustomerController::class, 'getCommunes']);
        Route::get('/khachhang/districts/{provinceId}', [CustomerController::class, 'getDistricts']);
        Route::get('/them-moi-khach-hang', [CustomerController::class, 'create'])->name('addkhachhang');
        Route::post('/them-moi-khach-hang', [CustomerController::class, 'store'])->name('khachhang.store');
        Route::get('/khachhang/{id}/edit', [CustomerController::class, 'edit'])->name('editkhachhang');
        Route::put('/khachhang/{id}', [CustomerController::class, 'update'])->name('khachhang.update');
        Route::delete('/khachhang/{id}', [CustomerController::class, 'destroy'])->name('khachhang.destroy');
    
        //combo
        Route::get('/combo', [ComboController::class, 'index'])->name('combo');
        Route::get('/them-moi-combo', [ComboController::class, 'create'])->name('addcombo');
        Route::post('/them-moi-combo', [ComboController::class, 'store'])->name('combo.store');
        Route::get('/combo/{id}/edit', [ComboController::class, 'edit'])->name('editcombo');
        Route::put('/combo/{id}', [ComboController::class, 'update'])->name('combo.update');
        Route::delete('/combo/{id}', [ComboController::class, 'destroy'])->name('combo.destroy');
    });

    Route::middleware('role:1,2,3')->group(function () {
        //đơn hàng
        Route::get('/don-hang', [OrderController::class, 'index'])->name('donhang');
        Route::post('/assign-shipper', [OrderController::class, 'assignShipper'])->name('assign.shipper');


        //vận chuyển
        Route::get('/van-chuyen', [TransportController::class, 'index'])->name('vanchuyen');
        Route::post('/orders/update-status', [TransportController::class, 'updateStatus'])
            ->name('orders.updateStatus');

        //thông báo
        Route::get('/thong-bao', [NotificationController::class, 'index'])->name('thongbao');

        //lịch sử
        Route::get('/lich-su', [DeliveryHistoryController::class, 'index'])->name('lichsu');
    });
});