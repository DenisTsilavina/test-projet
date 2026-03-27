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
        Schema::table('sous_categories', function (Blueprint $table) {
            $table->string('prix_achat')->after('stock_categorie')->nullable();
            $table->string('prix_vente')->after('prix_achat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sous_categories', function (Blueprint $table) {
            //
        });
    }
};
