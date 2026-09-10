<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Checkrole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowed = array_map([$this, 'resolveRole'], $roles);

        if (!in_array($user->role, $allowed, true)) {
            abort(403, 'Accès refusé.');
        }

        return $next($request);
    }

    private function resolveRole(string $role): UserRole
    {
        if (is_numeric($role)) {
            return UserRole::from((int) $role);
        }

        return match (strtolower($role)) {
            'client'  => UserRole::CLIENT,
            'super_admin', 'superadmin'  => UserRole::SUPER_ADMIN,
            'vendeur' => UserRole::VENDEUR,
            'admin', 'admins' => UserRole::ADMINS,
            default => throw new HttpException(
                500,
                "Rôle inconnu utilisé dans une route middleware('role:...') : \"{$role}\"."
            ),
        };
    }
}
