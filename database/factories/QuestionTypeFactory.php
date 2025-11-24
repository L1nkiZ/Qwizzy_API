<?php

namespace Database\Factories;

use App\Models\QuestionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionTypeFactory extends Factory
{
    protected $model = QuestionType::class;

    public function definition()
    {
        static $counter = 0;
        $counter++;
        
        return [
            'name' => $this->faker->unique()->word() . ' Type ' . $counter,
        ];
    }
}
