<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

class Checkrole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $required = UserRole::from((int) $role);

        if ($user->role !== $required) {
            abort(403, 'Accès refusé.');
        }

        return $next($request);
    }
}
