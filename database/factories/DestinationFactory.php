<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DestinationFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'recommendation_reason' => $this->faker->sentence(),
            'address' => $this->faker->address(),
            // Sekitar Kota Yogyakarta.
            'latitude' => $this->faker->latitude(-7.83, -7.78),
            'longitude' => $this->faker->longitude(110.35, 110.40),
            'price_min' => 10000,
            'price_max' => 50000,
            'open_time' => '08:00',
            'close_time' => '17:00',
            'is_active' => true,
        ];
    }
}
