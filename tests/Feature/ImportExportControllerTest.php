<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Difficulty;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportExportControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Crée un fichier CSV temporaire avec les lignes données.
     */
    private function makeCsv(array $rows, string $header = "Question;Sujet;Difficulte;Type;Prop1;Prop2;Prop3;Prop4;Reponse"): UploadedFile
    {
        $lines = [$header];
        foreach ($rows as $row) {
            $lines[] = implode(',', $row);
        }
        $content = implode("\n", $lines);

        $tmpPath = tempnam(sys_get_temp_dir(), 'qwizzy_test_') . '.csv';
        file_put_contents($tmpPath, $content);

        return new UploadedFile($tmpPath, 'questions.csv', 'text/csv', null, true);
    }

    // Import

    #[Test]
    public function it_can_import_a_valid_csv_file()
    {
        $csv = $this->makeCsv([
            ['Quelle est la capitale de la France ?', 'Géographie', 'Facile', 'QCM', 'Paris', 'Lyon', 'Marseille', 'Bordeaux', '1'],
        ]);

        $response = $this->postJson('/api/import/questions', ['file' => $csv]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Import terminé',
            ])
            ->assertJsonPath('stats.imported', 1)
            ->assertJsonPath('stats.skipped', 0);

        $this->assertDatabaseHas('question', [
            'question' => 'Quelle est la capitale de la France ?',
        ]);
    }

    #[Test]
    public function it_can_import_multiple_questions()
    {
        $csv = $this->makeCsv([
            ['Question 1 ?', 'Sujet A', 'Facile', 'QCM', 'R1', 'R2', 'R3', 'R4', '1'],
            ['Question 2 ?', 'Sujet B', 'Moyen',  'QCM', 'R1', 'R2', 'R3', 'R4', '2'],
            ['Question 3 ?', 'Sujet C', 'Difficile', 'QCM', 'R1', 'R2', 'R3', 'R4', '3'],
        ]);

        $response = $this->postJson('/api/import/questions', ['file' => $csv]);

        $response->assertStatus(200)
            ->assertJsonPath('stats.imported', 3)
            ->assertJsonPath('stats.skipped', 0);
    }

    #[Test]
    public function it_creates_subject_and_difficulty_if_not_exist()
    {
        $csv = $this->makeCsv([
            ['Qui a peint la Joconde ?', 'Art Nouveau', 'Expert', 'QCM', 'Léonard', 'Picasso', 'Monet', 'Dali', '1'],
        ]);

        $this->postJson('/api/import/questions', ['file' => $csv]);

        $this->assertDatabaseHas('subject', ['name' => 'Art Nouveau']);
        $this->assertDatabaseHas('difficulty', ['name' => 'Expert']);
    }

    #[Test]
    public function it_skips_duplicate_questions()
    {
        // Question déjà existante (noms uniques pour éviter le conflit avec les seeds)
        $subject    = Subject::create(['name' => 'Histoire ' . uniqid()]);
        $difficulty = Difficulty::create(['name' => 'Unique_Diff_' . uniqid(), 'point' => 1]);
        $type       = QuestionType::create(['name' => 'QCM_Dup_' . uniqid()]);
        Question::create([
            'question'         => 'Question en double ?',
            'subject_id'       => $subject->id,
            'difficulty_id'    => $difficulty->id,
            'question_type_id' => $type->id,
            'proposal_1'       => 'A',
            'proposal_2'       => 'B',
            'proposal_3'       => 'C',
            'proposal_4'       => 'D',
        ]);

        $csv = $this->makeCsv([
            ['Question en double ?', $subject->name, $difficulty->name, $type->name, 'A', 'B', 'C', 'D', '1'],
        ]);

        $response = $this->postJson('/api/import/questions', ['file' => $csv]);

        $response->assertStatus(200)
            ->assertJsonPath('stats.imported', 0)
            ->assertJsonPath('stats.skipped', 1);
    }

    #[Test]
    public function it_skips_rows_with_missing_columns()
    {
        $csv = $this->makeCsv([
            ['Question incomplète ?', 'Sujet'],   // seulement 2 colonnes
        ]);

        $response = $this->postJson('/api/import/questions', ['file' => $csv]);

        $response->assertStatus(200)
            ->assertJsonPath('stats.skipped', 1);
    }

    #[Test]
    public function it_validates_file_is_required_on_import()
    {
        $response = $this->postJson('/api/import/questions', []);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_validates_only_csv_files_are_accepted()
    {
        // Le contrôleur valide mimes:csv,txt,xlsx
        // En mode test, la validation MIME s'appuie sur le nom du fichier
        $file = UploadedFile::fake()->create('malicious.pdf', 10, 'application/pdf');

        $response = $this->postJson('/api/import/questions', ['file' => $file]);

        $response->assertStatus(422);
    }

    // Export

    #[Test]
    public function it_can_export_questions_as_csv()
    {
        $subject    = Subject::create(['name' => 'Sciences ' . uniqid()]);
        $difficulty = Difficulty::create(['name' => 'Moyen_' . uniqid(), 'point' => 2]);
        $type       = QuestionType::create(['name' => 'QCM_' . uniqid()]);
        $question   = Question::create([
            'question'         => 'Combien de planètes dans le système solaire ?',
            'subject_id'       => $subject->id,
            'difficulty_id'    => $difficulty->id,
            'question_type_id' => $type->id,
            'proposal_1'       => '7',
            'proposal_2'       => '8',
            'proposal_3'       => '9',
            'proposal_4'       => '10',
        ]);
        Answer::create(['question_id' => $question->id, 'answer' => '2', 'is_correct' => true]);

        $response = $this->get('/api/export/questions');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    #[Test]
    public function it_export_csv_contains_question_data()
    {
        $subject    = Subject::create(['name' => 'Mathématiques ' . uniqid()]);
        $difficulty = Difficulty::create(['name' => 'Facile_' . uniqid(), 'point' => 1]);
        $type       = QuestionType::create(['name' => 'QCM_' . uniqid()]);
        $question   = Question::create([
            'question'         => 'Combien font 2 + 2 ?',
            'subject_id'       => $subject->id,
            'difficulty_id'    => $difficulty->id,
            'question_type_id' => $type->id,
            'proposal_1'       => '3',
            'proposal_2'       => '4',
            'proposal_3'       => '5',
            'proposal_4'       => '6',
        ]);
        Answer::create(['question_id' => $question->id, 'answer' => '2', 'is_correct' => true]);

        $response = $this->get('/api/export/questions');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Combien font 2 + 2 ?', $content);
        $this->assertStringContainsString('Mathématiques', $content);
    }

    #[Test]
    public function it_export_csv_returns_headers_row_even_when_no_questions()
    {
        $response = $this->get('/api/export/questions');

        $response->assertStatus(200);
        $content = $response->streamedContent();

        $this->assertStringContainsString('Question', $content);
    }

    #[Test]
    public function it_export_csv_has_correct_content_disposition_header()
    {
        $response = $this->get('/api/export/questions');

        $this->assertStringContainsString(
            'questions_export_',
            $response->headers->get('Content-Disposition')
        );
    }
}
