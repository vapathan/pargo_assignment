<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ExchangeRateController;
use Illuminate\Support\Facades\Route;

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
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'authenticate'])->name('login');
Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('register');

Route::group(['middleware' => ['auth']], function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', [ExchangeRateController::class, 'home'])
        ->name('home');
    Route::get('exchange-rates', [
        ExchangeRateController::class, 'getRates'
    ])->name('get_exchange_rates');
    Route::get('exchange-rates-from-history/{id}', [
        ExchangeRateController::class, 'getRatesFromHistory'
    ])->name('get_exchange_rates_from_history');
    Route::post('save-rates-history', [ExchangeRateController::class, 'saveRatesHistory'])
        ->name('save_rates_history');
    Route::post('delete-rates-history/{id}', [ExchangeRateController::class, 'deleteRatesHistory'])
        ->name('delete_rates_history');
});

