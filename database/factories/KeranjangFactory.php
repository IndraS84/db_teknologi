<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Keranjang>
 */
class KeranjangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_pelanggan' => Pelanggan::factory(),
            'id_produk' => Produk::factory(),
            'jumlah' => $this->faker->numberBetween(1, 5),
            'subtotal' => $this->faker->numberBetween(50000, 1000000),
        ];
    }
}
