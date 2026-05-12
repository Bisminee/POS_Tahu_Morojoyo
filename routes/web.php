<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [GuestController::class, 'home'])->name('home');

Route::get('/menu', [GuestController::class, 'menu'])->name('menu');

Route::get('/about', [GuestController::class, 'about'])->name('about');

Route::get('/contact', [GuestController::class, 'contact'])->name('contact');
