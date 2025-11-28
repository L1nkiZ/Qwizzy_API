<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SoapDocumentationController;
use App\Http\Controllers\QuizSoapController;

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

// Page d'accueil avec choix de documentation
Route::get('/', [HomeController::class, 'index']);

// Documentation Swagger (REST API) - géré automatiquement par L5-Swagger
// Route: /api/documentation

// Serveur SOAP
Route::match(['get', 'post'], '/soap/quiz', [QuizSoapController::class, 'server']);

// Documentation SOAP interactive
Route::get('/soap/documentation', [SoapDocumentationController::class, 'index']);
