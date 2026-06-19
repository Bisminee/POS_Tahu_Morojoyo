<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\Owner\OwnerSheetsController;
use App\Http\Controllers\ShiftController;
use App\Http\Middleware\CheckAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
// Guest Pages
// ─────────────────────────────────────────────────────────────
Route::get('/', [GuestController::class, 'home'])->name('home');
Route::get('/menu', [GuestController::class, 'menu'])->name('menu');
Route::get('/about', [GuestController::class, 'about'])->name('about');
Route::get('/contact', [GuestController::class, 'contact'])->name('contact');

// ─────────────────────────────────────────────────────────────
// Login Kasir
// ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [CashierController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CashierController::class, 'login'])->name('cashier.login.submit');
});

// ─────────────────────────────────────────────────────────────
// Auth Routes
// ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [CashierController::class, 'logout'])->name('logout');

    // ─────────────────────────────────────────────────────────
    // Absensi Kasir
    // ─────────────────────────────────────────────────────────
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/absensi/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/absensi/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    // ─────────────────────────────────────────────────────────
    // Pilih Shift Kasir
    // ─────────────────────────────────────────────────────────
    Route::get('/cashier/select-shift', [CashierController::class, 'showShiftSelection'])->name('cashier.select-shift');
    Route::post('/cashier/select-shift', [CashierController::class, 'selectShift'])->name('cashier.select-shift.submit');

    // ─────────────────────────────────────────────────────────
    // POS Kasir
    // POS wajib sudah ada absensi Face ID aktif
    // ─────────────────────────────────────────────────────────
    Route::middleware(CheckAttendance::class)->group(function () {
        Route::get('/cashier/pos', [CashierController::class, 'pos'])->name('cashier.pos');
        Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.pos.checkout');
        Route::post('/cashier/sync-sheets', [CashierController::class, 'syncToSheets'])->name('cashier.sync-sheets');
        Route::post('/cashier/create-spreadsheet', [CashierController::class, 'createSpreadsheet'])->name('cashier.create-spreadsheet');
    });

    // ─────────────────────────────────────────────────────────
    // Owner - Google Sheets & Laporan Keuangan
    // URL tetap /admin/... supaya cocok dengan menu Filament
    // ─────────────────────────────────────────────────────────
    Route::prefix('admin')->name('owner.')->group(function () {
        Route::get('/laporan-keuangan', [OwnerSheetsController::class, 'showLaporanKeuangan'])
            ->name('laporan-keuangan');

        Route::post('/laporan-keuangan', [OwnerSheetsController::class, 'laporanKeuangan'])
            ->name('laporan-keuangan.data');

        Route::get('/sheets', [OwnerSheetsController::class, 'index'])
            ->name('sheets.index');

        Route::get('/sheets/create', [OwnerSheetsController::class, 'create'])
            ->name('sheets.create');

        Route::post('/sheets/store', [OwnerSheetsController::class, 'createSpreadsheet'])
            ->name('sheets.store');

        Route::post('/sheets/sync', [OwnerSheetsController::class, 'sync'])
            ->name('sheets.sync');
    });

    // ─────────────────────────────────────────────────────────
    // Owner - Rekap Absensi
    // ─────────────────────────────────────────────────────────
    Route::get('/owner/absensi', [AttendanceController::class, 'ownerDashboard'])
        ->name('attendance.owner');

    Route::get('/owner/absensi/export', [AttendanceController::class, 'exportAbsensiCsv'])
        ->name('attendance.export');

    // ─────────────────────────────────────────────────────────
    // Owner - Face ID Karyawan
    // ─────────────────────────────────────────────────────────
    Route::get('/owner/karyawan', [AttendanceController::class, 'karyawanList'])
        ->name('owner.karyawan.list');

    Route::get('/owner/karyawan/{karyawan}/face', function (\App\Models\Karyawan $karyawan) {
        if (Auth::user()?->role !== 'owner') {
            abort(403);
        }

        return view('owner.karyawan-face', compact('karyawan'));
    })->name('owner.karyawan.face');

    Route::post('/owner/karyawan/{karyawan}/face', [AttendanceController::class, 'saveFaceDataForKaryawan'])
        ->name('owner.karyawan.save-face');

    // ─────────────────────────────────────────────────────────
    // Owner - Manajemen Shift
    // ─────────────────────────────────────────────────────────
    Route::get('/owner/shifts', [ShiftController::class, 'index'])
        ->name('shifts.index');

    Route::post('/owner/shifts', [ShiftController::class, 'store'])
        ->name('shifts.store');

    Route::delete('/owner/shifts/{shift}', [ShiftController::class, 'destroy'])
        ->name('shifts.destroy');
});