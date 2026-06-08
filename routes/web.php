<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateShiftSession;
use App\Http\Controllers\Owner\OwnerSheetsController;


Route::get('/', [GuestController::class, 'home'])->name('home');
Route::get('/menu', [GuestController::class, 'menu'])->name('menu');
Route::get('/about', [GuestController::class, 'about'])->name('about');
Route::get('/contact', [GuestController::class, 'contact'])->name('contact');

// ── Guest only ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [CashierController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CashierController::class, 'login'])->name('cashier.login.submit');
});

// ── Auth ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', ValidateShiftSession::class])->group(function () {

    Route::post('/logout', [CashierController::class, 'logout'])->name('logout');

    // ── Absensi (kasir) ──────────────────────────────────────────────────────
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/absensi/reset', [AttendanceController::class, 'resetAndGoToAbsensi'])
        ->name('attendance.reset')
        ->middleware('auth');
    Route::post('/absensi/pilih-shift', [AttendanceController::class, 'selectShift'])->name('attendance.select-shift');
    Route::post('/absensi/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/absensi/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    Route::get('/absensi/verified', [AttendanceController::class, 'afterVerification'])->name('attendance.verified');

    // ── Face setup (kasir sendiri) ───────────────────────────────────────────
    Route::get('/absensi/save-face', [AttendanceController::class, 'showSaveFace'])->name('attendance.show-save-face');
    Route::post('/absensi/save-face', [AttendanceController::class, 'saveFaceData'])->name('attendance.save-face');

    // ── Pilih shift (kasir) ──────────────────────────────────────────────────
    Route::get('/cashier/select-shift', [CashierController::class, 'showShiftSelection'])->name('cashier.select-shift');
    Route::post('/cashier/select-shift', [CashierController::class, 'selectShift'])->name('cashier.select-shift.submit');

    // ── POS — TIDAK butuh middleware CheckAttendance ─────────────────────────
    // Kasir boleh masuk POS meski belum absen
    Route::get('/cashier/pos', [CashierController::class, 'pos'])->name('cashier.pos');
    Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.pos.checkout');
    Route::post('/cashier/sync-sheets', [CashierController::class, 'syncToSheets'])->name('cashier.sync-sheets');
    Route::post('/cashier/create-spreadsheet', [CashierController::class, 'createSpreadsheet'])->name('cashier.create-spreadsheet');


    // ── Owner ────────────────────────────────────────────────────────────────

    Route::prefix('owner')->name('owner.')->middleware(['auth'])->group(function () {

        // ── Google Sheets ──
        Route::get('sheets',           [OwnerSheetsController::class, 'index'])->name('sheets.index');
        // GET untuk menampilkan form create
        Route::get('sheets/create',    [OwnerSheetsController::class, 'create'])->name('sheets.create');
        // POST untuk handle buat spreadsheet baru (AJAX dari form)
        Route::post('sheets/store',    [OwnerSheetsController::class, 'createSpreadsheet'])->name('sheets.store');
        // POST untuk sync ke sheets
        Route::post('sheets/sync',     [OwnerSheetsController::class, 'sync'])->name('sheets.sync');
    });

    // Dashboard absensi
    Route::get('/owner/absensi', [AttendanceController::class, 'ownerDashboard'])->name('attendance.owner');

    // Manajemen shift
    Route::get('/owner/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/owner/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::delete('/owner/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    // Daftar karyawan + status face
    Route::get('/owner/karyawan', [AttendanceController::class, 'karyawanList'])->name('owner.karyawan.list');

    // Face registration per karyawan
    Route::get('/owner/karyawan/{karyawan}/face', function (\App\Models\Karyawan $karyawan) {
        if (auth()->user()->role !== 'owner') {
            abort(403);
        }
        return view('owner.karyawan-face', compact('karyawan'));
    })->name('owner.karyawan.face');

    Route::post('/owner/karyawan/{karyawan}/face', [AttendanceController::class, 'saveFaceDataForKaryawan'])
        ->name('owner.karyawan.save-face');
});
