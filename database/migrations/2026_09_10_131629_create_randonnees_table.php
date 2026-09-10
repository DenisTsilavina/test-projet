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
        Schema::create('randonnees', function (Blueprint $table) {
            $table->id();
            // client_id -> users.id (User avec role CLIENT)
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('site_visite');
            $table->date('date_randonnee');
            $table->integer('frais')->default(0);
            $table->string('numero')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('randonnees');
    }
};
