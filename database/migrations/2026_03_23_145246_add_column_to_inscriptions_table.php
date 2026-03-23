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
        Schema::table('inscriptions', function (Blueprint $table) {
            // Ajouts stratégiques :
            $table->date('date_inscription')->default(now()); // Pour l'historique
            $table->string('statut')->default('actif'); // actif, abandon, exclu
            $table->string('numero_recu')->nullable(); // Utile si tu gères les frais plus tard

            // Sécurité : Un élève ne peut pas être inscrit deux fois dans la même année !
            $table->unique(['eleve_id', 'annee_scolaire_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            //
        });
    }
};
