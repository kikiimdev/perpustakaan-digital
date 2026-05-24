<?php

use App\Enums\Peran;
use App\Models\Buku;
use App\Models\BukuFavorit;
use App\Models\Penulis;
use App\Models\RiwayatBaca;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);
    $this->admin = User::factory()->create(['peran' => Peran::Admin]);
    $this->penulis = Penulis::create(['nama' => 'Report Author']);
});

test('laporan shows monthly book count', function () {
    Buku::create([
        'penulis_id' => $this->penulis->id,
        'judul' => 'January Book',
        'sinopsis' => '...',
        'file_pdf' => 'buku_pdf/a.pdf',
        'jumlah_halaman' => 10,
    ]);

    $response = $this->actingAs($this->admin)
        ->get('/admin/laporan?bulan=' . now()->month . '&tahun=' . now()->year);

    $response->assertOk();
    $data = $response->getOriginalContent()->getData();
    expect($data['page']['props']['bukuDitambahkan'])->toBe(1);
});

test('laporan shows most read books', function () {
    $user = User::factory()->create(['peran' => Peran::User]);
    $buku = Buku::create([
        'penulis_id' => $this->penulis->id,
        'judul' => 'Most Read',
        'sinopsis' => '...',
        'file_pdf' => 'buku_pdf/b.pdf',
        'jumlah_halaman' => 10,
    ]);

    RiwayatBaca::create([
        'user_id' => $user->id,
        'buku_id' => $buku->id,
        'halaman_dibaca' => 50,
        'tanggal' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get('/admin/laporan?bulan=' . now()->month . '&tahun=' . now()->year);

    $response->assertOk();
    $data = $response->getOriginalContent()->getData();
    expect($data['page']['props']['bukuTerbanyakDibaca'])->toHaveCount(1);
    expect($data['page']['props']['bukuTerbanyakDibaca'][0]['judul'])->toBe('Most Read');
});

test('laporan shows most favorited books', function () {
    $user = User::factory()->create(['peran' => Peran::User]);
    $buku = Buku::create([
        'penulis_id' => $this->penulis->id,
        'judul' => 'Most Fav',
        'sinopsis' => '...',
        'file_pdf' => 'buku_pdf/c.pdf',
        'jumlah_halaman' => 10,
    ]);

    BukuFavorit::create([
        'user_id' => $user->id,
        'buku_id' => $buku->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->get('/admin/laporan');

    $response->assertOk();
    $data = $response->getOriginalContent()->getData();
    expect($data['page']['props']['bukuTerfavorit'])->toHaveCount(1);
    expect($data['page']['props']['bukuTerfavorit'][0]['judul'])->toBe('Most Fav');
});
