<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthToken
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', '');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'error' => true,
                'message' => 'Token invalide ou manquant'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = trim(str_replace('Bearer', '', $header));
        $secret = config('jwt.secret', env('JWT_SECRET', config('app.key')));

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json(['error' => true, 'message' => 'Token expiré'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => 'Token invalide', 'details' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        if (empty($decoded->sub)) {
            return response()->json(['error' => true, 'message' => 'Token invalide : subject manquant'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($decoded->sub);
        if (! $user) {
            return response()->json(['error' => true, 'message' => 'Utilisateur introuvable'], Response::HTTP_UNAUTHORIZED);
        }

        // Set the current user for the request
        Auth::setUser($user);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
