<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Difficulty;
use App\Models\Subject;
use App\Models\QuestionType;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ImportExportController extends Controller
{
    /**
     * Structure attendue du fichier Excel (CSV) :
     *
     * Colonne A: Question (String)
     * Colonne B: Sujet (String) - Sera créé s'il n'existe pas
     * Colonne C: Difficulté (String) - Sera créé si n'existe pas
     * Colonne D: Type (String) - (QCM, Vrai/Faux, etc.)
     * Colonne E: Proposition 1 (String)
     * Colonne F: Proposition 2 (String)
     * Colonne G: Proposition 3 (String)
     * Colonne H: Proposition 4 (String)
     * Colonne I: Bonne Réponse (1-4)
     */
    public function importQuestions(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx'
        ]);

        $file = $request->file('file');

        // Simple CSV handling
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Remove header row
        $header = array_shift($data);

        $stats = [
            'imported' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                // Validation basique de la ligne
                if (count($row) < 9) {
                    $stats['errors'][] = "Ligne " . ($index + 2) . ": Colonnes manquantes";
                    $stats['skipped']++;
                    continue;
                }

                $questionText = trim($row[0]);
                $subjectName = trim($row[1]);
                $difficultyName = trim($row[2]);
                $typeName = trim($row[3]);
                $prop1 = trim($row[4]);
                $prop2 = trim($row[5]);
                $prop3 = trim($row[6]);
                $prop4 = trim($row[7]);
                $correctIndex = (int) trim($row[8]);

                if (empty($questionText)) {
                    $stats['skipped']++;
                    continue;
                }

                // Check duplicate
                if (Question::where('question', $questionText)->exists()) {
                    $stats['errors'][] = "Ligne " . ($index + 2) . ": Question existe déjà";
                    $stats['skipped']++;
                    continue;
                }

                // Find or Create relations
                $subject = Subject::firstOrCreate(['name' => $subjectName]);
                $difficulty = Difficulty::firstOrCreate(['name' => $difficultyName], ['point' => 1]); // Valeur par défaut
                $type = QuestionType::firstOrCreate(['name' => $typeName]);

                // Create Question
                $question = Question::create([
                    'question' => $questionText,
                    'subject_id' => $subject->id,
                    'difficulty_id' => $difficulty->id,
                    'question_type_id' => $type->id,
                    'proposal_1' => $prop1,
                    'proposal_2' => $prop2,
                    'proposal_3' => $prop3,
                    'proposal_4' => $prop4
                ]);

                // Create Answers
                // Note: Le modèle actuel semble gérer les réponses dans la table answers liée
                $answers = [$prop1, $prop2, $prop3, $prop4];

                foreach ($answers as $key => $ansText) {
                    if (!empty($ansText)) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer' => ($key + 1), // Stocker l'index comme réponse (1, 2, 3, 4)
                            'is_correct' => ($key + 1) === $correctIndex
                        ]);
                    }
                }

                $stats['imported']++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import terminé',
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur critique lors de l\'import: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporte les questions en CSV
     */
    public function exportQuestions()
    {
        $fileName = 'questions_export_' . date('Y-m-d_H-i') . '.csv';

        $questions = Question::with(['subject', 'difficulty', 'answers'])->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Question', 'Sujet', 'Difficulté', 'Proposition 1', 'Proposition 2', 'Proposition 3', 'Proposition 4', 'Bonne Réponse (1-4)'];

        $callback = function() use ($questions, $columns) {
            $file = fopen('php://output', 'w');

            // AJOUT DU BOM UTF-8 pour Excel
            fputs($file, "\xEF\xBB\xBF");

            // Utilisation du point-virgule pour Excel français
            fputcsv($file, $columns, ';');

            foreach ($questions as $q) {
                // Trouver l'index de la bonne réponse
                // Par défaut on cherche lequel est correct
                $correctIdx = 1;
                foreach($q->answers as $ans) {
                     // Si la table answers a une colonne is_correct
                     if($ans->is_correct) {
                         $correctIdx = $ans->answer; // Si 'answer' contient '1', '2', etc.
                         break;
                     }
                }

                $row = [
                    $q->question,
                    $q->subject?->name ?? 'Inconnu',
                    $q->difficulty?->name ?? 'Moyenne',
                    $q->proposal_1,
                    $q->proposal_2,
                    $q->proposal_3,
                    $q->proposal_4,
                    $correctIdx
                ];

                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
