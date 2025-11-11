<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diskon>
 */
class DiskonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_diskon' => $this->faker->sentence(3),
            'jenis_diskon' => $this->faker->randomElement(['persentase', 'nominal']),
            'nilai_diskon' => $this->faker->numberBetween(5, 50),
            'tanggal_mulai' => now()->subDays($this->faker->numberBetween(0, 10)),
            'tanggal_berakhir' => now()->addDays($this->faker->numberBetween(1, 30)),
            'status' => 'active',
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
