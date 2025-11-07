<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/answers",
     *      operationId="getAnswersList",
     *      tags={"Answer"},
     *      summary="Obtenir la liste des réponses",
     *      description="Retourne la liste de toutes les réponses avec leurs questions associées. Le champ 'answer' contient le numéro de la bonne proposition (1 à 4)",
     *      @OA\Response(
     *          response=200,
     *          description="Liste des réponses récupérée avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="answers",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="question_id", type="integer", example=1),
     *                      @OA\Property(property="answer", type="integer", minimum=1, maximum=4, example=1, description="Numéro de la proposition correcte (1 à 4)"),
     *                      @OA\Property(property="created_at", type="string", format="date-time"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time"),
     *                      @OA\Property(
     *                          property="question",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="question", type="string", example="Quel est le nom du personnage principal de la série The Witcher ?"),
     *                          @OA\Property(property="proposal_1", type="string", example="Geralt de Riv"),
     *                          @OA\Property(property="proposal_2", type="string", example="Yennefer"),
     *                          @OA\Property(property="proposal_3", type="string", example="Ciri"),
     *                          @OA\Property(property="proposal_4", type="string", example="Jaskier"),
     *                      )
     *                  )
     *              )
     *          )
     *       )
     * )
     */
    public function index()
    {
        $answers = Answer::with(['question'])->get();
        return response()->json(compact('answers'));
    }
}
