<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    // 1. Vérifier si l'utilisateur est connecté ET s'il est admin
    if (Auth::check() && Auth::user()->role->value === 'admin') {
        return $next($request);
    }

    // Si l'utilisateur n'est pas admin, on le renvoie à la page d'accueil
    // avec un message d'erreur flash (session)
    return redirect()->route('home')->with('error', 'Accès restreint. Vous devez être administrateur pour accéder à cet espace.');
}
}
