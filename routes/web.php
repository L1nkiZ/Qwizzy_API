<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// La route '/' est gérée par L5-Swagger pour afficher la documentation
Route::resource('question-type', App\Http\Controllers\QuestionTypeController::class);
Route::resource('subject', App\Http\Controllers\SubjectController::class);
Route::resource('difficulty', App\Http\Controllers\DifficultyController::class);
Route::resource('question', App\Http\Controllers\QuestionController::class);
