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
            ['nama_anggota' => 'Super Admin', 'id_anggota' => 'superadmin', 'peran' => Peran::SuperAdmin],
            ['nama_anggota' => 'Admin', 'id_anggota' => 'admin', 'peran' => Peran::Admin],
            ['nama_anggota' => 'Pengguna', 'id_anggota' => 'user', 'peran' => Peran::User],
        ];

        foreach ($users as $userData) {
            User::create([
                'nama_anggota' => $userData['nama_anggota'],
                'id_anggota' => $userData['id_anggota'],
                'email' => $userData['id_anggota'].'@perpustakaan.test',
                'password' => Hash::make('stmik'),
                'email_verified_at' => now(),
                'peran' => $userData['peran'],
            ]);
        }
    }
}
