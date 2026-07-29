<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CenseurMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response

    // Vérifie si l'utilisateur est connecté
    {
        //dd('Je suis entré dans le middleware Censeur !');
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        // Affiche l'utilisateur connecté et son rôle, puis arrête l'exécution
        // dd([
        //     'is_logged' => Auth::check(),
        //     'user_name' => $user?->name,
        //     'user_role' => $user?->role,
        //     'role_type' => gettype($user?->role),
        // ]);
        // Adapte cette condition selon ton stockage en BD (Enum ou string)
        // Par exemple, si ton champ role est une string ou un Enum :
        $isCenseur = $user->role === UserRole::CENSEUR || $user->role === 'censeur';
        $isAdmin   = $user->role === UserRole::ADMIN   || $user->role === 'admin';

        if (!$isCenseur && !$isAdmin) {
            return redirect()
                ->route('home')
                ->with('error', 'Accès restreint.');
        }

        return $next($request);
    }
}
