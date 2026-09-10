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
        Schema::create('mouvements_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->enum('type_mouvement', ['entree', 'sortie']);
            // D'où vient le mouvement : achat, vente, ou ajustement manuel.
            $table->enum('reference_type', ['achat', 'vente', 'ajustement']);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('quantite', 12, 2);
            $table->integer('prix_unitaire')->nullable();
            $table->dateTime('date_mouvement')->useCurrent();
            // CHANGEMENT : utilisateur_id -> users.id (qui a fait le mouvement)
            $table->foreignId('utilisateur_id')->constrained('users');
            $table->text('remarque')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvement_stocks');
    }
};
