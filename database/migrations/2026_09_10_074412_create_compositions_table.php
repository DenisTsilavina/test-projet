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

        Schema::create('compositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_fini_id')->constrained('produits_finis')->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained('stocks');
            $table->decimal('quantite_necessaire', 12, 2);
            $table->string('unite')->nullable();
            $table->timestamps();
            // Un même stock ne peut apparaître qu'une fois dans la recette d'un produit fini.
            $table->unique(['produit_fini_id', 'stock_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compositions');
    }
};
