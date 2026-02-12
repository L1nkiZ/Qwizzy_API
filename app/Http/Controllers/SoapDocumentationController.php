<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\QuizSoapController;

class SoapDocumentationController extends Controller
{
    protected QuizSoapController $soapController;

    public function __construct(QuizSoapController $soapController)
    {
        $this->soapController = $soapController;
    }

    public function index()
    {
        return view('soap-documentation');
    }

    public function testMethod(Request $request)
    {
        try {
            $method = $request->input('method');
            $params = $request->input('params', []);
            $result = null;

            switch ($method) {
                case 'GenerateQuiz':
                    $result = $this->soapController->GenerateQuiz(
                        $params['numberOfQuestions'] ?? 10,
                        $params['subjectId'] ?? null,
                        $params['difficultyId'] ?? null,
                        $params['questionTypeId'] ?? null
                    );
                    break;

                case 'SubmitQuizAnswers':
                    // Mapping frontend userQuizId/answers to backend userId/quizName/answers
                    // Note: This matches the current QuizSoapController signature
                    $result = $this->soapController->SubmitQuizAnswers(
                        $params['userId'] ?? ($params['userQuizId'] ?? 1),
                        'Test Quiz',
                        $params['answers'] ?? []
                    );
                    break;

                case 'GetUserQuizHistory':
                    $result = $this->soapController->GetUserQuizHistory($params['userId'] ?? 1);
                    break;

                case 'GetQuizLeaderboard':
                    $result = $this->soapController->GetQuizLeaderboard($params['limit'] ?? 10);
                    break;

                default:
                     // Fallback for GenerateQuiz if method not matched (legacy behavior) or throw error
                     // Better to throw error for clarity
                     throw new \Exception("Method '$method' not implemented in test controller");
            }

            // Convertir en XML
            $xml = $this->arrayToXml($result, 'response');

            return response()->json([
                'xml' => $xml,
                'data' => $result
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SoapTest Error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 400);
        }
    }

    private function arrayToXml($data, $rootElement = 'root', $xml = null)
    {
        if ($xml === null) {
            $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><{$rootElement}></{$rootElement}>");
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item';
                }
                $subnode = $xml->addChild($key);
                $this->arrayToXml($value, $key, $subnode);
            } else {
                $xml->addChild($key, htmlspecialchars($value ?? ''));
            }
        }

        return $xml->asXML();
    }
}
