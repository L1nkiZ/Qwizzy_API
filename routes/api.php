<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\DifficultyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionTypeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;

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

// Routes pour les ressources
Route::apiResource('difficulties', DifficultyController::class);
Route::apiResource('question-types', QuestionTypeController::class);
Route::apiResource('subjects', SubjectController::class);
Route::apiResource('questions', QuestionController::class);
Route::apiResource('answers', AnswerController::class);
Route::apiResource('users', UserController::class);


// Route spécifique pour create avant apiResource
Route::get('questions/create', [QuestionController::class, 'create']);
Route::get('questions/{id}/edit', [QuestionController::class, 'edit'])->whereNumber('id');
Route::get('questions/show/{id}', [QuestionController::class, 'show'])->whereNumber('id');
Route::get('questions/by-theme', [QuestionController::class, 'byTheme']);
// Recherche par id de thème (subject_id)
Route::get('questions/theme/{id}', [QuestionController::class, 'byThemeId'])->whereNumber('id');

Route::post('auth/register', [UserController::class, 'store']);
Route::post('auth/login', [UserController::class, 'login']);

//groupe for auth.token
Route::middleware('auth.token')->group(function () {
    Route::post('questions', [QuestionController::class, 'store']);
    Route::put('questions/{id}', [QuestionController::class, 'update'])->whereNumber('id');
    Route::post('auth/logout', [UserController::class, 'logout']);
});
