<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table pivot entre Stock et Unite.
     * Chaque ligne = une unité rattachée à un stock avec sa quantité.
     */
    public function up(): void
    {
        Schema::create('stock_unite', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->cascadeOnDelete();   // supprime les lignes pivot si le stock est supprimé

            $table->foreignId('unite_id')
                ->constrained('unites')
                ->cascadeOnDelete();   // supprime les lignes pivot si l'unité est supprimée

            $table->decimal('quantite', 12, 2)->default(0); // quantité avec 2 décimales

            $table->timestamps();
            // Une unité ne peut apparaître qu'une seule fois par stock
            $table->unique(['stock_id', 'unite_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_unite');
    }
};
