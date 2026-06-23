<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;

class RoleService
{
    public function __construct(
        private readonly User $user
    ) {}

    public function role(): UserRole
    {
        // Plus besoin de UserRole::from(), $this->user->role est déjà l'Enum !
        return $this->user->role;
    }

    public function isAdmin(): bool
    {
        return $this->user->user_type === 'admin';
    }

    public function isClient():    bool { return !$this->isAdmin() && $this->role() === UserRole::CLIENT; }
    public function isVendeur():   bool { return !$this->isAdmin() && $this->role() === UserRole::VENDEUR; }
    public function isSuperAdmin():bool { return  $this->isAdmin() && $this->role() === UserRole::SUPER_ADMIN; }
    public function isAdmins():bool { return  $this->isAdmin() && $this->role() === UserRole::ADMINS; }


    public function dashboardRoute(): string
    {
        if ($this->isAdmin()) {
            return $this->isSuperAdmin()
                ? 'admin.super.dashboard'
                : 'admin.vente.dashboard';
        }

        return match($this->role()) {
            UserRole::VENDEUR   => 'vendeur.dashboard',
            default             => 'dashboard',
        };
    }
}
