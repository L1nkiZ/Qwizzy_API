<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuthToken
{
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $bearer);

        $validToken = env('AUTH_TOKEN');

        if (! $token || $token !== $validToken) {
            return response()->json(['message' => 'Token invalide ou manquant'], 401);
        }

        return $next($request);
    }
}
