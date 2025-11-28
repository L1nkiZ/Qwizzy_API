<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Subject;
use App\Models\Difficulty;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\UserQuiz;
use App\Models\User;
use SoapServer;
use SoapFault;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur SOAP autonome pour la génération et gestion de quiz
 *
 * Ce contrôleur contient toute la logique de génération de quiz,
 * gestion des scores et expose les services via SOAP (mode non-WSDL)
 */
class QuizSoapController extends Controller
{
    /**
     * Point d'entrée du serveur SOAP (mode non-WSDL)
     */
    public function server()
    {
        try {
            $server = new SoapServer(null, [
                'uri' => 'http://localhost:8000/soap/quiz',
                'trace' => true,
                'exceptions' => true
            ]);

            $server->setObject($this);

            ob_start();
            $server->handle();
            $response = ob_get_clean();

            return response($response, 200, [
                'Content-Type' => 'text/xml; charset=utf-8'
            ]);
        } catch (\Exception $e) {
            return response('SOAP Server Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Génère un quiz personnalisé (méthode SOAP)
     *
     * @param int $numberOfQuestions Nombre de questions souhaitées (1-100)
     * @param int|null $subjectId ID du thème (optionnel)
     * @param int|null $difficultyId ID de la difficulté (optionnel)
     * @param int|null $questionTypeId ID du type de question (optionnel)
     * @return array
     */
    public function GenerateQuiz($numberOfQuestions, $subjectId = null, $difficultyId = null, $questionTypeId = null)
    {
        try {
            // Validation des paramètres
            $numberOfQuestions = (int) $numberOfQuestions;

            if ($numberOfQuestions < 1 || $numberOfQuestions > 100) {
                throw new SoapFault('Client', 'Le nombre de questions doit être entre 1 et 100');
            }

            // Validation du subject si fourni
            if ($subjectId !== null) {
                $subject = Subject::find((int) $subjectId);
                if (!$subject) {
                    throw new SoapFault('Client', "Le thème avec l'ID $subjectId n'existe pas");
                }
            }

            // Validation de la difficulté si fournie
            if ($difficultyId !== null) {
                $difficulty = Difficulty::find((int) $difficultyId);
                if (!$difficulty) {
                    throw new SoapFault('Client', "La difficulté avec l'ID $difficultyId n'existe pas");
                }
            }

            // Construction de la requête
            $query = Question::with([
                'difficulty:id,name',
                'subject:id,name',
                'question_type:id,name',
                'answers:id,question_id,answer,is_correct'
            ]);

            // Application des filtres
            if ($subjectId !== null) {
                $query->where('subject_id', (int) $subjectId);
            }

            if ($difficultyId !== null) {
                $query->where('difficulty_id', (int) $difficultyId);
            }

            if ($questionTypeId !== null) {
                $query->where('question_type_id', (int) $questionTypeId);
            }

            // Sélection aléatoire des questions
            $questions = $query->inRandomOrder()
                ->limit($numberOfQuestions)
                ->get();

            if ($questions->count() < $numberOfQuestions) {
                throw new SoapFault(
                    'Server',
                    sprintf(
                        'Nombre insuffisant de questions disponibles. Demandé: %d, Disponible: %d',
                        $numberOfQuestions,
                        $questions->count()
                    )
                );
            }

            // Formatage du quiz
            $quiz = $this->formatQuiz($questions, [
                'numberOfQuestions' => $numberOfQuestions,
                'subjectId' => $subjectId,
                'difficultyId' => $difficultyId,
                'questionTypeId' => $questionTypeId
            ]);

            return [
                'success' => true,
                'message' => 'Quiz généré avec succès',
                'quiz' => $quiz
            ];
        } catch (SoapFault $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new SoapFault('Server', 'Erreur interne: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les statistiques des questions disponibles (méthode SOAP)
     *
     * @return array
     */
    public function GetQuizStatistics()
    {
        try {
            $totalQuestions = Question::count();

            // Statistiques par thème
            $bySubject = Question::select('subject_id')
                ->selectRaw('COUNT(*) as count')
                ->with('subject:id,name')
                ->groupBy('subject_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'subject_id' => $item->subject_id,
                        'subject_name' => $item->subject ? $item->subject->name : null,
                        'count' => $item->count
                    ];
                })
                ->toArray();

            // Statistiques par difficulté
            $byDifficulty = Question::select('difficulty_id')
                ->selectRaw('COUNT(*) as count')
                ->with('difficulty:id,name')
                ->groupBy('difficulty_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'difficulty_id' => $item->difficulty_id,
                        'difficulty_name' => $item->difficulty ? $item->difficulty->name : null,
                        'count' => $item->count
                    ];
                })
                ->toArray();

            return [
                'success' => true,
                'statistics' => [
                    'total_questions' => $totalQuestions,
                    'by_subject' => $bySubject,
                    'by_difficulty' => $byDifficulty
                ]
            ];
        } catch (\Exception $e) {
            throw new SoapFault('Server', 'Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        }
    }

    /**
     * Formate les questions en structure de quiz compatible SOAP
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
                'subject' => $question->subject ? $question->subject->name : '',
                'difficulty' => $question->difficulty ? $question->difficulty->name : '',
                'question_type' => $question->question_type ? $question->question_type->name : '',
                'proposal1' => $question->proposal_1 ?? '',
                'proposal2' => $question->proposal_2 ?? '',
                'proposal3' => $question->proposal_3 ?? '',
                'proposal4' => $question->proposal_4 ?? '',
                'answers' => $question->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'answer' => $answer->answer,
                        'is_correct' => (bool) $answer->is_correct
                    ];
                })->toArray()
            ];
        })->toArray();

        return [
            'metadata' => [
                'total_questions' => count($formattedQuestions),
                'subject_id' => $criteria['subjectId'] ?? 0,
                'difficulty_id' => $criteria['difficultyId'] ?? 0,
                'question_type_id' => $criteria['questionTypeId'] ?? 0,
                'generated_at' => now()->toIso8601String()
            ],
            'questions' => $formattedQuestions
        ];
    }

    /**
     * Crée un quiz avec des questions spécifiques (méthode SOAP)
     *
     * @param string $name Nom du quiz
     * @param string $description Description du quiz
     * @param array $questionIds IDs des questions à inclure
     * @return array
     */
    public function CreateQuiz($name, $description, $questionIds)
    {
        try {
            DB::beginTransaction();

            // Validation
            if (empty($name)) {
                throw new SoapFault('Client', 'Le nom du quiz est requis');
            }

            if (empty($questionIds) || !is_array($questionIds)) {
                throw new SoapFault('Client', 'Au moins une question est requise');
            }

            // Vérifier que toutes les questions existent
            $questions = Question::whereIn('id', $questionIds)->get();
            if ($questions->count() !== count($questionIds)) {
                throw new SoapFault('Client', 'Certaines questions n\'existent pas');
            }

            // Créer le quiz
            $quiz = Quiz::create([
                'name' => $name,
                'description' => $description ?? ''
            ]);

            // Associer les questions
            foreach ($questionIds as $questionId) {
                QuizQuestion::create([
                    'quizz_id' => $quiz->id,
                    'question_id' => $questionId
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Quiz créé avec succès',
                'quiz' => [
                    'id' => $quiz->id,
                    'name' => $quiz->name,
                    'description' => $quiz->description,
                    'questions_count' => count($questionIds),
                    'created_at' => $quiz->created_at->toIso8601String()
                ]
            ];
        } catch (SoapFault $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new SoapFault('Server', 'Erreur lors de la création du quiz: ' . $e->getMessage());
        }
    }

    /**
     * Démarre un quiz pour un utilisateur (méthode SOAP)
     *
     * @param int $userId ID de l'utilisateur
     * @param int $quizId ID du quiz
     * @return array
     */
    public function StartQuiz($userId, $quizId)
    {
        try {
            // Vérifier que l'utilisateur existe
            $user = User::find($userId);
            if (!$user) {
                throw new SoapFault('Client', "L'utilisateur avec l'ID $userId n'existe pas");
            }

            // Vérifier que le quiz existe
            $quiz = Quiz::with('questions.answers')->find($quizId);
            if (!$quiz) {
                throw new SoapFault('Client', "Le quiz avec l'ID $quizId n'existe pas");
            }

            // Calculer le score maximum (basé sur les points des difficultés)
            $maxScore = 0;
            foreach ($quiz->questions as $question) {
                if ($question->difficulty) {
                    $maxScore += $question->difficulty->point;
                }
            }

            // Créer une entrée UserQuiz
            $userQuiz = UserQuiz::create([
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'score' => 0,
                'max_score' => $maxScore,
                'started_at' => now(),
                'completed_at' => null,
                'status' => 'in_progress'
            ]);

            // Formater les questions (sans les réponses correctes)
            $questions = $quiz->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'proposal1' => $question->proposal_1 ?? '',
                    'proposal2' => $question->proposal_2 ?? '',
                    'proposal3' => $question->proposal_3 ?? '',
                    'proposal4' => $question->proposal_4 ?? '',
                ];
            })->toArray();

            return [
                'success' => true,
                'message' => 'Quiz démarré avec succès',
                'user_quiz_id' => $userQuiz->id,
                'quiz' => [
                    'id' => $quiz->id,
                    'name' => $quiz->name,
                    'description' => $quiz->description,
                    'max_score' => $maxScore,
                    'questions' => $questions
                ]
            ];
        } catch (SoapFault $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new SoapFault('Server', 'Erreur lors du démarrage du quiz: ' . $e->getMessage());
        }
    }

    /**
     * Soumet les réponses et calcule le score (méthode SOAP)
     *
     * @param int $userQuizId ID de l'entrée UserQuiz
     * @param array $answers Tableau des réponses [questionId => answerId]
     * @return array
     */
    public function SubmitQuizAnswers($userQuizId, $answers)
    {
        try {
            DB::beginTransaction();

            // Récupérer le UserQuiz
            $userQuiz = UserQuiz::with('quiz.questions.answers', 'quiz.questions.difficulty')->find($userQuizId);
            if (!$userQuiz) {
                throw new SoapFault('Client', "L'entrée UserQuiz avec l'ID $userQuizId n'existe pas");
            }

            if ($userQuiz->status === 'completed') {
                throw new SoapFault('Client', 'Ce quiz a déjà été complété');
            }

            if (!is_array($answers) || empty($answers)) {
                throw new SoapFault('Client', 'Les réponses sont requises');
            }

            $score = 0;
            $correctAnswers = 0;
            $totalQuestions = $userQuiz->quiz->questions->count();
            $details = [];

            // Calculer le score
            foreach ($userQuiz->quiz->questions as $question) {
                $questionId = $question->id;
                $answerId = $answers[$questionId] ?? null;

                $isCorrect = false;
                $correctAnswerId = null;

                // Trouver la bonne réponse
                foreach ($question->answers as $answer) {
                    if ($answer->is_correct) {
                        $correctAnswerId = $answer->id;
                        if ($answerId == $answer->id) {
                            $isCorrect = true;
                            $correctAnswers++;
                            // Ajouter les points de la difficulté
                            if ($question->difficulty) {
                                $score += $question->difficulty->point;
                            }
                        }
                        break;
                    }
                }

                $details[] = [
                    'question_id' => $questionId,
                    'user_answer_id' => $answerId,
                    'correct_answer_id' => $correctAnswerId,
                    'is_correct' => $isCorrect,
                    'points_earned' => $isCorrect && $question->difficulty ? $question->difficulty->point : 0
                ];
            }

            // Mettre à jour le UserQuiz
            $userQuiz->update([
                'score' => $score,
                'completed_at' => now(),
                'status' => 'completed'
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Quiz complété avec succès',
                'results' => [
                    'user_quiz_id' => $userQuiz->id,
                    'score' => $score,
                    'max_score' => $userQuiz->max_score,
                    'percentage' => $userQuiz->max_score > 0 ? round(($score / $userQuiz->max_score) * 100, 2) : 0,
                    'correct_answers' => $correctAnswers,
                    'total_questions' => $totalQuestions,
                    'completed_at' => $userQuiz->completed_at->toIso8601String(),
                    'details' => $details
                ]
            ];
        } catch (SoapFault $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new SoapFault('Server', 'Erreur lors de la soumission des réponses: ' . $e->getMessage());
        }
    }

    /**
     * Récupère l'historique des quiz d'un utilisateur (méthode SOAP)
     *
     * @param int $userId ID de l'utilisateur
     * @return array
     */
    public function GetUserQuizHistory($userId)
    {
        try {
            // Vérifier que l'utilisateur existe
            $user = User::find($userId);
            if (!$user) {
                throw new SoapFault('Client', "L'utilisateur avec l'ID $userId n'existe pas");
            }

            // Récupérer l'historique
            $userQuizzes = UserQuiz::with('quiz')
                ->where('user_id', $userId)
                ->orderBy('started_at', 'desc')
                ->get();

            $history = $userQuizzes->map(function ($userQuiz) {
                return [
                    'user_quiz_id' => $userQuiz->id,
                    'quiz_id' => $userQuiz->quiz_id,
                    'quiz_name' => $userQuiz->quiz ? $userQuiz->quiz->name : '',
                    'score' => $userQuiz->score,
                    'max_score' => $userQuiz->max_score,
                    'percentage' => $userQuiz->max_score > 0 ? round(($userQuiz->score / $userQuiz->max_score) * 100, 2) : 0,
                    'status' => $userQuiz->status,
                    'started_at' => $userQuiz->started_at->toIso8601String(),
                    'completed_at' => $userQuiz->completed_at ? $userQuiz->completed_at->toIso8601String() : null
                ];
            })->toArray();

            return [
                'success' => true,
                'user_id' => $userId,
                'total_quizzes' => count($history),
                'history' => $history
            ];
        } catch (SoapFault $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new SoapFault('Server', 'Erreur lors de la récupération de l\'historique: ' . $e->getMessage());
        }
    }

    /**
     * Récupère le classement des meilleurs scores pour un quiz (méthode SOAP)
     *
     * @param int $quizId ID du quiz
     * @param int $limit Nombre de résultats (par défaut 10)
     * @return array
     */
    public function GetQuizLeaderboard($quizId, $limit = 10)
    {
        try {
            // Vérifier que le quiz existe
            $quiz = Quiz::find($quizId);
            if (!$quiz) {
                throw new SoapFault('Client', "Le quiz avec l'ID $quizId n'existe pas");
            }

            $limit = min(max(1, (int) $limit), 100); // Entre 1 et 100

            // Récupérer les meilleurs scores
            $leaderboard = UserQuiz::with('user')
                ->where('quiz_id', $quizId)
                ->where('status', 'completed')
                ->orderBy('score', 'desc')
                ->orderBy('completed_at', 'asc')
                ->limit($limit)
                ->get()
                ->map(function ($userQuiz, $index) {
                    return [
                        'rank' => $index + 1,
                        'user_id' => $userQuiz->user_id,
                        'user_name' => $userQuiz->user ? $userQuiz->user->name : 'Unknown',
                        'score' => $userQuiz->score,
                        'max_score' => $userQuiz->max_score,
                        'percentage' => $userQuiz->max_score > 0 ? round(($userQuiz->score / $userQuiz->max_score) * 100, 2) : 0,
                        'completed_at' => $userQuiz->completed_at->toIso8601String()
                    ];
                })
                ->toArray();

            return [
                'success' => true,
                'quiz_id' => $quizId,
                'quiz_name' => $quiz->name,
                'leaderboard' => $leaderboard
            ];
        } catch (SoapFault $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new SoapFault('Server', 'Erreur lors de la récupération du classement: ' . $e->getMessage());
        }
    }

}


