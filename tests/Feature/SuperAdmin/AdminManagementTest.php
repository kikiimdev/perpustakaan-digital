<?php

use App\Enums\Peran;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);
    $this->superAdmin = User::factory()->create(['peran' => Peran::SuperAdmin]);
});

test('super admin can view list of admins', function () {
    User::factory()->count(3)->create(['peran' => Peran::Admin]);

    $response = $this->actingAs($this->superAdmin)
        ->get('/super-admin/kelola-admin');

    $response->assertOk();
    $data = $response->getOriginalContent()->getData();
    expect($data['page']['props']['admins'])->toHaveCount(3);
});

test('super admin can create a new admin', function () {
    $this->actingAs($this->superAdmin)
        ->post('/super-admin/kelola-admin', [
            'name' => 'New Admin',
            'email' => 'newadmin@perpustakaan.test',
            'password' => 'password123',
        ])
        ->assertRedirect(route('super-admin.kelola-admin.index'));

    $admin = User::where('email', 'newadmin@perpustakaan.test')->first();
    expect($admin)->not->toBeNull();
    expect($admin->peran)->toBe(Peran::Admin);
});

test('super admin can delete an admin', function () {
    $admin = User::factory()->create(['peran' => Peran::Admin]);

    $this->actingAs($this->superAdmin)
        ->delete("/super-admin/kelola-admin/{$admin->id}")
        ->assertRedirect(route('super-admin.kelola-admin.index'));

    expect(User::find($admin->id))->toBeNull();
});
