<?php

use App\Http\Controllers\BukuPreviewController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/buku/{buku}', [BukuPreviewController::class, 'show'])->name('buku.preview');

require __DIR__.'/settings.php';
require __DIR__.'/super-admin.php';
require __DIR__.'/admin.php';
require __DIR__.'/app.php';
