<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BukuController;
use App\Http\Controllers\API\JenisBukuController;
use App\Http\Controllers\API\PerpustakaanController; // Asumsi controller login kamu

// ==================== 1. RUTE PUBLIK (TANPA MIDDLEWARE AUTH) ====================
// Anggota & Flutter Web dapat mengakses ini secara bebas tanpa memicu error 401
Route::get('/buku', [BukuController::class, 'index']);
Route::get('/buku/{id}', [BukuController::class, 'show']);
Route::get('/jenis-buku', [JenisBukuController::class, 'index']);

Route::post('/login', [PerpustakaanController::class, 'login']); // Endpoint login harus publik


// ==================== 2. RUTE TERPROTEKSI (KHUSUS PETUGAS) ====================
// Hanya bisa diakses jika menyertakan Token Bearer Sanctum yang valid
Route::middleware('auth:sanctum')->group(function () {
    
    // Proses tulis data pusat (FR-02, FR-04)
    Route::post('/buku', [BukuController::class, 'store']);
    Route::put('/buku/{id}', [BukuController::class, 'update']);
    Route::delete('/buku/{id}', [BukuController::class, 'destroy']);
    
});