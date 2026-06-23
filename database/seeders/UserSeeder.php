<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email'    => 'superadmin@app.com',
                'name'     => 'Super Admin',
                'password' => 'Admin123', // Sera haché automatiquement par le modèle
                'role'     => UserRole::SUPER_ADMIN,
            ],
            [
                'email'    => 'vendeur@app.com',
                'name'     => 'Vendeur Test',
                'password' => 'Vendeur123',
                'role'     => UserRole::VENDEUR,
            ],
            [
                'email'    => 'client@app.com',
                'name'     => 'Client Test',
                'password' => 'Client123',
                'role'     => UserRole::CLIENT,
            ],
        ];

        foreach ($users as $userData) {
            // On cherche par l'email ou on instancie un nouvel utilisateur
            $user = User::firstOrNew(['email' => $userData['email']]);

            // Assignation des valeurs
            $user->name = $userData['name'];

            // Pas besoin de Hash::make() ici grâce au cast 'hashed' dans le modèle User
            $user->password = $userData['password'];

            // Laravel applique le cast Enum automatiquement ici
            $user->role = $userData['role'];

            $user->save();
        }
    }
}
