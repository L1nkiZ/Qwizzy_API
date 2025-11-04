<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DifficultyController;
use App\Http\Controllers\QuestionTypeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\QuestionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes pour les ressources
Route::apiResource('difficulties', DifficultyController::class);
Route::apiResource('question-types', QuestionTypeController::class);
Route::apiResource('subjects', SubjectController::class);
Route::apiResource('questions', QuestionController::class);
