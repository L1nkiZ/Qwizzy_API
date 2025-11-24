<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Difficulty;
use App\Models\QuestionType;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer des données de test avec des noms uniques
        $this->difficulty = Difficulty::create([
            'name' => 'Facile Test ' . uniqid(),
            'point' => 1
        ]);

        $this->subject = Subject::create([
            'name' => 'The Witcher Test ' . uniqid()
        ]);

        $this->questionType = QuestionType::create([
            'name' => 'QCM Test ' . uniqid()
        ]);
    }

    #[Test]
    public function it_can_list_questions_with_pagination()
    {
        // Créer une question
        Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->getJson('/api/questions?current_sort=id&current_sort_dir=asc&per_page=10');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'questions' => [
                         'data',
                         'current_page',
                         'per_page',
                         'total'
                     ]
                 ]);
    }

    #[Test]
    public function it_can_filter_questions_by_theme_string()
    {
        // Créer une question
        $question = Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->getJson('/api/questions/by-theme?theme=Witcher');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'questions' => [
                         '*' => [
                             'id',
                             'question',
                             'subject',
                             'difficulty',
                             'question_type',
                             'answers'
                         ]
                     ]
                 ]);
    }

    #[Test]
    public function it_returns_error_when_theme_string_is_missing()
    {
        $response = $this->getJson('/api/questions/by-theme');

        $response->assertStatus(200)
                 ->assertJson([
                     'error' => true,
                     'message' => 'Le thème est requis'
                 ]);
    }

    #[Test]
    public function it_can_filter_questions_by_theme_id()
    {
        // Créer une question
        $question = Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        Answer::create([
            'answer' => 1,
            'question_id' => $question->id
        ]);

        $response = $this->getJson("/api/questions/theme/{$this->subject->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'questions' => [
                         '*' => [
                             'id',
                             'question',
                             'subject',
                             'difficulty',
                             'question_type',
                             'answers'
                         ]
                     ]
                 ]);
    }

    #[Test]
    public function it_returns_404_when_theme_id_not_found()
    {
        $response = $this->getJson('/api/questions/theme/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => true,
                     'message' => 'Thème non trouvé'
                 ]);
    }

    #[Test]
    public function it_can_get_create_form_data()
    {
        $response = $this->getJson('/api/questions/create');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'difficulties',
                     'subjects',
                     'question_types'
                 ]);
    }

    #[Test]
    public function it_can_create_a_question()
    {
        $questionData = [
            'question' => 'Quelle est la capitale de la France ?',
            'proposal_1' => 'Paris',
            'proposal_2' => 'Lyon',
            'proposal_3' => 'Marseille',
            'proposal_4' => 'Bordeaux',
            'correct_answer_number' => 1,
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ];

        $response = $this->postJson('/api/questions', $questionData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'question',
                     'answer'
                 ]);

        $this->assertDatabaseHas('question', [
            'question' => 'Quelle est la capitale de la France ?',
            'proposal_1' => 'Paris',
        ]);

        $this->assertDatabaseHas('answer', [
            'answer' => 1
        ]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_question()
    {
        $response = $this->postJson('/api/questions', []);

        $response->assertStatus(200)
                 ->assertJson([
                     'error' => true
                 ])
                 ->assertJsonStructure([
                     'error',
                     'message'
                 ]);
    }

    #[Test]
    public function it_can_show_a_question()
    {
        $question = Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        Answer::create([
            'answer' => 1,
            'question_id' => $question->id
        ]);

        $response = $this->getJson("/api/questions/{$question->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'question' => [
                         'id',
                         'question',
                         'subject',
                         'difficulty',
                         'question_type',
                         'answers'
                     ]
                 ]);
    }

    #[Test]
    public function it_returns_404_when_question_not_found()
    {
        $response = $this->getJson('/api/questions/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => true,
                     'message' => 'Question non trouvée'
                 ]);
    }

    #[Test]
    public function it_can_get_edit_form_data()
    {
        $question = Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        $response = $this->getJson("/api/questions/{$question->id}/edit");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'difficulties',
                     'subjects',
                     'question_types',
                     'question'
                 ]);
    }

    #[Test]
    public function it_can_update_a_question()
    {
        $question = Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        Answer::create([
            'answer' => 1,
            'question_id' => $question->id
        ]);

        $updateData = [
            'question' => 'Question mise à jour ?',
            'proposal_1' => 'Réponse A',
            'proposal_2' => 'Réponse B',
            'proposal_3' => 'Réponse C',
            'proposal_4' => 'Réponse D',
            'correct_answer_number' => 2,
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ];

        $response = $this->putJson("/api/questions/{$question->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'question',
                     'answer'
                 ]);

        $this->assertDatabaseHas('question', [
            'id' => $question->id,
            'question' => 'Question mise à jour ?',
        ]);

        $this->assertDatabaseHas('answer', [
            'question_id' => $question->id,
            'answer' => 2
        ]);
    }

    #[Test]
    public function it_returns_error_when_updating_non_existent_question()
    {
        $updateData = [
            'question' => 'Question mise à jour ?',
            'proposal_1' => 'Réponse A',
            'proposal_2' => 'Réponse B',
            'proposal_3' => 'Réponse C',
            'proposal_4' => 'Réponse D',
            'correct_answer_number' => 2,
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ];

        $response = $this->putJson('/api/questions/999', $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'error' => true,
                     'message' => 'Question non trouvée'
                 ]);
    }

    #[Test]
    public function it_can_delete_a_question()
    {
        $question = Question::factory()->create([
            'subject_id' => $this->subject->id,
            'difficulty_id' => $this->difficulty->id,
            'question_type_id' => $this->questionType->id,
        ]);

        Answer::create([
            'answer' => 1,
            'question_id' => $question->id
        ]);

        $response = $this->deleteJson("/api/questions/{$question->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Question supprimée avec succès'
                 ]);

        $this->assertDatabaseMissing('question', [
            'id' => $question->id
        ]);

        $this->assertDatabaseMissing('answer', [
            'question_id' => $question->id
        ]);
    }

    #[Test]
    public function it_returns_404_when_deleting_non_existent_question()
    {
        $response = $this->deleteJson('/api/questions/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => true,
                     'message' => 'Question non trouvée'
                 ]);
    }
}
