<?php

namespace App\Http\Controllers;

use App\Models\QuestionType;
use Illuminate\Http\Request;


class QuestionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $questionType = QuestionType::find($id);

        if ($questionType) {
            $questionType->delete();

            return response()->json($this->success("Le type de question", "supprimé"));
        }

        return response()->json($this->error);
    }
}
