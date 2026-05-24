<?php

namespace Database\Seeders;

use App\Enums\Peran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@perpustakaan.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'peran' => Peran::SuperAdmin,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@perpustakaan.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'peran' => Peran::Admin,
        ]);

        User::create([
            'name' => 'Pengguna',
            'email' => 'user@perpustakaan.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'peran' => Peran::User,
        ]);
    }
}
