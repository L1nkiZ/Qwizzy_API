<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\UserQuiz;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use SoapServer;
use SoapFault;

/**
 * Contrôleur SOAP brut
 */
class QuizSoapController extends Controller
{
    /**
     * Serveur SOAP
     */
    public function server()
    {
        // Si ?wsdl est demandé, on retourne le fichier WSDL
        if (request()->has('wsdl')) {
            $wsdlPath = resource_path('wsdl/quiz.wsdl');
            if (file_exists($wsdlPath)) {
                return response()->file($wsdlPath, ['Content-Type' => 'text/xml']);
            }
        }

        // Sinon, on initialise le serveur avec le WSDL
        $options = [
            'uri' => 'http://localhost:8000/soap/quiz', // Fallback
            'cache_wsdl' => WSDL_CACHE_NONE
        ];

        try {
            $wsdlPath = resource_path('wsdl/quiz.wsdl');
            $server = new SoapServer($wsdlPath, $options);
            $server->setObject($this);
            $server->handle();
        } catch (\Exception $e) {
            Log::error('SOAP Server Error: ' . $e->getMessage());
            return response()->make('SOAP Error', 500);
        }
        exit;
    }

    /**
     * Génère un quiz
     */
    public function GenerateQuiz($numberOfQuestions, $subjectId = null, $difficultyId = null, $questionTypeId = null)
    {
        try {
            $numberOfQuestions = (int) $numberOfQuestions;

            if ($numberOfQuestions < 1 || $numberOfQuestions > 100) {
                throw new SoapFault('Client', 'Le nombre de questions doit être entre 1 et 100');
            }

            $query = Question::with([
                'difficulty:id,name,point',
                'subject:id,name',
                'question_type:id,name',
                'answers:id,question_id,answer'
            ]);

            if ($subjectId) {
                $query->where('subject_id', (int) $subjectId);
            }

            if ($difficultyId) {
                $query->where('difficulty_id', (int) $difficultyId);
            }

            if ($questionTypeId) {
                $query->where('question_type_id', (int) $questionTypeId);
            }

            $questions = $query->inRandomOrder()
                ->limit($numberOfQuestions)
                ->get();

            $formattedQuestions = [];

            foreach ($questions as $question) {
                $formattedQuestions[] = [
                    'id' => $question->id,
                    'question' => $question->question,
                    'subject' => $question->subject?->name ?? '',
                    'difficulty' => $question->difficulty?->name ?? '',
                    'question_type' => $question->question_type?->name ?? '',
                    'proposal1' => $question->proposal_1 ?? '',
                    'proposal2' => $question->proposal_2 ?? '',
                    'proposal3' => $question->proposal_3 ?? '',
                    'proposal4' => $question->proposal_4 ?? '',
                    'correct_answer' => (int) ($question->answers->first()?->answer ?? 0)
                ];
            }

            return [
                'total' => count($formattedQuestions),
                'questions' => $formattedQuestions
            ];

        } catch (SoapFault $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new SoapFault('Server', $e->getMessage());
        }
    }

