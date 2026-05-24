<?php

use App\Http\Controllers\App\BacaBukuController;
use App\Http\Controllers\App\PerpustakaanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'peran:super_admin,admin,user'])->prefix('app')->name('app.')->group(function () {
    Route::get('dasbor', [PerpustakaanController::class, 'index'])->name('dasbor');

    Route::get('jelajahi', [BacaBukuController::class, 'jelajahi'])->name('jelajahi');
    Route::get('favorit', [BacaBukuController::class, 'daftarFavorit'])->name('favorit');
    Route::get('markah', [BacaBukuController::class, 'daftarMarkah'])->name('markah');

    Route::get('buku/{buku}', [BacaBukuController::class, 'lihat'])->name('buku.lihat');
    Route::get('baca/{buku}', [BacaBukuController::class, 'bacaPdf'])->name('baca.pdflihat');

    Route::post('favorit', [BacaBukuController::class, 'toggleFavorit'])->name('favorit.toggle');
    Route::post('markah', [BacaBukuController::class, 'simpanMarkah'])->name('markah.simpan');
    Route::post('catat-halaman', [BacaBukuController::class, 'catatStatistik'])->name('statistik.catat');
});
