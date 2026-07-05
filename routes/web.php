<?php

use App\Http\Controllers\BukuPreviewController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/buku/{buku}', [BukuPreviewController::class, 'show'])->name('buku.preview');

Route::get('/file/{path}', function ($path) {
    // get file from s3 storage
    $file = Storage::disk('s3')->get($path);

    return response($file, 200, [
        // 'Content-Type' => Storage::disk('s3')->mimeType($path),
        'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        'Content-Length' => Storage::disk('s3')->size($path),
    ]);
});

require __DIR__.'/settings.php';
require __DIR__.'/super-admin.php';
require __DIR__.'/admin.php';
require __DIR__.'/app.php';
