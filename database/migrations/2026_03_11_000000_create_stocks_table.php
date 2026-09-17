<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            $table->string('name_stock')->unique();
            $table->dateTime('date_stock')->useCurrent();

            // Qui est responsable de ce stock (remplace l'ancien persn_stock en texte libre)
            $table->foreignId('responsable_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('unite_id')->constrained('unites');
            $table->decimal('quantite', 12, 2)->default(0);
            $table->integer('prix_achat_moyen')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
