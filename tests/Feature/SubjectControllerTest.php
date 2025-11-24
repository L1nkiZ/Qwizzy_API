<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class SubjectControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_all_subjects()
    {
        // Créer un sujet spécifique pour ce test
        $subject = Subject::create(['name' => 'The Witcher Test Unique']);

        $response = $this->getJson('/api/subjects?current_sort=id&current_sort_dir=asc&per_page=15');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'subject' => [
                         'data' => [
                             '*' => ['id', 'name']
                         ]
                     ]
                 ])
                 ->assertJsonFragment([
                     'name' => 'The Witcher Test Unique'
                 ]);
    }

    #[Test]
    public function it_can_create_a_subject()
    {
        $subjectData = [
            'name' => 'Le Seigneur des Anneaux'
        ];

        $response = $this->postJson('/api/subjects', $subjectData);

        $response->assertStatus(200)
                 ->assertJson([
                     'error' => false,
                     'message' => 'Le sujet a été créé avec succès'
                 ]);

        $this->assertDatabaseHas('subject', [
            'name' => 'Le Seigneur des Anneaux'
        ]);
    }

    #[Test]
    public function it_validates_subject_name_is_required()
    {
        $response = $this->postJson('/api/subjects', []);

        $response->assertStatus(200)
                 ->assertJson([
                     'error' => true
                 ]);
    }

    #[Test]
    public function it_can_update_a_subject()
    {
        $subject = Subject::create(['name' => 'Marvel']);

        $updateData = ['name' => 'Marvel Cinematic Universe'];

        $response = $this->putJson("/api/subjects/{$subject->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'error' => false,
                     'message' => 'Le sujet a été modifié avec succès'
                 ]);

        $this->assertDatabaseHas('subject', [
            'id' => $subject->id,
            'name' => 'Marvel Cinematic Universe'
        ]);
    }

    #[Test]
    public function it_can_delete_a_subject()
    {
        $subject = Subject::create(['name' => 'DC Comics']);

        $response = $this->deleteJson("/api/subjects/{$subject->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'error' => false,
                     'message' => 'Le sujet a été supprimé avec succès'
                 ]);

        $this->assertDatabaseMissing('subject', [
            'id' => $subject->id
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_subject_names()
    {
        Subject::create(['name' => 'Pokemon']);

        $response = $this->postJson('/api/subjects', ['name' => 'Pokemon']);

        $response->assertStatus(200)
                 ->assertJson([
                     'error' => true
                 ]);
    }
}