    /**
     * Soumet les réponses d'un quiz et retourne la correction
     *
     * @param int $userId ID de l'utilisateur
     * @param string $quizName Nom du quiz (généré automatiquement)
     * @param array $answers Tableau [questionId => numeroReponse] où numeroReponse est 1, 2, 3 ou 4
     * @return array Score et correction détaillée
     */
    public function SubmitQuizAnswers($userId, $quizName, $answers)
    {
        try {
            $userId = (int) $userId;

            // Vérifier l'utilisateur
            $user = User::find($userId);
            if (!$user) {
                throw new SoapFault('Client', 'Utilisateur introuvable');
            }

            if (!is_array($answers) || empty($answers)) {
                throw new SoapFault('Client', 'Aucune réponse fournie');
            }

            // Récupérer les questions avec leurs bonnes réponses
            $questionIds = array_keys($answers);
            $questions = Question::with([
                'answers:id,question_id,answer',
                'difficulty:id,name,point'
            ])->whereIn('id', $questionIds)->get();

            $score = 0;
            $maxScore = 0;
            $correction = [];

            foreach ($questions as $question) {
                $userAnswer = (int) $answers[$question->id]; // 1, 2, 3 ou 4
                $correctAnswer = (int) $question->answers->first()?->answer;
                $isCorrect = ($userAnswer === $correctAnswer);
                $points = $question->difficulty?->point ?? 1;

                $maxScore += $points;
                if ($isCorrect) {
                    $score += $points;
                }

                $correction[] = [
                    'question_id' => $question->id,
                    'question' => $question->question,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                    'is_correct' => $isCorrect,
                    'points' => $isCorrect ? $points : 0
                ];
            }

            // Enregistrer le résultat
            $userQuiz = UserQuiz::create([
                'user_id' => $userId,
                'quiz_id' => null,
                'score' => $score,
                'max_score' => $maxScore,
                'started_at' => now(),
                'completed_at' => now(),
                'status' => 'completed'
            ]);

            return [
                'user_quiz_id' => $userQuiz->id,
                'user_name' => $user->name,
                'quiz_name' => $quizName,
                'score' => $score,
                'max_score' => $maxScore,
                'percentage' => $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0,
                'total_questions' => count($correction),
                'correct_answers' => count(array_filter($correction, fn($c) => $c['is_correct'])),
                'correction' => $correction
            ];

        } catch (SoapFault $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new SoapFault('Server', $e->getMessage());
        }
    }

    /**
     * Récupère l'historique des quiz d'un utilisateur
     *
     * @param int $userId ID de l'utilisateur
     * @return array Historique des quiz
     */
    public function GetUserQuizHistory($userId)
    {
        try {
            $userId = (int) $userId;

            $user = User::find($userId);
            if (!$user) {
                throw new SoapFault('Client', 'Utilisateur introuvable');
            }

            $history = UserQuiz::where('user_id', $userId)
                ->where('status', 'completed')
                ->orderBy('completed_at', 'desc')
                ->get();

            $formattedHistory = [];
            foreach ($history as $userQuiz) {
                $formattedHistory[] = [
                    'id' => $userQuiz->id,
                    'score' => $userQuiz->score,
                    'max_score' => $userQuiz->max_score,
                    'percentage' => $userQuiz->max_score > 0 ? round(($userQuiz->score / $userQuiz->max_score) * 100, 2) : 0,
                    'completed_at' => $userQuiz->completed_at->format('Y-m-d H:i:s')
                ];
            }

            return [
                'user_name' => $user->name,
                'total_quiz' => count($formattedHistory),
                'history' => $formattedHistory
            ];

        } catch (SoapFault $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new SoapFault('Server', $e->getMessage());
        }
    }

    /**
     * Récupère le classement général (top scores)
     *
     * @param int $limit Nombre de résultats (défaut: 10)
     * @return array Classement
     */
    public function GetQuizLeaderboard($limit = 10)
    {
        try {
            $limit = min((int) $limit, 100);

            $leaderboard = UserQuiz::with('user:id,name')
                ->where('status', 'completed')
                ->orderBy('score', 'desc')
                ->orderBy('completed_at', 'asc')
                ->limit($limit)
                ->get();

            $formattedLeaderboard = [];
            $rank = 1;

            foreach ($leaderboard as $userQuiz) {
                $formattedLeaderboard[] = [
                    'rank' => $rank++,
                    'user_name' => $userQuiz->user?->name ?? 'Inconnu',
                    'score' => $userQuiz->score,
                    'max_score' => $userQuiz->max_score,
                    'percentage' => $userQuiz->max_score > 0 ? round(($userQuiz->score / $userQuiz->max_score) * 100, 2) : 0,
                    'completed_at' => $userQuiz->completed_at->format('Y-m-d H:i:s')
                ];
            }

            return [
                'total' => count($formattedLeaderboard),
                'leaderboard' => $formattedLeaderboard
            ];

        } catch (SoapFault $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new SoapFault('Server', $e->getMessage());
        }
    }
}
