<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasRole;
use App\Enums\UserRole;
use App\Models\Vente;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRole;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,  // cast automatique vers Enum
    ];

    public function ventes()
    {
        return $this->hasMany(Vente::class, 'user_id');
    }

    public function roleService()
    {
        return new class($this->role) {
            private $roleEnum;

            public function __construct($roleEnum)
            {
                $this->roleEnum = $roleEnum;
            }

            public function role()
            {
                return $this->roleEnum; // Retourne l'instance complète de l'Enum (ex: UserRole::ADMINS)
            }
        };
    }
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMINS
            || $this->role === UserRole::SUPER_ADMIN;
    }
}
