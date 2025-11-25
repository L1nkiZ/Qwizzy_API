<?php

namespace App\Http\Controllers;

use App\Http\Traits\ErrorTrait;
use App\Models\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionTypeController extends Controller
{
    use ErrorTrait;

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/question-types",
     *      operationId="getQuestionTypesList",
     *      tags={"QuestionType"},
     *      summary="Obtenir la liste des types de questions",
     *      description="Retourne la liste paginée des types de questions",
     *
     *      @OA\Parameter(
     *          name="current_sort",
     *          description="Champ de tri",
     *          required=false,
     *          in="query",
     *
     *          @OA\Schema(type="string", default="id")
     *      ),
     *
     *      @OA\Parameter(
     *          name="current_sort_dir",
     *          description="Direction du tri",
     *          required=false,
     *          in="query",
     *
     *          @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *      ),
     *
     *      @OA\Parameter(
     *          name="per_page",
     *          description="Nombre d'éléments par page",
     *          required=false,
     *          in="query",
     *
     *          @OA\Schema(type="integer", default=15)
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Liste des types de questions récupérée avec succès",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="questionType", type="object")
     *          )
     *       )
     * )
     */
    public function index(Request $request)
    {
        // Whitelist des colonnes autorisées pour éviter les injections SQL
        $allowedColumns = ['id', 'name', 'created_at', 'updated_at'];
        $sortColumn = in_array($request->current_sort, $allowedColumns) ? $request->current_sort : 'id';
        $sortDir = in_array(strtolower($request->current_sort_dir), ['asc', 'desc']) ? $request->current_sort_dir : 'asc';
        $perPage = max(1, min((int)$request->per_page, 100)); // Limiter entre 1 et 100

        $questionType = QuestionType::select('id', 'name')
            ->orderBy($sortColumn, $sortDir)
            ->paginate($perPage);

        return response()->json(compact('questionType'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/api/question-types",
     *      operationId="storeQuestionType",
     *      tags={"QuestionType"},
     *      summary="Créer un nouveau type de question",
     *      description="Crée un nouveau type de question",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name"},
     *
     *              @OA\Property(property="name", type="string", maxLength=200, example="QCM"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Type de question créé avec succès",
     *
     *          @OA\JsonContent()
     *       ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:question_type,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ]);
        }

        $questionType = QuestionType::create([
            'name' => $request->name,
        ]);

        if ($questionType) {
            return response()->json($this->success('Le type de question', 'créé', $questionType));
        }

        return response()->json($this->error);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $questionType = QuestionType::find($id);

        if ($questionType) {
            return response()->json(compact('questionType'));
        }

        return response()->json($this->error);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *      path="/api/question-types/{id}",
     *      operationId="updateQuestionType",
     *      tags={"QuestionType"},
     *      summary="Mettre à jour un type de question",
     *      description="Met à jour un type de question existant",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID du type de question",
     *          required=true,
     *
     *          @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name"},
     *
     *              @OA\Property(property="name", type="string", maxLength=200, example="QCM modifié"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Type de question modifié avec succès",
     *
     *          @OA\JsonContent()
     *       ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Type de question non trouvé"
     *      )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:question_type,name,'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ]);
        }

        $questionType = QuestionType::find($id);

        if ($questionType) {
            $questionType->update($request->all());

            return response()->json($this->success('Le type de question', 'modifié', $questionType));
        }

        return response()->json($this->error);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *      path="/api/question-types/{id}",
     *      operationId="deleteQuestionType",
     *      tags={"QuestionType"},
     *      summary="Supprimer un type de question",
     *      description="Supprime un type de question existant",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID du type de question",
     *          required=true,
     *
     *          @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Type de question supprimé avec succès",
     *
     *          @OA\JsonContent()
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Type de question non trouvé"
     *      )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $questionType = QuestionType::find($id);

        if ($questionType) {
            if ($questionType->questions()->exists()) {
                return response([
                    'error' => true,
                    'message' => 'Une ou plusieurs questions sont reliés à ce type. Vous ne pouvez pas le supprimer.',
                ]);
            }

            $questionType->delete();

            return response()->json($this->success('Le type de question', 'supprimé'));
        }

        return response()->json($this->error);
    }
}
