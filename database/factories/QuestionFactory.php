<?php

namespace Database\Factories;

use App\Models\Difficulty;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'question' => $this->faker->sentence().'?',
            'proposal_1' => $this->faker->word(),
            'proposal_2' => $this->faker->word(),
            'proposal_3' => $this->faker->word(),
            'proposal_4' => $this->faker->word(),
            'subject_id' => Subject::factory(),
            'difficulty_id' => Difficulty::factory(),
            'question_type_id' => QuestionType::factory(),
        ];
    }
}
