<?php

namespace Tests\Feature;

use App\Models\Difficulty;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Subject $subject;
    protected Difficulty $difficulty;
    protected QuestionType $questionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = Subject::create(['name' => 'Géographie Test ' . uniqid()]);
        $this->difficulty = Difficulty::create(['name' => 'Facile Test ' . uniqid(), 'point' => 1]);
        $this->questionType = QuestionType::create(['name' => 'QCM Test ' . uniqid()]);
    }

    // ─── CRUD Quiz ────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_create_a_quiz()
    {
        $response = $this->postJson('/api/quizzes', [
            'name' => 'Quiz Géographie',
            'description' => 'Un quiz sur la géographie',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'quiz' => ['id', 'name', 'description']]);

        $this->assertDatabaseHas('quiz', ['name' => 'Quiz Géographie']);
    }

    #[Test]
    public function it_validates_name_is_required_when_creating_quiz()
    {
        $response = $this->postJson('/api/quizzes', [
            'description' => 'Sans nom',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors']);
    }

    #[Test]
    public function it_can_update_a_quiz()
    {
        $quiz = Quiz::create(['name' => 'Quiz Original', 'description' => 'Description originale']);

        $response = $this->putJson("/api/quizzes/{$quiz->id}", [
            'name' => 'Quiz Modifié',
            'description' => 'Description modifiée',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Quiz mis à jour']);

        $this->assertDatabaseHas('quiz', ['id' => $quiz->id, 'name' => 'Quiz Modifié']);
    }

    #[Test]
    public function it_returns_404_when_updating_nonexistent_quiz()
    {
        $response = $this->putJson('/api/quizzes/99999', [
            'name' => 'Quiz Inexistant',
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Quiz introuvable']);
    }

    #[Test]
    public function it_can_delete_a_quiz()
    {
        $quiz = Quiz::create(['name' => 'Quiz à supprimer']);

        $response = $this->deleteJson("/api/quizzes/{$quiz->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Quiz supprimé']);

        $this->assertDatabaseMissing('quiz', ['id' => $quiz->id]);
    }

    #[Test]
    public function it_returns_404_when_deleting_nonexistent_quiz()
    {
        $response = $this->deleteJson('/api/quizzes/99999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Quiz introuvable']);
    }

    #[Test]
    public function it_can_add_questions_to_a_quiz()
    {
        $quiz = Quiz::create(['name' => 'Quiz avec questions']);

        $question = Question::factory()->create([
            'subject_id'       => $this->subject->id,
            'difficulty_id'    => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->postJson("/api/quizzes/{$quiz->id}/questions", [
            'question_ids' => [$question->id],
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Questions ajoutées au quiz']);

        $this->assertDatabaseHas('quiz_question', [
            'quizz_id'    => $quiz->id,
            'question_id' => $question->id,
        ]);
    }

    #[Test]
    public function it_returns_404_when_adding_questions_to_nonexistent_quiz()
    {
        $question = Question::factory()->create([
            'subject_id'       => $this->subject->id,
            'difficulty_id'    => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->postJson('/api/quizzes/99999/questions', [
            'question_ids' => [$question->id],
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Quiz introuvable']);
    }

    // ─── Génération de quiz ───────────────────────────────────────────────────

    #[Test]
    public function it_can_generate_a_quiz()
    {
        Question::factory()->count(5)->create([
            'subject_id'       => $this->subject->id,
            'difficulty_id'    => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->postJson('/api/quiz/generate', [
            'numberOfQuestions' => 3,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'quiz' => [
                    'metadata' => ['total_questions', 'generated_at'],
                    'questions',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_can_generate_a_quiz_filtered_by_subject()
    {
        Question::factory()->count(3)->create([
            'subject_id'       => $this->subject->id,
            'difficulty_id'    => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->postJson('/api/quiz/generate', [
            'numberOfQuestions' => 2,
            'subjectId'         => $this->subject->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_can_generate_a_quiz_filtered_by_difficulty()
    {
        Question::factory()->count(3)->create([
            'subject_id'       => $this->subject->id,
            'difficulty_id'    => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->postJson('/api/quiz/generate', [
            'numberOfQuestions' => 2,
            'difficultyId'      => $this->difficulty->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_validates_number_of_questions_is_required()
    {
        $response = $this->postJson('/api/quiz/generate', []);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_validates_number_of_questions_minimum_is_1()
    {
        $response = $this->postJson('/api/quiz/generate', [
            'numberOfQuestions' => 0,
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_validates_number_of_questions_maximum_is_100()
    {
        $response = $this->postJson('/api/quiz/generate', [
            'numberOfQuestions' => 101,
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_returns_error_when_subject_id_does_not_exist()
    {
        $response = $this->postJson('/api/quiz/generate', [
            'numberOfQuestions' => 5,
            'subjectId'         => 99999,
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_returns_empty_questions_when_no_questions_match_filters()
    {
        $otherSubject = Subject::create(['name' => 'Autre Thème ' . uniqid()]);

        $response = $this->postJson('/api/quiz/generate', [
            'numberOfQuestions' => 5,
            'subjectId'         => $otherSubject->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('quiz.metadata.total_questions', 0);
    }

    // ─── Statistiques ─────────────────────────────────────────────────────────

    #[Test]
    public function it_can_get_quiz_statistics()
    {
        Question::factory()->count(3)->create([
            'subject_id'       => $this->subject->id,
            'difficulty_id'    => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->getJson('/api/quiz/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'statistics' => [
                    'total_questions',
                    'by_subject',
                    'by_difficulty',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_returns_statistics_with_correct_structure_when_no_extra_questions()
    {
        $response = $this->getJson('/api/quiz/statistics');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'statistics' => [
                    'total_questions',
                    'by_subject',
                    'by_difficulty',
                ],
            ]);

        // Vérifie que total_questions correspond bien au nombre réel en base
        $response->assertJsonPath('statistics.total_questions', \App\Models\Question::count());
    }
}
