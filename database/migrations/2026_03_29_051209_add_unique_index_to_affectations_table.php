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
   // On crée l'index unique pour éviter qu'une classe ait 2 profs pour la même matière la même année
        $table->unique(['matiere_id', 'classe_id', 'annee_scolaire_id'], 'attribution_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affectations', function (Blueprint $table) {
// En cas de rollback, on supprime l'index par son nom
        $table->dropUnique('attribution_unique');
        });
    }
};
