<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends Controller
{
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
     * @OA\Post(
     *     path="/api/auth/register",
     *     operationId="storeUser",
     *     tags={"User"},
     *     summary="Créer un nouvel utilisateur",
     *     description="Enregistre un nouvel utilisateur et lui attribue un rôle (par défaut : Membre).",
     *     @OA\Parameter(
     *          name="username",
     *          description="Nom d'utilisateur",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="string", example="Guiguiz")
     *     ),
     *     @OA\Parameter(
     *          name="email",
     *          description="Adresse e-mail de l'utilisateur",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="string", format="email", example="guiguiz@exemple.com")
     *     ),
     *     @OA\Parameter(
     *          name="password",
     *          description="Mot de passe de l'utilisateur",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="string", format="password", example="motdepasse123")
     *    ),
     *    @OA\Parameter(
     *          name="role_id",
     *          description="ID du rôle à attribuer à l'utilisateur (1 pour 'Membre', 2 pour 'Rédacteur', 3 pour 'Administrateur')",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="integer", example=1)
     *    ),
     *    @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Utilisateur créé avec succès"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="name", type="string", example="Guiguiz"),
     *                 @OA\Property(property="email", type="string", example="guiguiz@exemple.com"),
     *                 @OA\Property(property="role_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreurs de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="object", example={"email":"The email field is required."})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Échec de la création de l'utilisateur")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages()
            ]);
        }

        //password hashing
        $hashedPassword = Hash::make($request->password);
        $user = new User();
        $user->name = $request->username;
        $user->email = $request->email;
        $user->password = $hashedPassword;
        $user->role_id = $request->role_id ?? 1; //Give role "Membre" by default, or the one provided by admin request
        $user->save();

        if ($user) {
            return response()->json([
                'error' => false,
                'message' => 'Utilisateur créé avec succès',
                'user' => $user
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => "Échec de la création de l'utilisateur"
            ]);
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
     * @OA\Post(
     *      path="/api/auth/login",
     *      operationId="getUserAuth",
     *      tags={"User"},
     *      summary="Vérifier l'authentification de l'user",
     *      description="Login de l'utilisateur dans le système",
     *      @OA\Parameter(
     *          name="username",
     *          description="Nom d'utilisateur",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="string", example="Guiguiz")
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="Adresse e-mail de l'utilisateur",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="string", format="email", example="guiguiz@exemple.com")
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="Mot de passe de l'utilisateur",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="string", format="password", example="motdepasse123")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Renvoie 'Authentification réussie' si le token est valide, sinon 'Échec de l'authentification : identifiants invalides'",
     *       )
     * )
     */
    public function login(Request $request)
    {
        //On récupère login et mdp
        $username = $request->input('username');
        $email = $request->input('email');
        $password = $request->input('password');

        //On cherche l'utilisateur par son email et son nom d'utilisateur
        $user = User::where('email', $email)
            ->orWhere('name', $username)
            ->first();
        if ($user && Hash::check($password, $user->password)) {

            //Create and Keep a JWT token

            $secret = config('jwt.secret', env('JWT_SECRET', config('app.key')));
            $ttlMinutes = config('jwt.ttl_minutes', 60 * 24 * 7); // default 7 days
            $issuedAt = time();
            $expire = $issuedAt + ($ttlMinutes * 60);

            $payload = [
                'sub' => $user->id,
                'email' => $user->email,
                'iat' => $issuedAt,
                'exp' => $expire,
            ];

            try {
                $token = JWT::encode($payload, $secret, 'HS256');
            } catch (\Exception $e) {
                return response()->json([
                    'error' => true,
                    'message' => 'Impossible de générer le token',
                    'details' => $e->getMessage(),
                ], 500);
            }

            return response()->json([
                'error' => false,
                'message' => 'Authentification réussie',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => date('c', $expire),
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => "Échec de l'authentification : identifiants invalides"
            ]);
        }
    }
}
