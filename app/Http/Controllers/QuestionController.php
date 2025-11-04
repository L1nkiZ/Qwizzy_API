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
     */
    public function index()
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
