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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->cascadeOnDelete();
            $table->string('depart')->nullable();
            $table->string('arrivee')->nullable();
            $table->integer('frais_transport')->default(0);
            $table->string('transporteur')->nullable();
            $table->dateTime('date_depart')->nullable();
            $table->dateTime('date_arrivee')->nullable();
            $table->string('statut')->default('planifie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
