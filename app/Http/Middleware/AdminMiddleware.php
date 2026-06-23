<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\UserRole;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté et s'il est admin/super_admin
        if (auth()->check() && (
                auth()->user()->roleService()->role() === UserRole::ADMIN ||
                auth()->user()->roleService()->role() === UserRole::SUPER_ADMIN
            )) {
            return $next($request);
        }

        // Sinon, redirection vers l'accueil client avec un message d'erreur
        return redirect('/')->with('error', 'Accès refusé.');
    }
}
