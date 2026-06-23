<?php

namespace Database\Seeders;

use App\Models\Unite;
use Illuminate\Database\Seeder;

class UniteSeeder extends Seeder
{
    public function run(): void
    {
        $unites = [
            ['nom' => 'Kilogramme', 'symbole' => 'kg'],
            ['nom' => 'Gramme',     'symbole' => 'g'],
            ['nom' => 'Litre',      'symbole' => 'L'],
            ['nom' => 'Millilitre', 'symbole' => 'mL'],
            ['nom' => 'Pièce',      'symbole' => 'pcs'],
            ['nom' => 'Carton',     'symbole' => 'ctn'],
            ['nom' => 'Sac',        'symbole' => 'sac'],
            ['nom' => 'Mètre',      'symbole' => 'm'],
        ];

        foreach ($unites as $unite) {
            Unite::firstOrCreate(['symbole' => $unite['symbole']], $unite);
        }
    }
}
