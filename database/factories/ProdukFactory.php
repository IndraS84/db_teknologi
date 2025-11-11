<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produk>
 */
class ProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_produk' => $this->faker->word(),
            'harga' => $this->faker->numberBetween(50000, 500000),
            'stok' => $this->faker->numberBetween(1, 100),
            'deskripsi' => $this->faker->sentence(),
            'kategori' => $this->faker->randomElement(['Elektronik', 'Fashion', 'Rumah Tangga', 'Olahraga', 'Kesehatan', 'Makanan', 'Minuman', 'Otomotif']),
            'id_admin' => Admin::factory(),
            'id_supplier' => Supplier::factory(),
        ];
    }
}
