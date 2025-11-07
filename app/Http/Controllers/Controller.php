<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Qwizzy API Documentation",
 *      description="Documentation de l'API Qwizzy pour la gestion des quiz",
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 *
 * @OA\Tag(
 *     name="Difficulty",
 *     description="Endpoints pour gérer les niveaux de difficulté"
 * )
 *
 * @OA\Tag(
 *     name="Question",
 *     description="Endpoints pour gérer les questions du quiz"
 * )
 *
 * @OA\Tag(
 *     name="Answer",
 *     description="Endpoints pour gérer les réponses aux questions"
 * )
 * 
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Entrer le token (le token correct est 'token123') :",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
