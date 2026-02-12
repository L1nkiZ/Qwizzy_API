<?php

namespace App\Http\Controllers;

use App\Services\QuizGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur REST pour la génération de quiz
 *
 * @OA\Tag(
 *     name="Quiz",
 *     description="Endpoints pour générer et gérer des quiz"
 * )
 */
class QuizController extends Controller
{
    protected QuizGeneratorService $quizService;

    public function __construct(QuizGeneratorService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Crée un nouveau quiz (CRUD)
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quiz = \App\Models\Quiz::create($request->only(['name', 'description']));

        return response()->json(['message' => 'Quiz créé', 'quiz' => $quiz], 201);
    }

    /**
     * Met à jour un quiz (CRUD)
     */
    public function update(Request $request, $id) {
        $quiz = \App\Models\Quiz::find($id);
        if (!$quiz) return response()->json(['error' => 'Quiz introuvable'], 404);

        $quiz->update($request->only(['name', 'description']));

        return response()->json(['message' => 'Quiz mis à jour', 'quiz' => $quiz]);
    }

    /**
     * Supprime un quiz (CRUD)
     */
    public function destroy($id) {
        $quiz = \App\Models\Quiz::find($id);
        if (!$quiz) return response()->json(['error' => 'Quiz introuvable'], 404);

        $quiz->delete();
        return response()->json(['message' => 'Quiz supprimé']);
    }

    /**
     * Ajoute des questions à un quiz existant
     */
    public function addQuestions(Request $request, $id) {
        $quiz = \App\Models\Quiz::find($id);
        if (!$quiz) return response()->json(['error' => 'Quiz introuvable'], 404);

        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:question,id'
        ]);

        $quiz->questions()->syncWithoutDetaching($request->question_ids);

        return response()->json(['message' => 'Questions ajoutées au quiz']);
    }

    /**
     * Génère un quiz personnalisé
     *
     * @OA\Post(
     *      path="/api/quiz/generate",
     *      operationId="generateQuiz",
     *      tags={"Quiz"},
     *      summary="Générer un quiz personnalisé",
     *      description="Génère un quiz avec un nombre spécifié de questions, filtré par thème, difficulté et/ou type de question",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"numberOfQuestions"},
     *              @OA\Property(property="numberOfQuestions", type="integer", example=10, description="Nombre de questions souhaitées (1-100)"),
     *              @OA\Property(property="subjectId", type="integer", example=1, description="ID du thème (optionnel)"),
     *              @OA\Property(property="difficultyId", type="integer", example=2, description="ID de la difficulté (optionnel)"),
     *              @OA\Property(property="questionTypeId", type="integer", example=1, description="ID du type de question (optionnel)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Quiz généré avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Quiz généré avec succès"),
     *              @OA\Property(property="quiz", type="object",
     *                  @OA\Property(property="metadata", type="object",
     *                      @OA\Property(property="total_questions", type="integer", example=10),
     *                      @OA\Property(property="subject_id", type="integer", example=1),
     *                      @OA\Property(property="difficulty_id", type="integer", example=2),
     *                      @OA\Property(property="question_type_id", type="integer", example=1),
     *                      @OA\Property(property="generated_at", type="string", example="2025-11-28T10:30:00Z")
     *                  ),
     *                  @OA\Property(property="questions", type="array",
     *                      @OA\Items(type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="question", type="string", example="Quelle est la capitale de la France ?"),
     *                          @OA\Property(property="subject", type="string", example="Géographie"),
     *                          @OA\Property(property="difficulty", type="string", example="Facile"),
     *                          @OA\Property(property="question_type", type="string", example="QCM"),
     *                          @OA\Property(property="proposals", type="array",
     *                              @OA\Items(type="string", example="Paris")
     *                          ),
     *                          @OA\Property(property="answers", type="array",
     *                              @OA\Items(type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="answer", type="string", example="Paris"),
     *                                  @OA\Property(property="is_correct", type="boolean", example=true)
     *                              )
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Erreur de validation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="error", type="string", example="Le nombre de questions doit être supérieur à 0")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation des données",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Les données fournies sont invalides"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function generate(Request $request): JsonResponse
    {
        // Validation des données d'entrée
        $validated = $request->validate([
            'numberOfQuestions' => 'required|integer|min:1|max:100',
            'subjectId' => 'nullable|integer|exists:subject,id',
            'difficultyId' => 'nullable|integer|exists:difficulty,id',
            'questionTypeId' => 'nullable|integer|exists:question_type,id',
        ], [
            'numberOfQuestions.required' => 'Le nombre de questions est requis',
            'numberOfQuestions.integer' => 'Le nombre de questions doit être un entier',
            'numberOfQuestions.min' => 'Le nombre de questions doit être au moins 1',
            'numberOfQuestions.max' => 'Le nombre de questions ne peut pas dépasser 100',
            'subjectId.exists' => 'Le thème spécifié n\'existe pas',
            'difficultyId.exists' => 'La difficulté spécifiée n\'existe pas',
            'questionTypeId.exists' => 'Le type de question spécifié n\'existe pas',
        ]);

        $result = $this->quizService->generateQuiz($validated);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => is_array($result['error']) ? implode(', ', $result['error']) : $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Quiz généré avec succès',
            'quiz' => $result['quiz']
        ]);
    }

    /**
     * Récupère les statistiques des questions disponibles
     *
     * @OA\Get(
     *      path="/api/quiz/statistics",
     *      operationId="getQuizStatistics",
     *      tags={"Quiz"},
     *      summary="Obtenir les statistiques des questions",
     *      description="Retourne les statistiques sur le nombre de questions disponibles par thème et par difficulté",
     *      @OA\Response(
     *          response=200,
     *          description="Statistiques récupérées avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="statistics", type="object",
     *                  @OA\Property(property="total_questions", type="integer", example=150),
     *                  @OA\Property(property="by_subject", type="array",
     *                      @OA\Items(type="object",
     *                          @OA\Property(property="subject_id", type="integer", example=1),
     *                          @OA\Property(property="subject_name", type="string", example="Géographie"),
     *                          @OA\Property(property="count", type="integer", example=50)
     *                      )
     *                  ),
     *                  @OA\Property(property="by_difficulty", type="array",
     *                      @OA\Items(type="object",
     *                          @OA\Property(property="difficulty_id", type="integer", example=1),
     *                          @OA\Property(property="difficulty_name", type="string", example="Facile"),
     *                          @OA\Property(property="count", type="integer", example=60)
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Erreur serveur",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="error", type="string", example="Erreur lors de la récupération des statistiques")
     *          )
     *      )
     * )
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->quizService->getQuizStatistics();

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }
}
