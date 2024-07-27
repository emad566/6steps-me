<?php

namespace Database\Factories;

use App\Models\Cat;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatFactory extends Factory
{
    protected $model = Cat::class;

    public function definition()
    {
        return [
            'cat_name' => $this->faker->word(), // Generates a random word for cat name
            'created_at' => now(), // Set current timestamp
            'updated_at' => now(), // Set current timestamp
            'deleted_at' => null, // Optional: can be set to a date if soft deleting
        ];
    }
}
