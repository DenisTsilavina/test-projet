<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up():void
    {

        Schema::table('ventes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['description_id']);
            $table->dropForeign(['categorie_id']);
            $table->dropColumn(['user_id', 'description_id', 'categorie_id', 'prix', 'effectif', 'prix_total']);
            $table->foreignId('vendeur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_anon_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('mode_paiement');
            $table->decimal('total_general', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['vendeur_id', 'client_anon_id', 'mode_paiement', 'total_general']);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('description_id')->constrained('descriptions');
            $table->foreignId('categorie_id')->constrained('sous_categories');
            $table->integer('prix');
            $table->integer('effectif');
            $table->integer('prix_total');
        });
    }
};
