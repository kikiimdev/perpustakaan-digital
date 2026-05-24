<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        \App\Models\Penulis::factory(5)->create();
        \App\Models\Kategori::factory(8)->create();

        $this->call([
            BukuSeeder::class,
        ]);
    }
}
