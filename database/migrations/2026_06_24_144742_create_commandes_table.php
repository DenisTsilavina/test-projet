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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();

            // Client ayant créé la commande
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Référence générée automatiquement
            $table->string('reference')->unique();

            // Désignation de la commande
            $table->string('designation');

            // Quantité
            $table->unsignedInteger('quantite');

            // Montant total calculé automatiquement
            $table->decimal('montant', 12, 2)->default(0);

            // Statut
            $table->enum('statut', [
                'en_attente',
                'en_cours',
                'livre',
                'annule',
            ])->default('en_attente');

            // Note du client
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
