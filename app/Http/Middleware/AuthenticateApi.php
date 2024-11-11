<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie que le token est présent dans les en-têtes
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        // Recherche si un utilisateur possède ce token (ici, pour simplifier, on suppose que c'est un token valide)
        // En production, il est important de stocker ces tokens dans une table, mais ici, nous faisons un check simple.
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // Attacher l'utilisateur authentifié à la requête
        $request->setUser($user);

        return $next($request);
    }
}
