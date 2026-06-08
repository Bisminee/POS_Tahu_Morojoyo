<?php

use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;

Route::prefix('/')->group(function () {

Route::get('/',       [App\Http\Controllers\GuestController::class, 'home'])->name('home');
Route::get('/menu',   [App\Http\Controllers\GuestController::class, 'menu'])->name('menu');
Route::get('/about',  [App\Http\Controllers\GuestController::class, 'about'])->name('about');
Route::get('/contact',[App\Http\Controllers\GuestController::class, 'contact'])->name('contact');

});

Route::middleware(['auth'])->group(function () {

    Route::get('/cashier/pos', [CashierController::class, 'pos'])
        ->name('cashier.pos');

    Route::post('/cashier/checkout', [CashierController::class, 'checkout'])
        ->name('cashier.pos.checkout');

    Route::post('/cashier/sync-sheets', [CashierController::class, 'syncToSheets'])
        ->name('cashier.sync-sheets');

    Route::post('/logout', [CashierController::class, 'logout'])
        ->name('logout');

});
