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
            $params = $request->input('params', []);

            $result = $this->soapController->GenerateQuiz(
                $params['numberOfQuestions'] ?? 10,
                $params['subjectId'] ?? null,
                $params['difficultyId'] ?? null,
                $params['questionTypeId'] ?? null
            );

            // Convertir en XML
            $xml = $this->arrayToXml($result, 'response');

            return response()->json([
                'xml' => $xml,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
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
