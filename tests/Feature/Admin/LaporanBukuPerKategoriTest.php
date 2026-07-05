<?php

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->admin = User::factory()->create(['peran' => 'admin']);
});

it('shows buku per kategori on laporan index', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Fiksi']);
    $buku = Buku::factory()->create(['created_at' => Carbon::now()]);

    $buku->kategori()->attach($kategori->id);

    $this->actingAs($this->admin)
        ->get(route('admin.laporan.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Laporan/Index')
            ->has('bukuPerKategori', 1)
            ->where('bukuPerKategori.0.nama', 'Fiksi')
            ->where('bukuPerKategori.0.buku_count', 1)
        );
});

it('can download buku per kategori pdf', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Non-Fiksi']);
    $buku = Buku::factory()->create(['created_at' => Carbon::now()]);

    $buku->kategori()->attach($kategori->id);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.laporan.cetak-buku-per-kategori'))
        ->assertOk();

    $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    $this->assertStringContainsString('laporan-buku-per-kategori', $response->headers->get('Content-Disposition'));
});
