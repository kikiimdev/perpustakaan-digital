<?php

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutMiddleware;

beforeEach(function () {
    withoutMiddleware(PreventRequestForgery::class);
});

test('user can update their profile with new fields', function () {
    $user = User::factory()->create();

    actingAs($user)->patch('/settings/profile', [
        'nama_anggota' => 'Updated Name',
        'email' => $user->email,
        'ttl' => 'Jakarta, 1 Januari 2000',
        'jenis_kelamin' => 'laki-laki',
        'no_telp' => '081234567890',
    ])->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->nama_anggota)->toBe('Updated Name')
        ->and($user->ttl)->toBe('Jakarta, 1 Januari 2000')
        ->and($user->jenis_kelamin)->toBe('laki-laki')
        ->and($user->no_telp)->toBe('081234567890');
});

test('registration does not allow setting new fields directly', function () {
    $response = post('/register', [
        'nama_anggota' => 'New User',
        'id_anggota' => 'newuser',
        'password' => 'password',
        'password_confirmation' => 'password',
        'ttl' => 'Surabaya, 2 Februari 2002',
        'jenis_kelamin' => 'perempuan',
        'no_telp' => '089876543210',
    ]);

    $response->assertRedirect('/app/dasbor'); // App specific default redirect

    $user = User::where('id_anggota', 'newuser')->first();

    expect($user)->not->toBeNull()
        ->and($user->nama_anggota)->toBe('New User')
        ->and($user->ttl)->toBeNull()
        ->and($user->jenis_kelamin)->toBeNull()
        ->and($user->no_telp)->toBeNull();
});
