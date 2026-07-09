<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebPerpustakaanController;

// Route Guest (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebPerpustakaanController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebPerpustakaanController::class, 'login'])->name('login.web.post');
});

// Route Protected (Wajib Login Session Web)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebPerpustakaanController::class, 'index'])->name('dashboard.web');
    Route::post('/buku', [WebPerpustakaanController::class, 'store'])->name('buku.store.web');
    Route::post('/logout', [WebPerpustakaanController::class, 'logout'])->name('logout.web');
    Route::put('/buku/{id}', [WebPerpustakaanController::class, 'update'])->name('buku.update.web');
    Route::delete('/buku/{id}', [WebPerpustakaanController::class, 'destroy'])->name('buku.destroy.web');
});

// Redirect root '/' ke login atau dashboard otomatis
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});