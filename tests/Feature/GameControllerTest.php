<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Difficulty;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Subject $subject;
    protected Difficulty $difficulty;
    protected QuestionType $questionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = Subject::create(['name' => 'Jeu Sujet ' . uniqid()]);
        $this->difficulty = Difficulty::create(['name' => 'Jeu Difficulte ' . uniqid(), 'point' => 1]);
        $this->questionType = QuestionType::create(['name' => 'Jeu Type ' . uniqid()]);
    }

    #[Test]
    public function it_can_get_a_batch_of_questions_for_game(): void
    {
        Question::factory()->count(3)->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->postJson('/api/game/getQuestions', [
            'numberOfQuestions' => 2,
            'subjectId' => $this->subject->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 2)
            ->assertJsonStructure([
                'success',
                'total',
                'questions' => [
                    '*' => [
                        'id',
                        'title',
                        'subject',
                        'difficulty',
                        'question_type',
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_can_get_a_random_batch_without_subject_filter(): void
    {
        Question::factory()->count(3)->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->postJson('/api/game/getQuestions', [
            'numberOfQuestions' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 2)
            ->assertJsonStructure([
                'success',
                'total',
                'questions' => [
                    '*' => ['id', 'title', 'subject', 'difficulty', 'question_type'],
                ],
            ]);
    }

    #[Test]
    public function it_can_get_two_proposals_for_jsuis_pas_sur_mode(): void
    {
        Question::factory()->count(1)->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $question = Question::first();
        Answer::create([
            'question_id' => $question->id,
            'answer' => 2,
        ]);

        $response = $this->postJson('/api/game/questions/options', [
            'question_id' => $question->id,
            'mode' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('mode', 1)
            ->assertJsonStructure([
                'success',
                'question_id',
                'mode',
                'mode_label',
                'proposals',
            ]);

        $this->assertCount(2, $response->json('proposals'));
    }

    #[Test]
    public function it_can_get_four_proposals_for_jcrois_je_lai_mode(): void
    {
        Question::factory()->count(1)->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $question = Question::first();
        Answer::create([
            'question_id' => $question->id,
            'answer' => 2,
        ]);

        $response = $this->postJson('/api/game/questions/options', [
            'question_id' => $question->id,
            'mode' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('mode', 2);

        $this->assertCount(4, $response->json('proposals'));
    }

    #[Test]
    public function it_returns_no_proposals_for_let_me_cook_mode(): void
    {
        Question::factory()->count(1)->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $question = Question::first();
        Answer::create([
            'question_id' => $question->id,
            'answer' => 2,
        ]);

        $response = $this->postJson('/api/game/questions/options', [
            'question_id' => $question->id,
            'mode' => 3,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('mode', 3);

        $this->assertCount(0, $response->json('proposals'));
    }

    #[Test]
    public function it_can_check_answer_with_a_separate_api_call(): void
    {
        $question = Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 2,
        ]);

        $checkResponse = $this->postJson('/api/game/answers/check', [
            'question_id' => $question->id,
        ]);

        $checkResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('correct_answer_position', 2);
    }
}
