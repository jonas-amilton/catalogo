<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $slugBase = Str::slug($name);

        return [
            'name' => ucfirst($name),
            'slug' => $slugBase . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'stock' => $this->faker->numberBetween(0, 100),
            'active' => $this->faker->boolean(85),
        ];
    }
}