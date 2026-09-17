<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unites', function (Blueprint $table) {
            $table->id();
            $table->string('nom');        // ex: kg, litre, pièce, carton
            $table->string('symbole');    // ex: kg, L, pcs, ctn
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('stock_unite');
        Schema::dropIfExists('unites');
    }
};
