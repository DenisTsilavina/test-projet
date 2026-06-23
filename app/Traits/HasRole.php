<?php

namespace App\Traits;

use App\Enums\UserRole;

trait HasRole
{
    /**
     * Vérifie si l'utilisateur est un Super Administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur est un Client.
     */
    public function isClient(): bool
    {
        return $this->role === UserRole::CLIENT;
    }

    /**
     * Vérifie si l'utilisateur est un Vendeur (Optionnel, si vous en avez besoin).
     */
    public function isVendeur(): bool
    {
        return $this->role === UserRole::VENDEUR;
    }
}
