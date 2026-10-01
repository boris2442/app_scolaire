<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {

            // On ne crée PAS niveau_id :
            // cette colonne ne fait plus partie du schéma actuel.

            if (!Schema::hasColumn('classes', 'salle_id')) {
                $table->foreignId('salle_id')
                    ->nullable();
            }

            if (!Schema::hasColumn('classes', 'annee_scolaire_id')) {
                $table->foreignId('annee_scolaire_id');
            }
        });

        // Ajouter les clés étrangères seulement si nécessaire
        if (Schema::hasColumn('classes', 'salle_id')) {
            try {
                Schema::table('classes', function (Blueprint $table) {
                    $table->foreign('salle_id')
                        ->references('id')
                        ->on('salles')
                        ->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // La contrainte existe peut-être déjà.
            }
        }

        if (Schema::hasColumn('classes', 'annee_scolaire_id')) {
            try {
                Schema::table('classes', function (Blueprint $table) {
                    $table->foreign('annee_scolaire_id')
                        ->references('id')
                        ->on('annees_scolaires')
                        ->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // La contrainte existe peut-être déjà.
            }
        }
    }

    public function down(): void
    {
        // Ne rien supprimer automatiquement.
    }
};
