<?php

namespace Tests\Feature;

use App\Models\Difficulty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DifficultyControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_all_difficulties()
    {
        // Créer une difficulté spécifique pour ce test
        $difficulty = Difficulty::create(['name' => 'Facile Test Unique', 'point' => 1]);

        $response = $this->getJson('/api/difficulties?current_sort=id&current_sort_dir=asc&per_page=15');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'difficulty' => [
                    'data' => [
                        '*' => ['id', 'name', 'point'],
                    ],
                ],
            ])
            ->assertJsonFragment([
                'name' => 'Facile Test Unique',
                'point' => 1,
            ]);
    }

    #[Test]
    public function it_can_create_a_difficulty()
    {
        $difficultyData = [
            'name' => 'Expert',
            'point' => 5,
        ];

        $response = $this->postJson('/api/difficulties', $difficultyData);

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'La difficulté a été créée avec succès',
            ]);

        $this->assertDatabaseHas('difficulty', [
            'name' => 'Expert',
            'point' => 5,
        ]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_difficulty()
    {
        $response = $this->postJson('/api/difficulties', []);

        $response->assertStatus(200)
            ->assertJson([
                'error' => true,
            ]);
    }

    #[Test]
    public function it_validates_point_is_numeric()
    {
        $difficultyData = [
            'name' => 'Test Numeric',
            'point' => 'not-a-number',
        ];

        $response = $this->postJson('/api/difficulties', $difficultyData);

        $response->assertStatus(200)
            ->assertJson([
                'error' => true,
            ]);
    }

    #[Test]
    public function it_can_update_a_difficulty()
    {
        $difficulty = Difficulty::create(['name' => 'Basique', 'point' => 1]);

        $updateData = [
            'name' => 'Très Facile',
            'point' => 1,
        ];

        $response = $this->putJson("/api/difficulties/{$difficulty->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'La difficulté a été modifiée avec succès',
            ]);

        $this->assertDatabaseHas('difficulty', [
            'id' => $difficulty->id,
            'name' => 'Très Facile',
            'point' => 1,
        ]);
    }

    #[Test]
    public function it_can_delete_a_difficulty()
    {
        $difficulty = Difficulty::create(['name' => 'Impossible', 'point' => 5]);

        $response = $this->deleteJson("/api/difficulties/{$difficulty->id}");

        $response->assertStatus(200)
            ->assertJson([
                'error' => false,
                'message' => 'La difficulté a été supprimée avec succès',
            ]);

        $this->assertDatabaseMissing('difficulty', [
            'id' => $difficulty->id,
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_difficulty_names()
    {
        Difficulty::create(['name' => 'Normal', 'point' => 3]);

        $response = $this->postJson('/api/difficulties', [
            'name' => 'Normal',
            'point' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'error' => true,
            ]);
    }
}
