<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use App\Http\Traits\ErrorTrait;

use App\Models\Question;
use App\Models\Difficulty;
use App\Models\Subject;
use App\Models\QuestionType;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/questions",
     *      operationId="getQuestionsList",
     *      tags={"Question"},
     *      summary="Obtenir la liste des questions",
     *      description="Retourne la liste paginée des questions avec leurs relations",
     *      @OA\Parameter(
     *          name="current_sort",
     *          description="Champ de tri",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", default="id")
     *      ),
     *      @OA\Parameter(
     *          name="current_sort_dir",
     *          description="Direction du tri",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          description="Nombre d'éléments par page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="integer", default=15)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Liste des questions récupérée avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="questions", type="object")
     *          )
     *       )
     * )
     */
    public function index(Request $request)
    {
        $questions = Question::with([
            'difficulty' => function ($query) {
                $query->select('id', 'name');
            },
            'subject' => function ($query) {
                $query->select('id', 'name');
            },
            'question_type' => function ($query) {
                $query->select('id', 'name');
            },
        ])
        ->orderBy($request->current_sort, $request->current_sort_dir)
        ->paginate($request->per_page);

        return response()->json(compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @OA\Get(
     *      path="/api/questions/create",
     *      operationId="getQuestionCreateData",
     *      tags={"Question"},
     *      summary="Obtenir les données pour créer une question",
     *      description="Retourne les listes des difficultés, matières et types de questions disponibles",
     *      @OA\Response(
     *          response=200,
     *          description="Données récupérées avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="difficulties", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="subjects", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="question_types", type="array", @OA\Items(type="object"))
     *          )
     *       )
     * )
     */
    public function create()
    {
        $difficulties = Difficulty::select('id', 'name', 'point')
        ->orderBy('nom', 'asc')
        ->get();

        $subjects = Subject::select('id', 'name')
        ->orderBy('nom', 'asc')
        ->get();

        $question_types = QuestionType::select('id', 'name')
        ->orderBy('nom', 'asc')
        ->get();

        return response()->json(compact('difficulties', 'subjects', 'question_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/api/questions",
     *      operationId="storeQuestion",
     *      tags={"Question"},
     *      summary="Créer une nouvelle question",
     *      description="Crée une nouvelle question avec ses propositions et sa réponse",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"question","proposal_1","proposal_2","proposal_3","answer","subject_id","difficulty_id","question_type_id"},
     *              @OA\Property(property="question", type="string", maxLength=255, example="Quelle est la capitale de la France ?"),
     *              @OA\Property(property="proposal_1", type="string", example="Paris"),
     *              @OA\Property(property="proposal_2", type="string", example="Lyon"),
     *              @OA\Property(property="proposal_3", type="string", example="Marseille"),
     *              @OA\Property(property="answer", type="string", example="Paris"),
     *              @OA\Property(property="subject_id", type="integer", example=1),
     *              @OA\Property(property="difficulty_id", type="integer", example=1),
     *              @OA\Property(property="question_type_id", type="integer", example=1),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Question créée avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="question", type="object")
     *          )
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="object")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:500',
            'proposal_1' => 'required|string|max:100',
            'proposal_2' => 'required|string|max:100',
            'proposal_3' => 'required|string|max:100',
            'answer' => 'required|string|max:100',
            'subject_id' => 'required|exists:subject,id',
            'difficulty_id' => 'required|exists:difficulty,id',
            'question_type_id' => 'required|exists:question_type,id',
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ]);
        }
        else {
            $question = Question::create([
                'question' => $request->question,
                'subject_id' => $request->subject_id,
                'difficulty_id' => $request->difficulty_id,
                'question_type_id' => $request->question_type_id,
                'proposal_1' => $request->proposal_1,
                'proposal_2' => $request->proposal_2,
                'proposal_3' => $request->proposal_3,
                'answer' => $request->answer,
            ]);

            return response()->json(compact('question'));
        }
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @OA\Get(
     *      path="/api/questions/{id}/edit",
     *      operationId="getQuestionEditData",
     *      tags={"Question"},
     *      summary="Obtenir les données pour éditer une question",
     *      description="Retourne les listes des difficultés, matières et types de questions disponibles pour l'édition",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de la question",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Données récupérées avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="difficulties", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="subjects", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="question_types", type="array", @OA\Items(type="object"))
     *          )
     *       )
     * )
     */
    public function edit(string $id)
    {
        $difficulties = Difficulty::select('id', 'name', 'point')
        ->orderBy('nom', 'asc')
        ->get();

        $subjects = Subject::select('id', 'name')
        ->orderBy('nom', 'asc')
        ->get();

        $question_types = QuestionType::select('id', 'name')
        ->orderBy('nom', 'asc')
        ->get();

        return response()->json(compact('difficulties', 'subjects', 'question_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *      path="/api/questions/{id}",
     *      operationId="updateQuestion",
     *      tags={"Question"},
     *      summary="Mettre à jour une question",
     *      description="Met à jour une question existante",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de la question",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"question","proposal_1","proposal_2","proposal_3","answer","subject_id","difficulty_id","question_type_id"},
     *              @OA\Property(property="question", type="string", maxLength=255, example="Quelle est la capitale de la France ?"),
     *              @OA\Property(property="proposal_1", type="string", example="Paris"),
     *              @OA\Property(property="proposal_2", type="string", example="Lyon"),
     *              @OA\Property(property="proposal_3", type="string", example="Marseille"),
     *              @OA\Property(property="answer", type="string", example="Paris"),
     *              @OA\Property(property="subject_id", type="integer", example=1),
     *              @OA\Property(property="difficulty_id", type="integer", example=1),
     *              @OA\Property(property="question_type_id", type="integer", example=1),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Question mise à jour avec succès",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Question non trouvée"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="object")
     *          )
     *      )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'question' => 'required|max:500|unique:question,question',
            'proposal_1' => 'required|string|max:100',
            'proposal_2' => 'required|string|max:100',
            'proposal_3' => 'required|string|max:100',
            'answer' => 'required|string|max:100',
            'subject_id' => 'required|numeric|exists:subject,id',
            'difficulty_id' => 'required|numeric|exists:difficulty,id',
            'question_type_id' => 'required|numeric|exists:question_type,id',
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ]);
        }
        $question = Question::find($id);

        if($question){
            $question->update($request->all());

            return response()->json($this->success("La question", "Modifiée"));
        }

        return response()->json($this->error);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *      path="/api/questions/{id}",
     *      operationId="deleteQuestion",
     *      tags={"Question"},
     *      summary="Supprimer une question",
     *      description="Supprime une question existante",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de la question",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Question supprimée avec succès",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Question non trouvée"
     *      )
     * )
     */
    public function destroy(string $id)
    {
        $question = Question::find($id);

        if($question){
            $question->delete();

            return response()->json($this->success("La question a été", "supprimée"));
        }

        return response()->json($this->error);
    }
}
