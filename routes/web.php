<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\ImageGalleryController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\SiteController;
use App\Livewire\UserForm;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

Route::middleware(['web'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');
});

Route::prefix('dashboard')
    ->name('dashboard.')
    ->middleware(['auth', 'role:superadministrator|admin|delivery_agent|merchant'])
    ->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::resource('/customers', \App\Http\Controllers\Dashboard\CustomerController::class);
        Route::post('/customers/{id}/restore', [\App\Http\Controllers\Dashboard\CustomerController::class, 'restore'])->name('customers.restore');
        Route::resource('/statuses', \App\Http\Controllers\Dashboard\StatusController::class);
        Route::post('/statuses/{id}/restore', [\App\Http\Controllers\Dashboard\StatusController::class, 'restore'])->name('statuses.restore');
        Route::resource('/items', \App\Http\Controllers\Dashboard\ItemController::class);
        Route::post('/items/{id}/restore', [\App\Http\Controllers\Dashboard\ItemController::class, 'restore'])->name('items.restore');
    });

require __DIR__ . '/auth.php';
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('/orders', \App\Http\Controllers\Dashboard\OrderController::class);
    Route::post('/orders/{id}/restore', [\App\Http\Controllers\Dashboard\OrderController::class, 'restore'])->name('orders.restore');
});

