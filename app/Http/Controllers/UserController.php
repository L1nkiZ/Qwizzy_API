<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\SecurityScheme(
     *     type="http",
     *     description="Entrer le token sous la forme: Bearer {votre_token}",
     *     name="Authorization",
     *     in="header",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     securityScheme="bearerAuth",
     * )
     */

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/user",
     *      security={{"bearerAuth":{}}},
     *      operationId="getUserAuth",
     *      tags={"User"},
     *      summary="Vérifier l'authentification de l'user",
     *      description="Retourne les informations de l'user authentifié (à condition de founir le bon token)",
     *      @OA\Response(
     *          response=200,
     *          description="Renvoie 'user' => 'ok' si le token est valide, sinon 'message' => 'Token invalide ou manquant'",
     *       )
     * )
     */
    public function index()
    {
        //
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
