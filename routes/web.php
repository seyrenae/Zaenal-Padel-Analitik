<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PertandinganController;

// Setup Halaman 1
Route::get('/', [PertandinganController::class, 'setup']);
Route::post('/setup', [PertandinganController::class, 'simpan']);

// Live Tracking Halaman 2
Route::get('/operator/{id}', [PertandinganController::class, 'operator']);
Route::post('/tambah-poin', [PertandinganController::class, 'tambahPoin']);
Route::post('/undo-poin', [PertandinganController::class, 'undoPoin']);

Route::get('/livestream/{id}', [PertandinganController::class, 'livestream']);


Route::get('/summary/{id}', [PertandinganController::class, 'summary']);