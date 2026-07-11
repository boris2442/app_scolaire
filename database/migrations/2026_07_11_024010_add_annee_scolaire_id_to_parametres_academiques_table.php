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
        Schema::table('parametres_academiques', function (Blueprint $table) {
            // On ajoute la colonne. 
            // On met nullable() temporairement si tu as déjà des données dans la table.
            $table->foreignId('annee_scolaire_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parametres_academiques', function (Blueprint $table) {
            $table->dropForeign(['annee_scolaire_id']);
            $table->dropColumn('annee_scolaire_id');
        });
    }
};
