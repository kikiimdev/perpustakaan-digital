<?php

use App\Enums\Peran;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);
});

test('guests are redirected to login for protected routes', function () {
    $this->get('/super-admin/kelola-admin')->assertRedirect(route('login'));
    $this->get('/admin/buku')->assertRedirect(route('login'));
    $this->get('/app/dasbor')->assertRedirect(route('login'));
});

test('user role cannot access admin routes', function () {
    $user = User::factory()->create(['peran' => Peran::User]);

    $this->actingAs($user)
        ->get('/admin/buku')
        ->assertForbidden();

    $this->actingAs($user)
        ->get('/admin/laporan')
        ->assertForbidden();
});

test('user role cannot access super admin routes', function () {
    $user = User::factory()->create(['peran' => Peran::User]);

    $this->actingAs($user)
        ->get('/super-admin/kelola-admin')
        ->assertForbidden();
});

test('admin role cannot access super admin routes', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    $this->actingAs($admin)
        ->get('/super-admin/kelola-admin')
        ->assertForbidden();
});

test('admin role can access admin routes', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    $this->actingAs($admin)
        ->get('/admin/buku')
        ->assertOk();

    $this->actingAs($admin)
        ->get('/admin/laporan')
        ->assertOk();

    $this->actingAs($admin)
        ->get('/admin/kartu-anggota')
        ->assertOk();
});

test('super admin can access all routes', function () {
    $superAdmin = User::factory()->create(['peran' => Peran::SuperAdmin]);

    $this->actingAs($superAdmin)
        ->get('/super-admin/kelola-admin')
        ->assertOk();

    $this->actingAs($superAdmin)
        ->get('/admin/buku')
        ->assertOk();

    $this->actingAs($superAdmin)
        ->get('/app/dasbor')
        ->assertOk();
});

test('user role can access app routes', function () {
    $user = User::factory()->create(['peran' => Peran::User]);

    $this->actingAs($user)
        ->get('/app/dasbor')
        ->assertOk();
});
