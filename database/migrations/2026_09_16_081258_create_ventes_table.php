<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();

            // Le vendeur (User, admin/vendeur)
            $table->foreignId('user_id')->constrained('users');
            // Le client, optionnel (vente directe = souvent sans client identifié)
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            // CHANGEMENT : description_id + sous_categorie_id remplacés par
            // produit_id + type_produit, pour unifier Vente avec le système
            // Produit (stock/fini/compositions) déjà utilisé par Commande.
            $table->foreignId('produit_id')->constrained('produits');
            $table->enum('type_produit', ['stock', 'fini']);

            $table->integer('prix');
            $table->integer('effectif');
            $table->integer('prix_total');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
