<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DifficultyController;
use App\Http\Controllers\QuestionTypeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;

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

Route::middleware('auth.token')->get('/user', function (Request $request) {
    //return $request->user();
    return ['user' => 'ok'];
});

// Routes pour les ressources
Route::apiResource('difficulties', DifficultyController::class);
Route::apiResource('question-types', QuestionTypeController::class);
Route::apiResource('subjects', SubjectController::class);

// Route spécifique pour create avant apiResource
Route::get('questions/create', [QuestionController::class, 'create']);
Route::get('questions/{id}/edit', [QuestionController::class, 'edit']);
// Filtrer les questions par id de thème (subject)
Route::get('questions/theme/{id}', [QuestionController::class, 'byTheme']);
Route::apiResource('questions', QuestionController::class);

Route::apiResource('answers', AnswerController::class);
