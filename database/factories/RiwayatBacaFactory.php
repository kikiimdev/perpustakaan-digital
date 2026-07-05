<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\RiwayatBaca;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiwayatBaca>
 */
class RiwayatBacaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'buku_id' => Buku::factory(),
            'halaman_dibaca' => $this->faker->numberBetween(1, 50),
            'tanggal' => $this->faker->date(),
        ];
    }
}
