<?php

use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\KartuAnggotaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PenulisController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'peran:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('buku', BukuController::class);
    Route::post('penulis/cepat', [BukuController::class, 'tambahPenulisCepat'])->name('penulis.cepat');
    Route::post('kategori/cepat', [BukuController::class, 'tambahKategoriCepat'])->name('kategori.cepat');

    Route::get('penulis', [PenulisController::class, 'index'])->name('penulis.index');
    Route::get('penulis/{penulis}', [PenulisController::class, 'show'])->name('penulis.show');

    Route::get('kartu-anggota', [KartuAnggotaController::class, 'index'])->name('kartu-anggota.index');
    Route::get('kartu-anggota/cetak/{user}', [KartuAnggotaController::class, 'cetak'])->name('kartu-anggota.cetak');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/cetak-buku-ditambahkan', [LaporanController::class, 'cetakBukuDitambahkan'])->name('laporan.cetak-buku-ditambahkan');
    Route::get('laporan/cetak-buku-terbanyak-dibaca', [LaporanController::class, 'cetakBukuTerbanyakDibaca'])->name('laporan.cetak-buku-terbanyak-dibaca');
    Route::get('laporan/cetak-buku-terfavorit', [LaporanController::class, 'cetakBukuTerfavorit'])->name('laporan.cetak-buku-terfavorit');
    Route::get('laporan/cetak-aktivitas-membaca', [LaporanController::class, 'cetakAktivitasMembaca'])->name('laporan.cetak-aktivitas-membaca');
});
