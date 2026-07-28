<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();

            // Clé étrangère vers la table articles (avec suppression en cascade)
            $table->foreignId('article_id')
                ->constrained()
                ->onDelete('cascade');

            // Clé étrangère vers la table descriptions
            $table->foreignId('description_id')
                ->constrained()
                ->onDelete('restrict'); // Empêche de supprimer une description utilisée

            // Clé étrangère vers la table unites
            $table->foreignId('unite_id')
                ->constrained()
                ->onDelete('restrict'); // Empêche de supprimer une unité utilisée

            // Quantité ou effectif requis pour cet ingrédient
            $table->decimal('effectif', 10, 2);

            // Prix de cet ingrédient
            $table->decimal('prix', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
