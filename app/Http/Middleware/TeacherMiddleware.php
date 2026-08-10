<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
     public function handle(Request $request, Closure $next): Response
    {

        if (!Auth::check() || Auth::user()->role != UserRole::ENSEIGNANT) {
            return redirect()
                ->route('home')
                ->with('error', 'Accès restreint.');
        }

        return $next($request);
    }
}
