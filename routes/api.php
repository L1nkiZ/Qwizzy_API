<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\DifficultyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionTypeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SoapDocumentationController;
use App\Http\Controllers\ImportExportController;

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

// Route spécifique pour create avant apiResource
Route::get('questions/create', [QuestionController::class, 'create']);
Route::get('questions/edit/{id}', [QuestionController::class, 'edit'])->whereNumber('id');
Route::get('questions/show/{id}', [QuestionController::class, 'show'])->whereNumber('id');
Route::get('questions/by-theme', [QuestionController::class, 'byTheme']);
// Recherche par id de thème (subject_id)
Route::get('questions/theme/{id}', [QuestionController::class, 'byThemeId'])->whereNumber('id');

// Metrics endpoint for Prometheus
Route::get('/metrics', [MetricsController::class, 'metrics']);

// Routes REST pour la génération et gestion de quiz
Route::post('quiz/generate', [QuizController::class, 'generate']);
Route::get('quiz/statistics', [QuizController::class, 'statistics']);

// CRUD Quiz
Route::post('quizzes', [QuizController::class, 'store']);
Route::put('quizzes/{id}', [QuizController::class, 'update'])->whereNumber('id');
Route::delete('quizzes/{id}', [QuizController::class, 'destroy'])->whereNumber('id');
Route::post('quizzes/{id}/questions', [QuizController::class, 'addQuestions'])->whereNumber('id'); // Ajout questions à un quiz

// Import / Export
Route::post('import/questions', [\App\Http\Controllers\ImportExportController::class, 'importQuestions']);
Route::get('export/questions', [\App\Http\Controllers\ImportExportController::class, 'exportQuestions']);

// Routes pour les ressources
Route::apiResource('difficulties', DifficultyController::class);
Route::apiResource('question-types', QuestionTypeController::class);
Route::apiResource('subjects', SubjectController::class);
Route::apiResource('questions', QuestionController::class);
Route::apiResource('answers', AnswerController::class);
Route::apiResource('users', UserController::class);

Route::post('auth/register', [UserController::class, 'store']);
Route::post('auth/login', [UserController::class, 'login']);

//groupe for auth.token
Route::middleware('auth.token')->group(function () {
    Route::post('questions', [QuestionController::class, 'store']);
    Route::put('questions/{id}', [QuestionController::class, 'update'])->whereNumber('id');
    Route::post('auth/logout', [UserController::class, 'logout']);
});

// Endpoint pour tester SOAP depuis l'interface web
Route::post('soap/test', [SoapDocumentationController::class, 'testMethod']);

// Route pour le serveur SOAP
Route::match(['get', 'post'], '/soap/import-export', [ImportExportController::class, 'server']);
