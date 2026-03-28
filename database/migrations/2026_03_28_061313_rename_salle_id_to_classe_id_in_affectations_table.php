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
        Schema::table('affectations', function (Blueprint $table) {
       // 1. On supprime l'ancienne contrainte de clé étrangère
            // Note: Le nom de la contrainte est souvent table_colonne_foreign
            $table->dropForeign(['salle_id']);

            // 2. On renomme la colonne
            $table->renameColumn('salle_id', 'classe_id');

            // 3. On recrée la contrainte sur la table 'classes'
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
 public function down(): void
    {
        Schema::table('affectations', function (Blueprint $table) {
            $table->dropForeign(['classe_id']);
            $table->renameColumn('classe_id', 'salle_id');
            $table->foreign('salle_id')->references('id')->on('salles'); // ou ton ancienne table
        });
    }
};
