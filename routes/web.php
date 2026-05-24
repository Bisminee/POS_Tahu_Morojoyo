<?php

use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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