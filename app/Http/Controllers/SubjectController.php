<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Subject",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string"),
     * )
     * @OA\Get(
     *     path="/api/subjects",
     *     summary="Retrieve a list of subjects",
     *     tags={"Subjects"},
     *     @OA\Parameter(
     *         name="current_sort",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="current_sort_dir",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", format="int32", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Subject")),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subjects not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subjects not found")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $subject =
            Subject::select('id', 'name')
            ->orderby($request->current_sort, $request->current_sort_dir)
            ->paginate($request->per_page);

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
     * @OA\Post(
     *     path="/api/subjects",
     *     summary="Create a new subject",
     *     tags={"Subjects"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","point"},
     *             @OA\Property(property="name", type="string", maxLength=200, example="Mathematics"),
     *             @OA\Property(property="point", type="integer", format="int32", minimum=1, maximum=5, example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Subject")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:Subject,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ]);
        }

        $subject = Subject::create([
            'name' => $request->name,
        ]);

        if ($subject) {
            return response()->json($this->success("Le sujet", "créé", $subject));
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
     * @OA\Put(
     *     path="/api/subjects/{id}",
     *     summary="Update an existing subject",
     *     tags={"Subjects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=200, example="Mathematics")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Subject")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subject not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subject not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="object")
     *         )
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

        $subject = Subject::find($id);

        if ($subject) {
            $subject->update($request->all());
            return response()->json($this->success("Le sujet", "modifié", $subject));
        }

        return response()->json($this->error);
    }

    /**
     * Remove the specified resource from storage.
     * @OA\Delete(
     *     path="/api/subjects/{id}",
     *     summary="Delete an existing subject",
     *     tags={"Subjects"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No Content"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subject not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subject not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $subject = Subject::find($id);

        if ($subject) {
            if ($subject->questions()->exists()) {
                return response([
                    'error' => true,
                    'message' => "Une ou plusieurs questions sont reliés à ce sujet. Vous ne pouvez pas le supprimer."
                ]);
            }

            $subject->delete();

            return response()->json($this->success("Le sujet", "supprimé"));
        }

        return response()->json($this->error);
    }
}
