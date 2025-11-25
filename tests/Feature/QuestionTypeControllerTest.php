<?php

namespace Tests\Feature;

use App\Models\QuestionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuestionTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_all_question_types()
    {
        // Créer un type spécifique pour ce test
        $type = QuestionType::create(['name' => 'QCM Test Unique']);

        $response = $this->getJson('/api/question-types?current_sort=id&current_sort_dir=asc&per_page=15');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'questionType' => [
                    'data' => [
                        '*' => ['id', 'name'],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'name' => 'QCM Test Unique',
            ]);
    }

    #[Test]
    public function it_can_create_a_question_type()
    {
        $typeData = [
            'name' => 'Question à choix multiples',
        ];

        $response = $this->postJson('/api/question-types', $typeData);

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'Le type de question a été créé avec succès',
            ]);

        $this->assertDatabaseHas('question_type', [
            'name' => 'Question à choix multiples',
        ]);
    }

    #[Test]
    public function it_validates_name_is_required()
    {
        $response = $this->postJson('/api/question-types', []);

        $response->assertStatus(200)
            ->assertJson([
                'error' => true,
            ]);
    }

    #[Test]
    public function it_can_update_a_question_type()
    {
        $type = QuestionType::create(['name' => 'QCM simple']);

        $updateData = ['name' => 'QCM avancé'];

        $response = $this->putJson("/api/question-types/{$type->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'Le type de question a été modifié avec succès',
            ]);

        $this->assertDatabaseHas('question_type', [
            'id' => $type->id,
            'name' => 'QCM avancé',
        ]);
    }

    #[Test]
    public function it_can_delete_a_question_type()
    {
        $type = QuestionType::create(['name' => 'Appariement']);

        $response = $this->deleteJson("/api/question-types/{$type->id}");

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'Le type de question a été supprimé avec succès',
            ]);

        $this->assertDatabaseMissing('question_type', [
            'id' => $type->id,
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_question_type_names()
    {
        QuestionType::create(['name' => 'Réponse courte']);

        $response = $this->postJson('/api/question-types', ['name' => 'Réponse courte']);

        $response->assertStatus(200)
            ->assertJson([
                'error' => true,
            ]);
    }
}
