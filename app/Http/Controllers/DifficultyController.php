<?php

namespace App\Http\Controllers;

use App\Http\Traits\ErrorTrait;
use App\Models\Difficulty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DifficultyController extends Controller
{
    use ErrorTrait;

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/difficulties",
     *      operationId="getDifficultiesList",
     *      tags={"Difficulty"},
     *      summary="Obtenir la liste des difficultés",
     *      description="Retourne la liste paginée des niveaux de difficulté",
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
     *          description="Liste des difficultés récupérée avec succès",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="difficulty", type="object")
     *          )
     *       )
     * )
     */
    public function index(Request $request)
    {
        // Whitelist des colonnes autorisées pour éviter les injections SQL
        $allowedColumns = ['id', 'name', 'point', 'created_at', 'updated_at'];
        $sortColumn = in_array($request->current_sort, $allowedColumns) ? $request->current_sort : 'id';
        $sortDir = in_array(strtolower($request->current_sort_dir), ['asc', 'desc']) ? $request->current_sort_dir : 'asc';
        $perPage = max(1, min((int)$request->per_page, 100)); // Limiter entre 1 et 100

        $difficulty = Difficulty::select('id', 'name', 'point')
            ->orderBy($sortColumn, $sortDir)
            ->paginate($perPage);

        return response()->json(compact('difficulty'));
    }

    /**
     * Show the form for creating a new resource.dock
     */
    public function create()
    {
        //
    }

    /**
     * Récupère une difficulté par son ID.
     *
     * @OA\Get(
     *     path="/api/difficulties/{id}",
     *     operationId="getDifficulty",
     *     tags={"Difficulty"},
     *     summary="Récupérer une difficulté",
     *     description="Retourne les détails d'une difficulté par son ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la difficulté",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Difficulté récupérée avec succès",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="difficulty", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Difficulté non trouvée"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:difficulty,name',
            'point' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ]);
        }

        $difficulty = Difficulty::create([
            'name' => $request->name,
            'point' => $request->point,
        ]);

        if ($difficulty) {
            return response()->json($this->success('La difficulté', 'créée', $difficulty));
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
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/api/difficulties",
     *      operationId="storeDifficulty",
     *      tags={"Difficulty"},
     *      summary="Créer une nouvelle difficulté",
     *      description="Crée un nouveau niveau de difficulté",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name","point"},
     *
     *              @OA\Property(property="name", type="string", maxLength=200, example="Facile"),
     *              @OA\Property(property="point", type="integer", minimum=1, maximum=5, example=1),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Difficulté créée avec succès",
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
    public function edit(string $id)
    {
        $difficulty = Difficulty::find($id);

        if ($difficulty) {
            return response()->json(compact('difficulty'));
        }

        return response()->json($this->error);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *      path="/api/difficulties/{id}",
     *      operationId="updateDifficulty",
     *      tags={"Difficulty"},
     *      summary="Mettre à jour une difficulté",
     *      description="Met à jour un niveau de difficulté existant",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID de la difficulté",
     *          required=true,
     *
     *          @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"name","point"},
     *
     *              @OA\Property(property="name", type="string", maxLength=200, example="Moyen"),
     *              @OA\Property(property="point", type="integer", minimum=1, maximum=5, example=3),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Difficulté modifiée avec succès",
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
     *          description="Difficulté non trouvée"
     *      )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:difficulty,name,'.$id,
            'point' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ]);
        }

        $difficulty = Difficulty::find($id);

        if ($difficulty) {
            $difficulty->update($request->all());

            return response()->json($this->success('La difficulté', 'modifiée', $difficulty));
        }

        return response()->json($this->error);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *      path="/api/difficulties/{id}",
     *      operationId="deleteDifficulty",
     *      tags={"Difficulty"},
     *      summary="Supprimer une difficulté",
     *      description="Supprime un niveau de difficulté existant",
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID de la difficulté",
     *          required=true,
     *
     *          @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Difficulté supprimée avec succès",
     *
     *          @OA\JsonContent()
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="Difficulté non trouvée"
     *      )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $difficulty = Difficulty::find($id);

        if ($difficulty) {
            if ($difficulty->questions()->exists()) {
                return response([
                    'error' => true,
                    'message' => 'Une ou plusieurs questions sont reliés à cette difficulté. Vous ne pouvez pas la supprimer.',
                ]);
            }

            $difficulty->delete();

            return response()->json($this->success('La difficulté', 'supprimée'));
        }

        return response()->json($this->error);
    }
}
