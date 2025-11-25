<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Firebase\JWT\JWT;


class TokenHelper
{
    public static function createTokens(){
        $member_user = \App\Models\User::where('id', 1)->first();
        $redactor_user = \App\Models\User::where('id', 2)->first();
        $admin_user = \App\Models\User::where('id', 3)->first();
        return [
            "member_token" => self::createToken($member_user),
            "redactor_token" => self::createToken($redactor_user),
            "admin_token" => self::createToken($admin_user),
        ];
    }


    public static function createToken($user)
    {
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
            return $token;
        } catch (\Exception $e) {
            throw new \Exception('Impossible de générer le token: ' . $e->getMessage());
        }
    }
}
