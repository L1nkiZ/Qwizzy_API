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
