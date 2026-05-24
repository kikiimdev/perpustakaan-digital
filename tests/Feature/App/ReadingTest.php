<?php

use App\Enums\Peran;
use App\Models\Buku;
use App\Models\BukuFavorit;
use App\Models\MarkahBuku;
use App\Models\Penulis;
use App\Models\RiwayatBaca;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);
    $this->user = User::factory()->create(['peran' => Peran::User]);
    $this->penulis = Penulis::create(['nama' => 'Test Author']);
    $this->buku = Buku::create([
        'penulis_id' => $this->penulis->id,
        'judul' => 'Reading Book',
        'sinopsis' => 'A book to read.',
        'file_pdf' => 'buku_pdf/test.pdf',
        'jumlah_halaman' => 100,
    ]);
});

test('user can view book detail', function () {
    $response = $this->actingAs($this->user)
        ->get("/app/buku/{$this->buku->id}");

    $response->assertOk();
    $data = $response->getOriginalContent()->getData();
    expect($data['page']['props']['buku']['judul'])->toBe('Reading Book');
});

test('user can toggle favorite', function () {
    $response = $this->actingAs($this->user)
        ->post('/app/favorit', [
            'buku_id' => $this->buku->id,
        ]);

    $response->assertOk();
    $data = $response->json();
    expect($data['status'])->toBe('added');
    expect(BukuFavorit::where('user_id', $this->user->id)->where('buku_id', $this->buku->id)->exists())->toBeTrue();

    // Toggle off
    $response = $this->actingAs($this->user)
        ->post('/app/favorit', [
            'buku_id' => $this->buku->id,
        ]);

    $response->assertOk();
    $data = $response->json();
    expect($data['status'])->toBe('removed');
});

test('user can save bookmark', function () {
    $response = $this->actingAs($this->user)
        ->post('/app/markah', [
            'buku_id' => $this->buku->id,
            'halaman' => 42,
            'catatan' => 'Interesting part',
        ]);

    $response->assertOk();
    $data = $response->json();
    expect($data['halaman'])->toBe(42);
    expect($data['catatan'])->toBe('Interesting part');
});

test('user can log reading stats', function () {
    $response = $this->actingAs($this->user)
        ->post('/app/catat-halaman', [
            'buku_id' => $this->buku->id,
            'halaman_dibaca' => 15,
            'tanggal' => now()->toDateString(),
        ]);

    $response->assertOk();

    $riwayat = RiwayatBaca::where('user_id', $this->user->id)
        ->where('buku_id', $this->buku->id)
        ->first();

    expect($riwayat)->not->toBeNull();
    expect($riwayat->halaman_dibaca)->toBe(15);
});
