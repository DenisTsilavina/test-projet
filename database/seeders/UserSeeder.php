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
                'email' => 'superadmin@app.com',
                'name' => 'Super Admin',
                'password' => 'Admin123',
                'role' => UserRole::SUPER_ADMIN,
            ],

            /**
             *  CHANGEMENT : UserRole::VENDEUR -> UserRole::ADMINS
             * (rôle fusionné : administrateur/vendeur/production).
             */
            [
                'email' => 'vendeur@app.com',
                'name' => 'Vendeur Test',
                'password' => 'Vendeur123',
                'role' => UserRole::ADMINS,
            ],
            [
                'email' => 'client@app.com',
                'name' => 'Client Test',
                'password' => 'Client123',
                'role' => UserRole::CLIENT,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrNew(['email' => $userData['email']]);

            $user->name = $userData['name'];
            $user->password = $userData['password'];
            $user->role = $userData['role'];

            $user->save();
        }
    }
}
