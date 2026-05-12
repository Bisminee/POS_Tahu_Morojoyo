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

    Route::get('/cashier/pos/laporan/pdf', [CashierController::class, 'laporanPdf'])
        ->name('cashier.pos.laporan.pdf');

    Route::get('/cashier/pos/laporan/excel', [CashierController::class, 'laporanExcel'])
        ->name('cashier.pos.laporan.excel');

    Route::post('/logout', [CashierController::class, 'logout'])
        ->name('logout');

});
