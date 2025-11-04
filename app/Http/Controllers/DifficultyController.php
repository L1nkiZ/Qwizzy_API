<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use App\Http\Traits\ErrorTrait;

use App\Models\Difficulty;

class DifficultyController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @OA\Get(
     *      path="/api/difficulties",
     *      operationId="getDifficultiesList",
     *      tags={"Difficulty"},
     *      summary="Obtenir la liste des difficultés",
     *      description="Retourne la liste paginée des niveaux de difficulté",
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
     *          description="Liste des difficultés récupérée avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="difficulty", type="object")
     *          )
     *       )
     * )
     */
    public function index(Request $request)
    {
        $difficulty =
        Difficulty::select('id', 'name', 'point')
        ->orderby($request->current_sort, $request->current_sort_dir)
        ->paginate($request->per_page);

        return response()->json(compact('difficulty'));
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
     *      path="/api/difficulties",
     *      operationId="storeDifficulty",
     *      tags={"Difficulty"},
     *      summary="Créer une nouvelle difficulté",
     *      description="Crée un nouveau niveau de difficulté",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","point"},
     *              @OA\Property(property="name", type="string", maxLength=200, example="Facile"),
     *              @OA\Property(property="point", type="integer", minimum=1, maximum=5, example=1),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Difficulté créée avec succès",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:200|unique:difficulties,name',
            'point' => 'required|integer|min:1|max:5',
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ]);
        }

        $difficulty = Difficulty::create([
            'name' => $request->name,
            'point' => $request->point,
        ]);

        if($difficulty){
            return response()->json($this->success("La difficulté", "créée", $difficulty));
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
        $difficulty = Difficulty::find($id);

        if($difficulty){
            return response()->json(compact('difficulty'));
        }

        return response()->json($this->error);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:200',
            'point' => 'required|integer|min:1|max:5',
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ]);
        }

        $difficulty = Difficulty::find($id);

        if($difficulty){
            $difficulty->update($request->all());
            return response()->json($this->success("La difficulté", "modifiée", $difficulty));
        }

        return response()->json($this->error);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $difficulty = Difficulty::find($id);

        if($difficulty){
            $difficulty->delete();

            return response()->json($this->success("La difficulté", "supprimée"));
        }

        return response()->json($this->error);
    }
}
