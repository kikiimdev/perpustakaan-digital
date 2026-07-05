<?php

use App\Enums\Peran;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\RiwayatBaca;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guest is redirected to login', function () {
    get('/admin/dasbor')->assertRedirect('/login');
});

test('non-admin gets 403', function () {
    $user = User::factory()->create(['peran' => Peran::User]);

    actingAs($user)->get('/admin/dasbor')->assertForbidden();
});

test('admin can view dashboard and returns correct props', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    actingAs($admin)->get('/admin/dasbor')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Dasbor/Index')
            ->has('bulanAwal')
            ->has('bulanAkhir')
            ->has('riwayatBacaPerHari')
            ->has('bukuTerbanyakDibaca')
            ->has('riwayatBacaPerGender')
            ->has('kategoriTerbanyakDibaca')
        );
});

test('bulan_awal and bulan_akhir query params filter data correctly', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    $buku = Buku::factory()->create(['judul' => 'Buku A']);

    // Data outside period
    RiwayatBaca::factory()->create([
        'buku_id' => $buku->id,
        'tanggal' => Carbon::parse('2026-05-15'),
        'halaman_dibaca' => 10,
    ]);

    // Data inside period
    RiwayatBaca::factory()->create([
        'buku_id' => $buku->id,
        'tanggal' => Carbon::parse('2026-06-15'),
        'halaman_dibaca' => 20,
    ]);

    actingAs($admin)->get('/admin/dasbor?bulan_awal=2026-06&bulan_akhir=2026-06')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('bulanAwal', '2026-06')
            ->where('bulanAkhir', '2026-06')
            ->where('bukuTerbanyakDibaca.0.judul', 'Buku A')
            ->where('bukuTerbanyakDibaca.0.total_halaman', 20) // Only the one inside period
        );
});

test('riwayat baca per hari groups data by date, orders chronologically, and fills date gaps', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    $bulan = '2026-06';
    $day1 = Carbon::parse("{$bulan}-05");
    $day2 = Carbon::parse("{$bulan}-15");

    RiwayatBaca::factory()->count(3)->create(['tanggal' => $day2]);
    RiwayatBaca::factory()->count(2)->create(['tanggal' => $day1]);

    actingAs($admin)->get("/admin/dasbor?bulan_awal={$bulan}&bulan_akhir={$bulan}")
        ->assertInertia(fn (Assert $page) => $page
            ->has('riwayatBacaPerHari', 30) // June has 30 days
            ->where('riwayatBacaPerHari.0.tanggal', '2026-06-01')
            ->where('riwayatBacaPerHari.0.total', 0)
            ->where('riwayatBacaPerHari.4.tanggal', '2026-06-05') // Index 4 is the 5th
            ->where('riwayatBacaPerHari.4.total', 2)
            ->where('riwayatBacaPerHari.14.tanggal', '2026-06-15') // Index 14 is the 15th
            ->where('riwayatBacaPerHari.14.total', 3)
            ->where('riwayatBacaPerHari.29.tanggal', '2026-06-30')
            ->where('riwayatBacaPerHari.29.total', 0)
        );
});

test('riwayat_baca_per_gender splits data correctly', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    $laki = User::factory()->lakiLaki()->create();
    $perempuan = User::factory()->perempuan()->create();
    $anonim = User::factory()->create(['jenis_kelamin' => null]);

    // Laki-laki: 2 sessions
    RiwayatBaca::factory()->count(2)->create(['user_id' => $laki->id, 'tanggal' => now()]);
    // Perempuan: 1 session
    RiwayatBaca::factory()->create(['user_id' => $perempuan->id, 'tanggal' => now()]);
    // Tidak diketahui: 3 sessions
    RiwayatBaca::factory()->count(3)->create(['user_id' => $anonim->id, 'tanggal' => now()]);

    $bulan = now()->format('Y-m');

    actingAs($admin)->get("/admin/dasbor?bulan_awal={$bulan}&bulan_akhir={$bulan}")
        ->assertInertia(fn (Assert $page) => $page
            ->has('riwayatBacaPerGender', 3)
            ->where('riwayatBacaPerGender', fn ($data) => collect($data)->firstWhere('gender', 'tidak_diketahui')['total'] === 3)
            ->where('riwayatBacaPerGender', fn ($data) => collect($data)->firstWhere('gender', 'laki-laki')['total'] === 2)
            ->where('riwayatBacaPerGender', fn ($data) => collect($data)->firstWhere('gender', 'perempuan')['total'] === 1)
        );
});

test('buku terbanyak dibaca is ordered by total halaman', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    $buku1 = Buku::factory()->create(['judul' => 'Buku Sikit']);
    $buku2 = Buku::factory()->create(['judul' => 'Buku Banyak']);

    RiwayatBaca::factory()->create(['buku_id' => $buku1->id, 'halaman_dibaca' => 10, 'tanggal' => now()]);
    RiwayatBaca::factory()->create(['buku_id' => $buku2->id, 'halaman_dibaca' => 50, 'tanggal' => now()]);

    $bulan = now()->format('Y-m');

    actingAs($admin)->get("/admin/dasbor?bulan_awal={$bulan}&bulan_akhir={$bulan}")
        ->assertInertia(fn (Assert $page) => $page
            ->has('bukuTerbanyakDibaca', 2)
            ->where('bukuTerbanyakDibaca.0.judul', 'Buku Banyak')
            ->where('bukuTerbanyakDibaca.1.judul', 'Buku Sikit')
        );
});

test('kategori terbanyak dibaca is ordered by total dibaca', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    $kat1 = Kategori::factory()->create(['nama' => 'Sains']);
    $kat2 = Kategori::factory()->create(['nama' => 'Fiksi']);

    $buku1 = Buku::factory()->create();
    $buku1->kategori()->attach($kat1->id);

    $buku2 = Buku::factory()->create();
    $buku2->kategori()->attach($kat2->id);

    // Sains read 1 time
    RiwayatBaca::factory()->create(['buku_id' => $buku1->id, 'tanggal' => now()]);
    // Fiksi read 3 times
    RiwayatBaca::factory()->count(3)->create(['buku_id' => $buku2->id, 'tanggal' => now()]);

    $bulan = now()->format('Y-m');

    actingAs($admin)->get("/admin/dasbor?bulan_awal={$bulan}&bulan_akhir={$bulan}")
        ->assertInertia(fn (Assert $page) => $page
            ->has('kategoriTerbanyakDibaca', 2)
            ->where('kategoriTerbanyakDibaca.0.nama', 'Fiksi')
            ->where('kategoriTerbanyakDibaca.0.total_dibaca', 3)
            ->where('kategoriTerbanyakDibaca.1.nama', 'Sains')
            ->where('kategoriTerbanyakDibaca.1.total_dibaca', 1)
        );
});
