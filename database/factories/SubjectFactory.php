<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition()
    {
        static $counter = 0;
        $counter++;

        return [
            'name' => $this->faker->unique()->words(2, true).' '.$counter,
        ];
    }
}
