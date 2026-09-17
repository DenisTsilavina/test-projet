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

            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            $table->string('reference')->nullable()->unique();

            $table->dateTime('date_commande')->useCurrent();
            $table->date('date_besoin')->nullable();
            $table->date('date_livraison')->nullable();

            // CHANGEMENT : string plutôt qu'enum fermé, pour pouvoir
            // ajouter des statuts (ex: infos_demandees) sans migration
            // de type ALTER COLUMN à chaque fois.
            $table->string('statut')->default('en_attente');

            $table->text('remarque')->nullable();
            $table->integer('total')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
