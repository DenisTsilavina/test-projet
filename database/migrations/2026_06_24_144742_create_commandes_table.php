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

            // Relation polymorphique (Crée 'commandable_type' et 'commandable_id')
            // Permet de lier la commande à un Article, un Stock, etc. Nullable si "Autre".
            $table->nullableMorphs('commandable');

            // Si le choix est "Autre", on remplit ces champs
            $table->string('nom_commande')->nullable()->comment('Nom de l\'élément si hors catalogue');

            // Informations de la commande
            $table->integer('effectif')->default(1)->comment('Quantité ou effectif commandé');
            $table->date('date_besoin');

            // --- CHAMPS AJOUTÉS (Ce qu'il manquait) ---
            $table->string('statut')->default('en_attente')->comment('en_attente, approuve, livre, annule');
            $table->text('commentaires')->nullable()->comment('Pour spécifier un besoin particulier');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Qui a fait la commande');
            // ------------------------------------------

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
