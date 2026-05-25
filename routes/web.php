<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;

// Guest Routes (Hanya untuk pengguna yang BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes (Hanya untuk pengguna yang SUDAH login)
Route::middleware('auth')->group(function () {
    Route::get('/', [NoteController::class, 'index'])->name('home');
    Route::resource('notes', NoteController::class)->except(['create', 'show', 'edit']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
