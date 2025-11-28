<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\QuizSoapController;
use SoapFault;

class SoapDocumentationController extends Controller
{
    protected QuizSoapController $soapController;

    public function __construct(QuizSoapController $soapController)
    {
        $this->soapController = $soapController;
    }

    /**
     * Affiche la documentation SOAP interactive
     */
    public function index()
    {
        return view('soap-documentation');
    }

    /**
     * Endpoint pour tester les méthodes SOAP depuis l'interface web
     */
    public function testMethod(Request $request)
    {
        try {
            $method = $request->input('method');
            $params = $request->input('params', []);

            // Préparer les paramètres selon la méthode
            $soapParams = $this->prepareSoapParams($method, $params);

            // Appeler directement la méthode du contrôleur SOAP
            $result = $this->callSoapMethod($method, $soapParams);

            return response()->json($result);
        } catch (SoapFault $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'faultstring' => $e->faultstring ?? null,
                'faultcode' => $e->faultcode ?? null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Appelle la méthode SOAP appropriée
     */
    private function callSoapMethod(string $method, array $params)
    {
        return match ($method) {
            'GenerateQuiz' => $this->soapController->GenerateQuiz(...$params),
            'GetQuizStatistics' => $this->soapController->GetQuizStatistics(),
            'CreateQuiz' => $this->soapController->CreateQuiz(...$params),
            'StartQuiz' => $this->soapController->StartQuiz(...$params),
            'SubmitQuizAnswers' => $this->soapController->SubmitQuizAnswers(...$params),
            'GetUserQuizHistory' => $this->soapController->GetUserQuizHistory(...$params),
            'GetQuizLeaderboard' => $this->soapController->GetQuizLeaderboard(...$params),
            default => throw new \Exception("Méthode SOAP '$method' inconnue")
        };
    }

    /**
     * Prépare les paramètres pour l'appel SOAP
     */
    private function prepareSoapParams(string $method, array $params): array
    {
        // Pour SOAP en mode non-WSDL, on passe les paramètres dans l'ordre
        switch ($method) {
            case 'GenerateQuiz':
                return [
                    $params['numberOfQuestions'] ?? 10,
                    $params['subjectId'] ?? null,
                    $params['difficultyId'] ?? null,
                    $params['questionTypeId'] ?? null
                ];

            case 'GetQuizStatistics':
                return [];

            case 'CreateQuiz':
                return [
                    $params['name'] ?? '',
                    $params['description'] ?? '',
                    $params['questionIds'] ?? []
                ];

            case 'StartQuiz':
                return [
                    $params['userId'] ?? null,
                    $params['quizId'] ?? null
                ];

            case 'SubmitQuizAnswers':
                return [
                    $params['userQuizId'] ?? null,
                    $params['answers'] ?? []
                ];

            case 'GetUserQuizHistory':
                return [
                    $params['userId'] ?? null
                ];

            case 'GetQuizLeaderboard':
                return [
                    $params['quizId'] ?? null,
                    $params['limit'] ?? 10
                ];

            default:
                return array_values($params);
        }
    }
}
