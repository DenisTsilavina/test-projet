<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unite;

class UniteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unites = [
            // --- MASSE (Base: kilogramme) ---
            ['name' => 'kilogramme', 'symbol' => 'kg', 'type' => 'masse', 'factor' => 1, 'is_base' => true],
            ['name' => 'gramme', 'symbol' => 'g', 'type' => 'masse', 'factor' => 0.001, 'is_base' => false],
            ['name' => 'sac (50kg)', 'symbol' => 'sac50', 'type' => 'masse', 'factor' => 50, 'is_base' => false],
            ['name' => 'sac (25kg)', 'symbol' => 'sac25', 'type' => 'masse', 'factor' => 25, 'is_base' => false],
            ['name' => 'sac (10kg)', 'symbol' => 'sac10', 'type' => 'masse', 'factor' => 10, 'is_base' => false],
            ['name' => 'sac (5kg)', 'symbol' => 'sac5', 'type' => 'masse', 'factor' => 5, 'is_base' => false],
            ['name' => 'sachet (500g)', 'symbol' => 'sachet500', 'type' => 'masse', 'factor' => 0.5, 'is_base' => false],

            // --- VOLUME (Base: litre) ---
            ['name' => 'litre', 'symbol' => 'L', 'type' => 'volume', 'factor' => 1, 'is_base' => true],
            ['name' => 'millilitre', 'symbol' => 'ml', 'type' => 'volume', 'factor' => 0.001, 'is_base' => false],
            ['name' => 'centilitre', 'symbol' => 'cl', 'type' => 'volume', 'factor' => 0.01, 'is_base' => false],
            ['name' => 'carton (12L)', 'symbol' => 'ctn12L', 'type' => 'volume', 'factor' => 12, 'is_base' => false],

            // --- UNITÉ / PIÈCE / CONDITIONNEMENT (Base: pièce) ---
            ['name' => 'pièce', 'symbol' => 'pcs', 'type' => 'unit', 'factor' => 1, 'is_base' => true],
            ['name' => 'douzaine', 'symbol' => 'dz', 'type' => 'unit', 'factor' => 12, 'is_base' => false],
            ['name' => 'carton (36 pcs)', 'symbol' => 'ctn36', 'type' => 'unit', 'factor' => 36, 'is_base' => false],
            ['name' => 'carton (24 pcs)', 'symbol' => 'ctn24', 'type' => 'unit', 'factor' => 24, 'is_base' => false],
            ['name' => 'carton (12 pcs)', 'symbol' => 'ctn12', 'type' => 'unit', 'factor' => 12, 'is_base' => false],
            ['name' => 'sachet (10 pcs)', 'symbol' => 'pkt10', 'type' => 'unit', 'factor' => 10, 'is_base' => false],
            ['name' => 'sachet (20 pcs)', 'symbol' => 'pkt20', 'type' => 'unit', 'factor' => 20, 'is_base' => false],
            ['name' => 'sachet (30 pcs)', 'symbol' => 'pkt30', 'type' => 'unit', 'factor' => 30, 'is_base' => false],

        ];

        foreach ($unites as $unite) {
            // updateOrCreate évite les doublons si vous relancez le seeder
            Unite::updateOrCreate(
                ['symbol' => $unite['symbol']],
                $unite
            );
        }

        // Optionnel : Supprimer les anciennes unités de type 'length' si elles existent déjà
        Unite::where('type', 'length')->delete();
    }
}
