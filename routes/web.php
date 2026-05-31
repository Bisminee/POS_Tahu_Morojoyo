<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

// ── Guest only ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [CashierController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CashierController::class, 'login'])->name('cashier.login.submit');
});

// ── Auth ──
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [CashierController::class, 'logout'])->name('logout');

    // Absensi - TANPA CheckAttendance
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/absensi/pilih-shift', [AttendanceController::class, 'selectShift'])->name('attendance.select-shift');
    Route::post('/absensi/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/absensi/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    // HAPUS duplikat ini ↓
    // Route::get('/absensi/verified', ...)

    // Face setup
    Route::get('/absensi/save-face', [AttendanceController::class, 'showSaveFace'])->name('attendance.show-save-face');
    Route::post('/absensi/save-face', [AttendanceController::class, 'saveFaceData'])->name('attendance.save-face');

    // Owner face
    Route::post('/owner/karyawan/{karyawan}/face', [AttendanceController::class, 'saveFaceDataForKaryawan'])
        ->name('owner.karyawan.save-face');

    // Pilih shift
    Route::get('/cashier/select-shift', [CashierController::class, 'showShiftSelection'])->name('cashier.select-shift');
    Route::post('/cashier/select-shift', [CashierController::class, 'selectShift'])->name('cashier.select-shift.submit');

    // ── CheckAttendance middleware ──
    Route::middleware(\App\Http\Middleware\CheckAttendance::class)->group(function () {
        Route::get('/cashier/pos', [CashierController::class, 'pos'])->name('cashier.pos');
        Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.pos.checkout');
        Route::post('/cashier/sync-sheets', [CashierController::class, 'syncToSheets'])->name('cashier.sync-sheets');

        // attendance.verified di sini saja — SATU definisi
        Route::get('/absensi/verified', [AttendanceController::class, 'afterVerification'])
            ->name('attendance.verified');
    });

    // Owner
    Route::get('/owner/absensi', [AttendanceController::class, 'ownerDashboard'])->name('attendance.owner');
    Route::get('/owner/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/owner/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::delete('/owner/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    Route::get('/owner/karyawan/{karyawan}/face', function (\App\Models\Karyawan $karyawan) {
        if (auth()->user()->role !== 'owner') abort(403);
        return view('owner.karyawan-face', compact('karyawan'));
    })->name('owner.karyawan.face');

    Route::post('/owner/karyawan/{karyawan}/face', [AttendanceController::class, 'saveFaceDataForKaryawan'])
        ->name('owner.karyawan.save-face');

    Route::post('/cashier/create-spreadsheet', [CashierController::class, 'createSpreadsheet'])
        ->name('cashier.create-spreadsheet');
});
