<?php

namespace App\Enums;

enum UserRole: int
{
    case CLIENT = 0;
    case SUPER_ADMIN = 1;
    case VENDEUR = 2;
    case ADMINS = 3;
    /**
     * Obtenir le label lisible pour l'humain.
     */
    public function label(): string
    {
        return match($this) {
            self::CLIENT => 'Client',
            self::SUPER_ADMIN => 'Super Administrateur',
            self::VENDEUR => 'Vendeur',
            self::ADMINS => 'Administrateur',
        };
    }
}
