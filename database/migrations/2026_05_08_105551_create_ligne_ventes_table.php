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
        Schema::create('ligne_ventes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained('stocks');
            $table->foreignId('description_id')->constrained('descriptions');
            $table->foreignId('categorie_id')->constrained('sous_categories');
            $table->foreignId('unite_id')->constrained('unites');
            $table->decimal('quantite', 10, 3);
            $table->string('unite_symbol');
            $table->decimal('prix_unitaire', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('produit_nom');
            $table->string('sous_categorie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_ventes');
    }
};
