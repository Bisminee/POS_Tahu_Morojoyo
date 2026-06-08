<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

// ── Guest pages ─────────────────────────────────────────────────────────────
Route::get('/', [GuestController::class, 'home'])->name('home');
Route::get('/menu', [GuestController::class, 'menu'])->name('menu');
Route::get('/about', [GuestController::class, 'about'])->name('about');
Route::get('/contact', [GuestController::class, 'contact'])->name('contact');

// ── Guest only ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [CashierController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CashierController::class, 'login'])->name('cashier.login.submit');
});

// ── Authenticated ───────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [CashierController::class, 'logout'])->name('logout');

    // ── Absensi kasir (pre-shift) ─────────────────────────────────────────────
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/absensi/select-employee', [AttendanceController::class, 'selectEmployee'])->name('attendance.select-employee');
    Route::post('/absensi/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/absensi/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    Route::get('/absensi/verified', [AttendanceController::class, 'afterVerification'])->name('attendance.verified');

    // ── Face setup (kasir) ───────────────────────────────────────────────────
    Route::get('/absensi/save-face', [AttendanceController::class, 'showSaveFace'])->name('attendance.show-save-face');
    Route::post('/absensi/save-face', [AttendanceController::class, 'saveFaceData'])->name('attendance.save-face');

    // ── Middleware untuk POS: hanya boleh masuk setelah check-in ─────────────
    Route::middleware('attendance.checkedin')->group(function () {
        Route::get('/cashier/pos', [CashierController::class, 'pos'])->name('cashier.pos');
        Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.pos.checkout');
        Route::post('/cashier/sync-sheets', [CashierController::class, 'syncToSheets'])->name('cashier.sync-sheets');
        Route::post('/cashier/create-spreadsheet', [CashierController::class, 'createSpreadsheet'])->name('cashier.create-spreadsheet');
    });

    // ── Pilih shift (kasir) ──────────────────────────────────────────────────
    Route::get('/cashier/select-shift', [CashierController::class, 'showShiftSelection'])->name('cashier.select-shift');
    Route::post('/cashier/select-shift', [CashierController::class, 'selectShift'])->name('cashier.select-shift.submit');

    // ── Owner routes ─────────────────────────────────────────────────────────

    Route::middleware(['auth'])->group(function () {

        Route::get('/owner/karyawan', [AttendanceController::class, 'karyawanList'])
            ->name('owner.karyawan.list');

        Route::post('/owner/karyawan/{karyawan}/face', [AttendanceController::class, 'saveFaceDataForKaryawan'])
            ->name('owner.karyawan.save-face');

        Route::get('/owner/absensi', [AttendanceController::class, 'ownerDashboard'])
            ->name('attendance.owner');

        Route::get('/owner/absensi/export', [AttendanceController::class, 'exportAbsensiCsv'])
            ->name('attendance.export');
    });
});
