<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowed = array_map([$this, 'resolveRole'], $roles);
        /**
         * CHANGEMENT : au lieu d'un 403 brut, on redirige l'utilisateur
         *  vers SON propre espace (le dashboard qui correspond à son
         *  vrai rôle), avec un message explicatif. Un admin qui atterrit
         *  sur une page client (ou l'inverse) est réorienté en douceur.
         */
        if (!in_array($user->role, $allowed, true)) {
            return redirect()
                ->route($this->dashboardRouteFor($user->role))
                ->with('error', "Vous n'avez pas accès à cette page, vous avez été redirigé vers votre espace.");
        }

        return $next($request);
    }

    private function resolveRole(string $role): UserRole
    {
        if (is_numeric($role)) {
            return UserRole::from((int) $role);
        }

        return match (strtolower($role)) {
            'client' => UserRole::CLIENT,
            'super_admin', 'superadmin' => UserRole::SUPER_ADMIN,
            'admin', 'admins', 'vendeur' => UserRole::ADMINS,
            default => throw new HttpException(
                500,
                "Rôle inconnu utilisé dans une route middleware('role:...') : \"{$role}\"."
            ),
        };
    }

    /**
     * Le dashboard "maison" de chaque rôle, utilisé pour rediriger un
     * utilisateur qui n'a pas accès à la page qu'il vient de demander.
     */
    private function dashboardRouteFor(UserRole $role): string
    {
        return match ($role) {
            UserRole::CLIENT => 'client.dashboard',
            UserRole::SUPER_ADMIN, UserRole::ADMINS => 'admin.dashboard',
        };
    }
}
