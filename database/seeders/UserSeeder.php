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
        $users = [
            ['name' => 'Super Admin', 'username' => 'superadmin', 'peran' => Peran::SuperAdmin],
            ['name' => 'Admin', 'username' => 'admin', 'peran' => Peran::Admin],
            ['name' => 'Pengguna', 'username' => 'user', 'peran' => Peran::User],
        ];

        foreach ($users as $userData) {
            User::create([
                'name' => $userData['name'],
                'username' => $userData['username'],
                'email' => $userData['username'].'@perpustakaan.test',
                'password' => Hash::make('stimik'),
                'email_verified_at' => now(),
                'peran' => $userData['peran'],
            ]);
        }
    }
}
