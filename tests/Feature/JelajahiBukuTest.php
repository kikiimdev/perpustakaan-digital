<?php

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penulis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('guest is redirected to login', function () {
    $this->get('/app/jelajahi')
        ->assertRedirect('/login');
});

test('user can browse books', function () {
    $user = User::factory()->create();
    Buku::factory()->count(3)->create();

    $this->actingAs($user)
        ->get('/app/jelajahi')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('App/Jelajahi/Index')
            ->has('buku.data', 3)
        );
});

test('user can search by book title', function () {
    $user = User::factory()->create();
    Buku::factory()->create(['judul' => 'Buku Pemrograman PHP']);
    Buku::factory()->create(['judul' => 'Belajar Laravel']);

    $this->actingAs($user)
        ->get('/app/jelajahi?cari=PHP')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('buku.data', 1)
            ->where('buku.data.0.judul', 'Buku Pemrograman PHP')
        );
});

test('user can search by author name', function () {
    $user = User::factory()->create();

    $penulis = Penulis::factory()->create(['nama' => 'Taylor Otwell']);
    Buku::factory()->create([
        'judul' => 'Membangun Aplikasi',
        'penulis_id' => $penulis->id,
    ]);

    Buku::factory()->create(['judul' => 'Buku Lain']);

    $this->actingAs($user)
        ->get('/app/jelajahi?cari=Taylor')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('buku.data', 1)
            ->where('buku.data.0.judul', 'Membangun Aplikasi')
        );
});

test('user can filter by category', function () {
    $user = User::factory()->create();

    $kategori = Kategori::factory()->create();
    $bukuCocok = Buku::factory()->create(['judul' => 'Buku Kategori Cocok']);
    $bukuCocok->kategori()->attach($kategori->id);

    Buku::factory()->create(['judul' => 'Buku Kategori Lain']);

    $this->actingAs($user)
        ->get('/app/jelajahi?kategori='.$kategori->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('buku.data', 1)
            ->where('buku.data.0.judul', 'Buku Kategori Cocok')
        );
});

test('user can search and filter simultaneously', function () {
    $user = User::factory()->create();

    $kategori = Kategori::factory()->create();
    $penulis = Penulis::factory()->create(['nama' => 'Taylor Otwell']);

    // Cocok search author dan kategori
    $bukuCocok = Buku::factory()->create([
        'judul' => 'Buku Kategori Cocok',
        'penulis_id' => $penulis->id,
    ]);
    $bukuCocok->kategori()->attach($kategori->id);

    // Cocok search author, beda kategori
    Buku::factory()->create([
        'judul' => 'Buku Taylor Lain',
        'penulis_id' => $penulis->id,
    ]);

    $this->actingAs($user)
        ->get('/app/jelajahi?cari=Taylor&kategori='.$kategori->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('buku.data', 1)
            ->where('buku.data.0.judul', 'Buku Kategori Cocok')
        );
});

test('user can filter by author dropdown', function () {
    $user = User::factory()->create();

    $penulis1 = Penulis::factory()->create();
    $penulis2 = Penulis::factory()->create();

    Buku::factory()->create([
        'judul' => 'Buku Penulis 1',
        'penulis_id' => $penulis1->id,
    ]);

    Buku::factory()->create([
        'judul' => 'Buku Penulis 2',
        'penulis_id' => $penulis2->id,
    ]);

    $this->actingAs($user)
        ->get('/app/jelajahi?penulis='.$penulis1->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('buku.data', 1)
            ->where('buku.data.0.judul', 'Buku Penulis 1')
        );
});

test('user can filter by author, category, and search simultaneously', function () {
    $user = User::factory()->create();

    $kategori = Kategori::factory()->create();
    $penulis1 = Penulis::factory()->create(['nama' => 'Taylor Otwell']);
    $penulis2 = Penulis::factory()->create(['nama' => 'Caleb Porzio']);

    // Target buku (Penulis 1, Kategori A, Judul match)
    $bukuCocok = Buku::factory()->create([
        'judul' => 'Belajar Laravel Spesifik',
        'penulis_id' => $penulis1->id,
    ]);
    $bukuCocok->kategori()->attach($kategori->id);

    // Salah kategori
    Buku::factory()->create([
        'judul' => 'Belajar Laravel Lain',
        'penulis_id' => $penulis1->id,
    ]);

    // Salah penulis
    $bukuSalahPenulis = Buku::factory()->create([
        'judul' => 'Belajar Laravel Juga',
        'penulis_id' => $penulis2->id,
    ]);
    $bukuSalahPenulis->kategori()->attach($kategori->id);

    $this->actingAs($user)
        ->get('/app/jelajahi?cari=Laravel&kategori='.$kategori->id.'&penulis='.$penulis1->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('buku.data', 1)
            ->where('buku.data.0.judul', 'Belajar Laravel Spesifik')
        );
});

test('non-matching search returns empty', function () {
    $user = User::factory()->create();
    Buku::factory()->create(['judul' => 'Belajar Laravel']);

    $this->actingAs($user)
        ->get('/app/jelajahi?cari=TidakAda')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('buku.data', 0)
        );
});
