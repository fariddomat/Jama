<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\BarcodeController;
use App\Http\Controllers\Dashboard\ImageGalleryController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\SiteController;
use App\Livewire\Dashboard as LivewireDashboard;
use App\Livewire\OrderStatusUpdater;
use App\Livewire\UserForm;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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

Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');

    return "Cleared!";
});

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');


Route::middleware(['web'])->group(function () {

    Route::get('/dashboard', LivewireDashboard::class)->middleware('auth')->name('dashboard');


    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');
});

Route::get('/barcode', [BarcodeController::class, 'index']);
Route::post('/barcode/submit', [BarcodeController::class, 'store']);
Route::prefix('dashboard')
    ->name('dashboard.')
    ->middleware(['auth', 'role:superadministrator|admin|delivery_agent|merchant'])
    ->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/order-status', OrderStatusUpdater::class)->name('order-status');
        Route::get('/delivery-agent-dashboard', \App\Livewire\DeliveryAgentDashboard::class)
            ->middleware(['auth', 'role:delivery_agent'])
            ->name('delivery-agent-dashboard');

        // routes/web.php


        Route::resource('/customers', \App\Http\Controllers\Dashboard\CustomerController::class);
        Route::post('/customers/{id}/restore', [\App\Http\Controllers\Dashboard\CustomerController::class, 'restore'])->name('customers.restore');
        Route::resource('/statuses', \App\Http\Controllers\Dashboard\StatusController::class);
        Route::post('/statuses/{id}/restore', [\App\Http\Controllers\Dashboard\StatusController::class, 'restore'])->name('statuses.restore');
        Route::resource('/items', \App\Http\Controllers\Dashboard\ItemController::class);
        Route::post('/items/{id}/restore', [\App\Http\Controllers\Dashboard\ItemController::class, 'restore'])->name('items.restore');
        Route::get('/orders/import', [App\Http\Controllers\Dashboard\OrderController::class, 'import'])->name('orders.import');
        Route::post('/orders/import', [App\Http\Controllers\Dashboard\OrderController::class, 'importStore'])->name('orders.import.store');
        Route::resource('/orders', \App\Http\Controllers\Dashboard\OrderController::class);
        Route::post('/orders/{id}/restore', [\App\Http\Controllers\Dashboard\OrderController::class, 'restore'])->name('orders.restore');
    });

require __DIR__ . '/auth.php';
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
    Route::resource('/order_images', \App\Http\Controllers\Dashboard\OrderImageController::class);
    Route::post('/order_images/{id}/restore', [\App\Http\Controllers\Dashboard\OrderImageController::class, 'restore'])->name('order_images.restore');
});
