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
        Schema::table('moyennes', function (Blueprint $table) {
         // Index composite pour la récupération rapide du bulletin d'un élève
            $table->index(['sequence_id', 'inscription_id'], 'idx_moyennes_sequence_inscription');

            // Index composite pour le calcul rapide des statistiques par matière
            $table->index(['sequence_id', 'matiere_id'], 'idx_moyennes_sequence_matiere');

            // Index sur le trimestre si besoin de statistiques trimestrielles
            $table->index(['trimestre_id', 'inscription_id'], 'idx_moyennes_trimestre_inscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moyennes', function (Blueprint $table) {
         $table->dropIndex('idx_moyennes_sequence_inscription');
            $table->dropIndex('idx_moyennes_sequence_matiere');
            $table->dropIndex('idx_moyennes_trimestre_inscription');
        });
    }
};
