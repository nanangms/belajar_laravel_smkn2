<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PendaftaranController;


Route::get('/', [FrontendController::class, 'index']);
Route::get('/news', [FrontendController::class, 'news']);
Route::get('/galeri', [FrontendController::class, 'galeri']);
Route::get('/tentang', [FrontendController::class, 'tentang']);
Route::get('/pendaftaran', [FrontendController::class, 'pendaftaran'])->name('pendaftaran');
Route::post('/pendaftaran_submit', [PendaftaranController::class, 'pendaftaran_submit'])
    ->name('pendaftaran.submit');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('berita', BeritaController::class);
    Route::resource('kategori', KategoriController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
