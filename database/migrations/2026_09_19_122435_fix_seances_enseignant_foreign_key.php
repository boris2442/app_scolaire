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
        Schema::table('seances', function (Blueprint $table) {
           // Supprimer l'ancienne contrainte
            $table->dropForeign(['enseignant_id']);

            // Ajouter la nouvelle contrainte vers la table users
            $table->foreign('enseignant_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seances', function (Blueprint $table) {
          $table->dropForeign(['enseignant_id']);

            $table->foreign('enseignant_id')
                  ->references('id')
                  ->on('enseignants')
                  ->onDelete('cascade');
        });
    }
};
