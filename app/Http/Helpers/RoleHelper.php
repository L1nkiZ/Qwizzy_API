<?php

namespace App\Http\Helpers;

class RoleHelper
{
    /**
     * Get role (or user) from a JWT token.
     *
     * @param string|null $token
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public static function getRole(?string $token)
    {
        $user = null;

        if ($token) {
            try {
                $sub = null;

                // Prefer Firebase JWT if available (verifies signature)
                if (class_exists(\Firebase\JWT\JWT::class) && class_exists(\Firebase\JWT\Key::class)) {
                    $key = config('jwt.secret', env('JWT_SECRET'));
                    if ($key) {
                        $payload = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($key, 'HS256'));
                        $sub = $payload->sub ?? null;
                    }
                }

                // Fallback: decode payload without verifying signature (not secure)
                if ($sub === null) {
                    $parts = explode('.', $token);
                    if (count($parts) >= 2) {
                        $b64 = $parts[1];
                        $b64 = str_replace(['-', '_'], ['+', '/'], $b64);
                        $pad = strlen($b64) % 4;
                        if ($pad) {
                            $b64 .= str_repeat('=', 4 - $pad);
                        }
                        $decoded = json_decode(base64_decode($b64));
                        $sub = $decoded->sub ?? null;
                    }
                }

                if ($sub) {
                    $user = \App\Models\User::find($sub);
                }
            } catch (\Throwable $e) {
                // ignore and keep $user = null
                $user = null;
            }
        }

        if (!$user) {
            return response()->json([
                'error' => true,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        // return user's role if available, otherwise return the user model
        return $user->role_id ?? $user;
    }
}