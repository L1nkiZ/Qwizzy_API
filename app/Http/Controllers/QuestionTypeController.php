<?php

namespace App\Http\Controllers;

use App\Models\QuestionType;
use Illuminate\Http\Request;


class QuestionTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/question-types",
     *     summary="Afficher la liste des types de questions",
     *     tags={"QuestionType"},
     *     @OA\Parameter(
     *         name="current_sort",
     *         in="query",
     *         required=false,
     *         description="Champ de tri",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="current_sort_dir",
     *         in="query",
     *         required=false,
     *         description="Direction du tri (asc, desc)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Nombre d'éléments par page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste paginée des types de questions"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $questionType =
            QuestionType::select('id', 'name')
            ->orderby($request->current_sort, $request->current_sort_dir)
            ->paginate($request->per_page);

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
     * @OA\Post(
     *     path="/question-types",
     *     summary="Créer un nouveau type de question",
     *     tags={"QuestionType"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=200, example="QCM")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type de question créé avec succès"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:QuestionType,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ]);
        }

        $questionType = QuestionType::create([
            'name' => $request->name,
        ]);

        if ($questionType) {
            return response()->json($this->success("Le type de question", "créé", $questionType));
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
     * @OA\Put(
     *     path="/question-types/{id}",
     *     summary="Mettre à jour un type de question",
     *     tags={"QuestionType"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du type de question",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=200, example="QCM modifié")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type de question modifié avec succès"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ]);
        }

        $questionType = QuestionType::find($id);

        if ($questionType) {
            $questionType->update($request->all());
            return response()->json($this->success("Le type de question", "modifié", $questionType));
        }

        return response()->json($this->error);
    }

    /**
     * @OA\Delete(
     *     path="/question-types/{id}",
     *     summary="Supprimer un type de question",
     *     tags={"QuestionType"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du type de question",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type de question supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Type de question non trouvé"
     *     )
     * )
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $questionType = QuestionType::find($id);

        if ($questionType) {
            if ($questionType->questions()->exists()) {
                return response([
                    'error' => true,
                    'message' => "Une ou plusieurs questions sont reliés à ce type. Vous ne pouvez pas le supprimer."
                ]);
            }

            $questionType->delete();

            return response()->json($this->success("Le type de question", "supprimé"));
        }

        return response()->json($this->error);
    }
}
