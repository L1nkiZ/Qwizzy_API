<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controleur REST pour le mode jeu
 *
 * @OA\Tag(
 *     name="Game",
 *     description="Endpoints jeu style quiz: generation de questions par theme ou aleatoire et recuperation de la bonne reponse"
 * )
 */
class GameController extends Controller
{
    /**
     * Retourne un lot de questions pour le jeu
     *
     * @OA\Post(
     *      path="/api/game/getQuestions",
     *      operationId="getGameQuestions",
     *      tags={"Game"},
     *      summary="Recuperer un lot de questions",
    *      description="Genere un lot de questions sur un theme precise ou en mix aleatoire sur tous les themes. Cette route ne retourne pas les propositions",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
    *              required={"numberOfQuestions"},
     *              @OA\Property(property="numberOfQuestions", type="integer", minimum=1, maximum=100, example=10),
    *              @OA\Property(property="subjectId", type="integer", nullable=true, example=1, description="ID du theme. Si absent, les questions sont melangees sur tous les themes")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Lot de questions recupere",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="total", type="integer", example=10),
     *              @OA\Property(property="questions", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="id", type="integer", example=12),
    *                      @OA\Property(property="title", type="string", example="Quelle est la capitale de la France ?"),
     *                      @OA\Property(property="subject", type="string", example="Geographie"),
     *                      @OA\Property(property="difficulty", type="string", example="Facile"),
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation"
     *      )
     * )
     */
    public function getQuestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'numberOfQuestions' => 'required|integer|min:1|max:100',
            'subjectId' => 'nullable|integer|exists:subject,id',
        ]);

        $questions = $this->buildQuestionQuery($validated)
            ->inRandomOrder()
            ->limit($validated['numberOfQuestions'])
            ->get();

        return response()->json([
            'success' => true,
            'total' => $questions->count(),
            'questions' => $questions->map(fn (Question $question) => $this->formatQuestionBase($question))->values(),
        ]);
    }

    /**
     * Retourne les propositions d'une question selon le mode choisi
     *
     * @OA\Post(
     *      path="/api/game/questions/options",
     *      operationId="getGameQuestionOptions",
     *      tags={"Game"},
     *      summary="Recuperer les propositions selon le mode",
     *      description="Retourne 2, 4 ou 0 propositions selon le mode choisi par l'utilisateur",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"question_id","mode"},
     *              @OA\Property(property="question_id", type="integer", example=12),
    *              @OA\Property(property="mode", type="integer", enum={1,2,3}, example=1, description="1=J'suis pas sûr, 2=J'crois je l'ai, 3=Let me cook")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Propositions recuperees",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="question_id", type="integer", example=12),
    *              @OA\Property(property="mode", type="integer", example=1),
     *              @OA\Property(property="mode_label", type="string", example="J'suis pas sûr"),
     *              @OA\Property(property="proposals", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="position", type="integer", example=1),
     *                      @OA\Property(property="label", type="string", example="Paris")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Question ou reponse introuvable"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation"
     *      )
     * )
     */
    public function getQuestionOptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|integer|exists:question,id',
            'mode' => 'required|integer|exists:question_type,id',
        ]);

        $questionType = QuestionType::find($validated['mode']);

        if (!$questionType) {
            return response()->json([
                'success' => false,
                'message' => 'Mode introuvable',
            ], 404);
        }

        $question = Question::with(['answers:id,question_id,answer'])->find($validated['question_id']);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question introuvable',
            ], 404);
        }

        $correctPosition = (int) optional($question->answers->first())->answer;

        if ($correctPosition < 1 || $correctPosition > 4) {
            return response()->json([
                'success' => false,
                'message' => 'Réponse correcte introuvable pour cette question',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'question_id' => (int) $validated['question_id'],
            'mode' => (int) $validated['mode'],
            'mode_label' => $this->modeLabel($questionType),
            'proposals' => $this->buildProposalsForMode($question, $correctPosition, (int) $validated['mode']),
        ]);
    }

    /**
     * Retourne la bonne reponse d'une question
     *
     * @OA\Post(
     *      path="/api/game/answers/check",
     *      operationId="checkGameAnswer",
     *      tags={"Game"},
     *      summary="Obtenir la bonne reponse",
     *      description="Retourne la bonne reponse associee a une question",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"question_id"},
     *              @OA\Property(property="question_id", type="integer", example=12)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Bonne reponse retournee",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="question_id", type="integer", example=12),
     *              @OA\Property(property="correct_answer_position", type="integer", example=2),
     *              @OA\Property(property="correct_answer_label", type="string", example="Paris")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Reponse introuvable"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation"
     *      )
     * )
     */
    public function checkAnswer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|integer|exists:question,id',
        ]);

        $question = Question::find($validated['question_id']);
        $answer = Answer::where('question_id', $validated['question_id'])->first();

        if (!$question || !$answer) {
            return response()->json([
                'success' => false,
                'message' => 'Réponse correcte introuvable pour cette question',
            ], 404);
        }

        $correctAnswerPosition = (int) $answer->answer;
        $correctAnswerLabel = $this->proposalLabelByPosition($question, $correctAnswerPosition);

        return response()->json([
            'success' => true,
            'question_id' => (int) $validated['question_id'],
            'correct_answer_position' => $correctAnswerPosition,
            'correct_answer_label' => $correctAnswerLabel,
        ]);
    }

    private function buildQuestionQuery(array $filters)
    {
        return Question::query()
            ->with(['subject:id,name', 'difficulty:id,name,point', 'question_type:id,name'])
            ->when(isset($filters['subjectId']), fn ($query) => $query->where('subject_id', $filters['subjectId']));
    }

    private function formatQuestionBase(Question $question): array
    {
        return [
            'id' => $question->id,
            'title' => $question->question,
            'subject' => optional($question->subject)->name,
            'difficulty' => optional($question->difficulty)->name,
            'question_type' => optional($question->question_type)->name,
        ];
    }

    private function buildProposalsForMode(Question $question, int $correctPosition, int $mode): array
    {
        $allProposals = [
            ['position' => 1, 'label' => $question->proposal_1],
            ['position' => 2, 'label' => $question->proposal_2],
            ['position' => 3, 'label' => $question->proposal_3],
            ['position' => 4, 'label' => $question->proposal_4],
        ];

        if ($mode === 3) {
            return [];
        }

        if ($mode === 2 || $correctPosition < 1 || $correctPosition > 4) {
            shuffle($allProposals);
            return $allProposals;
        }

        $correctProposal = collect($allProposals)->firstWhere('position', $correctPosition);
        $wrongProposals = collect($allProposals)
            ->reject(fn (array $proposal) => $proposal['position'] === $correctPosition)
            ->shuffle()
            ->values();

        $twoChoices = [$correctProposal, $wrongProposals->first()];
        shuffle($twoChoices);

        return $twoChoices;
    }

    private function proposalLabelByPosition(Question $question, int $position): ?string
    {
        return match ($position) {
            1 => $question->proposal_1,
            2 => $question->proposal_2,
            3 => $question->proposal_3,
            4 => $question->proposal_4,
            default => null,
        };
    }

    private function modeLabel(QuestionType $questionType): string
    {
        return $questionType->name;
    }
}
