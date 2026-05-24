<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Penulis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'penulis_id' => Penulis::factory(),
            'judul' => fake()->sentence(3),
            'sinopsis' => fake()->paragraph(),
            'file_pdf' => 'buku_pdf/contoh.pdf',
            'jumlah_halaman' => fake()->numberBetween(10, 500),
        ];
    }
}
