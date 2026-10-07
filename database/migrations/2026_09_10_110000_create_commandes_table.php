<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('code_commande')->unique();
            $table->string('nom_client');
            $table->string('telephone_client');
            $table->text('adresse_livraison')->nullable();
            $table->decimal('montant_total', 10, 2);
            $table->enum('statut', ['en_attente', 'validee', 'annulee', 'livree'])->default('en_attente');
            $table->timestamps();
        });

        Schema::create('commande_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('sous_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_lignes');
        Schema::dropIfExists('commandes');
    }
};
