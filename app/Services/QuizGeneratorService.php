<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Subject;
use App\Models\Difficulty;
use Illuminate\Support\Collection;

/**
 * Service de génération de quiz
 * Logique métier réutilisable par REST et SOAP
 */
class QuizGeneratorService
{
    /**
     * Génère un quiz personnalisé basé sur les critères fournis
     *
     * @param array $criteria Critères de génération du quiz
     * @return array
     */
    public function generateQuiz(array $criteria): array
    {
        $numberOfQuestions = $criteria['numberOfQuestions'] ?? 20;
        $subjectId = $criteria['subjectId'] ?? null;
        $difficultyId = $criteria['difficultyId'] ?? null;
        $questionTypeId = $criteria['questionTypeId'] ?? null;

        // Validation
        $validation = $this->validateCriteria($criteria);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'error' => $validation['errors'],
                'quiz' => null
            ];
        }

        // Construction de la requête
        $query = Question::with([
            'difficulty' => function ($query) {
                $query->select('id', 'name');
            },
            'subject' => function ($query) {
                $query->select('id', 'name');
            },
            'question_type' => function ($query) {
                $query->select('id', 'name');
            },
            'answers' => function ($query) {
                $query->select('id', 'question_id', 'answer');
            }
        ]);

        // Application des filtres
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if ($difficultyId) {
            $query->where('difficulty_id', $difficultyId);
        }

        if ($questionTypeId) {
            $query->where('question_type_id', $questionTypeId);
        }

        // Sélection aléatoire des questions
        $questions = $query->inRandomOrder()
            ->limit($numberOfQuestions)
            ->get();

        foreach ($questions as $question) {
            $question->answers = $question->answers->shuffle()->values();
        }

        // Formatage du quiz
        $quiz = $this->formatQuiz($questions, $criteria);

        return [
            'success' => true,
            'error' => null,
            'quiz' => $quiz
        ];
    }

    /**
     * Valide les critères de génération
     *
     * @param array $criteria
     * @return array
     */
    private function validateCriteria(array $criteria): array
    {
        $errors = [];

        // Validation du nombre de questions
        $numberOfQuestions = $criteria['numberOfQuestions'];
        if (!$numberOfQuestions || $numberOfQuestions < 1) {
            $errors[] = 'Le nombre de questions doit être supérieur à 0';
        }

        if ($numberOfQuestions > 100) {
            $errors[] = 'Le nombre de questions ne peut pas dépasser 100';
        }

        // Validation du subject_id si fourni
        if (isset($criteria['subjectId']) && $criteria['subjectId']) {
            $subject = Subject::find($criteria['subjectId']);
            if (!$subject) {
                $errors[] = sprintf('Le thème avec l\'ID %d n\'existe pas', $criteria['subjectId']);
            }
        }

        // Validation du difficulty_id si fourni
        if (isset($criteria['difficultyId']) && $criteria['difficultyId']) {
            $difficulty = Difficulty::find($criteria['difficultyId']);
            if (!$difficulty) {
                $errors[] = sprintf('La difficulté avec l\'ID %d n\'existe pas', $criteria['difficultyId']);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Formate les questions en structure de quiz
     *
     * @param Collection $questions
     * @param array $criteria
     * @return array
     */
    private function formatQuiz(Collection $questions, array $criteria): array
    {
        $formattedQuestions = $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'subject' => $question->subject ? $question->subject->name : null,
                'difficulty' => $question->difficulty ? $question->difficulty->name : null,
                'question_type' => $question->question_type ? $question->question_type->name : null,
                'proposals' => [
                    $question->proposal_1,
                    $question->proposal_2,
                    $question->proposal_3,
                    $question->proposal_4,
                ],
                'answers' => $question->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'answer' => $answer->answer
                    ];
                })->toArray()
            ];
        })->toArray();

        return [
            'metadata' => [
                'total_questions' => count($formattedQuestions),
                'subject_id' => $criteria['subjectId'] ?? null,
                'difficulty_id' => $criteria['difficultyId'] ?? null,
                'question_type_id' => $criteria['questionTypeId'] ?? null,
                'generated_at' => now()->toIso8601String()
            ],
            'questions' => $formattedQuestions
        ];
    }
}
