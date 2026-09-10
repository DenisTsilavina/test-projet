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

        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            // client_id -> users.id (User avec role CLIENT)
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('nom_evenement');
            $table->string('lieu')->nullable();
            $table->date('date_evenement');
            $table->integer('prix_entree')->default(0);
            $table->text('description')->nullable();
            $table->string('organisateur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
