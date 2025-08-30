<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Non authentifié.',
                'status' => 'error'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier si l'utilisateur a le rôle requis
        $user = Auth::user();
        if ($user->role !== $role) {
            return response()->json([
                'message' => 'Accès non autorisé. Rôle requis : ' . $role,
                'status' => 'error'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
