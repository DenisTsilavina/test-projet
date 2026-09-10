<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        // Étape A : enlever le lien vers descriptions
        Schema::table('lignes_commandes', function (Blueprint $table) {
            $table->dropForeign(['description_id']);
        });

        // Étape B : supprimer la colonne description_id
        Schema::table('lignes_commandes', function (Blueprint $table) {
            $table->dropColumn('description_id');
        });

        // Étape C : ajouter produit_id + type_produit
        Schema::table('lignes_commandes', function (Blueprint $table) {
            $table->foreignId('produit_id')
                ->after('commande_id')
                ->constrained('produits');

            $table->enum('type_produit', ['stock', 'fini'])
                ->after('produit_id');
        });
    }

    public function down(): void
    {
        Schema::table('lignes_commandes', function (Blueprint $table) {
            $table->dropForeign(['produit_id']);
            $table->dropColumn(['produit_id', 'type_produit']);
        });

        Schema::table('lignes_commandes', function (Blueprint $table) {
            $table->foreignId('description_id')
                ->after('commande_id')
                ->constrained('descriptions');
        });
    }
};
