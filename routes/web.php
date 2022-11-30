<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Auth\GoogleController;

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

Route::get('/', [DefaultController::class, 'index']);
Route::get('/auth/index', [DefaultController::class, 'authIndex'])->name('auth.index');
Route::get('/auth/logout', [DefaultController::class, 'logout'])->name('auth.logout');
 
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DefaultController::class, 'dashboard'])->name('dashboard');
    Route::get('/stats', [DefaultController::class, 'stats'])->name('stats');
    Route::prefix('/subscription')->name('subscription.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::get('payment-token', [SubscriptionController::class, 'paymentToken'])->name('payment.token');
        Route::post('pay', [SubscriptionController::class, 'pay'])->name('pay');
        Route::get('cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    });
});