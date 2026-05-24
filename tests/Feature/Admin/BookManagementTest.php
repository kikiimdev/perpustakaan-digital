<?php

use App\Enums\Peran;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penulis;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);
    $this->admin = User::factory()->create(['peran' => Peran::Admin]);
    $this->penulis = Penulis::create(['nama' => 'Test Author']);
    $this->kategori = Kategori::create(['nama' => 'Fiksi', 'slug' => 'fiksi']);
});

test('admin can view book list', function () {
    $response = $this->actingAs($this->admin)->get('/admin/buku');
    $response->assertOk();
});

test('admin can store a new book', function () {
    $pdf = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

    $response = $this->actingAs($this->admin)
        ->post('/admin/buku', [
            'penulis_id' => $this->penulis->id,
            'judul' => 'Test Book',
            'sinopsis' => 'A great book synopsis.',
            'file_pdf' => $pdf,
            'jumlah_halaman' => 50,
            'kategori_ids' => [$this->kategori->id],
        ]);

    $response->assertRedirect(route('admin.buku.index'));

    $buku = Buku::where('judul', 'Test Book')->first();
    expect($buku)->not->toBeNull();
    expect($buku->kategori)->toHaveCount(1);
    expect($buku->kategori->first()->nama)->toBe('Fiksi');
});

test('admin can create penulis on the fly', function () {
    $response = $this->actingAs($this->admin)
        ->post('/admin/penulis/cepat', [
            'nama' => 'Instant Author',
        ]);

    $response->assertOk();
    $data = $response->json();
    expect($data['nama'])->toBe('Instant Author');
    expect(Penulis::where('nama', 'Instant Author')->exists())->toBeTrue();
});

test('admin can delete a book', function () {
    $pdf = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');
    $buku = Buku::create([
        'penulis_id' => $this->penulis->id,
        'judul' => 'To Delete',
        'sinopsis' => '...',
        'file_pdf' => $pdf->store('buku_pdf', 'public'),
        'jumlah_halaman' => 10,
    ]);

    $response = $this->actingAs($this->admin)
        ->delete("/admin/buku/{$buku->id}");

    $response->assertRedirect(route('admin.buku.index'));
    expect(Buku::find($buku->id))->toBeNull();
});

test('user role cannot create a book', function () {
    $user = User::factory()->create(['peran' => Peran::User]);
    $pdf = UploadedFile::fake()->create('book.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)
        ->post('/admin/buku', [
            'penulis_id' => $this->penulis->id,
            'judul' => 'Unauthorized',
            'sinopsis' => '...',
            'file_pdf' => $pdf,
            'jumlah_halaman' => 1,
            'kategori_ids' => [$this->kategori->id],
        ]);

    $response->assertForbidden();
});
