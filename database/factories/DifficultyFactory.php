<?php

namespace Database\Factories;

use App\Models\Difficulty;
use Illuminate\Database\Eloquent\Factories\Factory;

class DifficultyFactory extends Factory
{
    protected $model = Difficulty::class;

    public function definition()
    {
        static $counter = 0;
        $counter++;

        return [
            'name' => $this->faker->unique()->word() . ' ' . $counter,
            'point' => $this->faker->numberBetween(1, 5),
        ];
    }
}
