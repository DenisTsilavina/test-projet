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
        Schema::create('unites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('description_id')->nullable()->constrained('descriptions')->onDelete('set null');
            $table->string('name');
            $table->string('symbol', 20)->unique();
            $table->string('type', 50);                        // 'length', 'mass', 'volume', etc.
            $table->decimal('factor', 20, 10)->default(1);     // facteur par rapport à l'unité de base
            $table->boolean('is_base')->default(false);        // true = unité de base du type
            $table->timestamps();
            $table->index('type');
            $table->index(['type', 'is_base']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unites');
    }
};
