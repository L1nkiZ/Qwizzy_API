<?php

namespace App\Http\Controllers;

use App\Http\Traits\ErrorTrait;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    use ErrorTrait;

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/subjects",
     *      operationId="getSubjectsList",
     *      tags={"Subject"},
     *      summary="Obtenir la liste des sujets",
     *      description="Retourne la liste paginée des sujets",
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
     *          description="Liste des sujets récupérée avec succès",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="subject", type="object")
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

        $subject = Subject::select('id', 'name')
            ->orderBy($sortColumn, $sortDir)
            ->paginate($perPage);

        return response()->json(compact('subject'));
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
     *      path="/api/subjects",
     *      operationId="storeSubject",
     *      tags={"Subject"},
     *      summary="Créer un nouveau sujet",
     *      description="Crée un nouveau sujet",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name"},
     *
     *              @OA\Property(property="name", type="string", maxLength=200, example="Mathématiques"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Sujet créé avec succès",
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
            'name' => 'required|string|max:200|unique:subject,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ]);
        }

        $subject = Subject::create([
            'name' => $request->name,
        ]);

        if ($subject) {
            return response()->json($this->success('Le sujet', 'créé', $subject));
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
        $subject = Subject::find($id);

        if ($subject) {
            return response()->json(compact('subject'));
        }

        return response()->json($this->error);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *      path="/api/subjects/{id}",
     *      operationId="updateSubject",
     *      tags={"Subject"},
     *      summary="Mettre à jour un sujet",
     *      description="Met à jour un sujet existant",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID du sujet",
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
     *              @OA\Property(property="name", type="string", maxLength=200, example="Histoire"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Sujet modifié avec succès",
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
     *          description="Sujet non trouvé"
     *      )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:subject,name,'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ]);
        }

        $subject = Subject::find($id);

        if ($subject) {
            $subject->update($request->all());

            return response()->json($this->success('Le sujet', 'modifié', $subject));
        }

        return response()->json($this->error);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *      path="/api/subjects/{id}",
     *      operationId="deleteSubject",
     *      tags={"Subject"},
     *      summary="Supprimer un sujet",
     *      description="Supprime un sujet existant",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID du sujet",
     *          required=true,
     *
     *          @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Sujet supprimé avec succès",
     *
     *          @OA\JsonContent()
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Sujet non trouvé"
     *      )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $subject = Subject::find($id);

        if ($subject) {
            if ($subject->questions()->exists()) {
                return response([
                    'error' => true,
                    'message' => 'Une ou plusieurs questions sont reliés à ce sujet. Vous ne pouvez pas le supprimer.',
                ]);
            }

            $subject->delete();

            return response()->json($this->success('Le sujet', 'supprimé'));
        }

        return response()->json($this->error);
    }
}
