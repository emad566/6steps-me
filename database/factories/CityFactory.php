<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        return [
            'city_name' => $this->faker->unique()->city(), // Generates a random city name
            'country_name' => 'Saudi Arabia', // Generates a random country name
            
            // Timestamps are managed by Eloquent automatically
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null, // Optional; can be assigned a date if soft deleting
        ];
    }
}
