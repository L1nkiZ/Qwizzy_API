<?php

namespace App\Http\Controllers;

use App\Models\Question;
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
        $server = new SoapServer(null, [
            'uri' => 'http://localhost:8000/soap/quiz'
        ]);

        $server->setObject($this);
        $server->handle();
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
                    'correct_answer' => $question->answers->first()?->answer ?? ''
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
}
