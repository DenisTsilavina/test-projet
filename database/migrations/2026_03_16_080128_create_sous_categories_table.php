<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        // CHANGEMENT : 'stock_categorie' (texte libre) supprimé et
        // remplacé par les vraies colonnes de prix, utilisées partout
        // ailleurs (DescriptionController, SousCategory, Vente, les vues).
        Schema::create('sous_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('description_id')->constrained()->onDelete('cascade');
            $table->decimal('prix_achat', 12, 2)->nullable();
            $table->decimal('prix_vente', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sous_categories');
    }
};
