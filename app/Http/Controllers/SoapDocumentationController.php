<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SoapClient;
use SoapFault;

class SoapDocumentationController extends Controller
{
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

            // Créer le client SOAP
            $client = new SoapClient(null, [
                'location' => url('/soap/quiz'),
                'uri' => url('/soap/quiz'),
                'trace' => 1,
                'exceptions' => true
            ]);

            // Appeler la méthode SOAP avec les paramètres
            $result = $client->__soapCall($method, $params);

            return response()->json($result);
        } catch (SoapFault $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'faultstring' => $e->faultstring ?? null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
