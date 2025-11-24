<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Difficulty;
use App\Models\QuestionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AnswerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les données nécessaires pour les questions avec des noms uniques
        $this->subject = Subject::create(['name' => 'Test Subject ' . uniqid()]);
        $this->difficulty = Difficulty::create(['name' => 'Test Diff ' . uniqid(), 'point' => 1]);
        $this->questionType = QuestionType::create(['name' => 'QCM Test ' . uniqid()]);
    }

    #[Test]
    public function it_can_list_all_answers()
    {
        $question = Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        Answer::create(['answer' => 1, 'question_id' => $question->id]);
        Answer::create(['answer' => 2, 'question_id' => $question->id]);

        $response = $this->getJson('/api/answers');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'answers' => [
                         '*' => ['id', 'answer', 'question_id', 'question']
                     ]
                 ])
                 ->assertJsonFragment([
                     'answer' => "1"  // L'API retourne answer comme string
                 ])
                 ->assertJsonFragment([
                     'answer' => "2"
                 ]);
    }

    #[Test]
    public function it_returns_empty_array_when_no_answers()
    {
        $response = $this->getJson('/api/answers');

        $response->assertStatus(200)
                 ->assertJson([
                     'answers' => []
                 ]);
    }
}
