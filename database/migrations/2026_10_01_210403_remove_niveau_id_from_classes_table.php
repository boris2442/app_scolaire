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
        Schema::table('classes', function (Blueprint $table) {

            // Supprimer niveau_id : il existe déjà dans la base
            if (Schema::hasColumn('classes', 'niveau_id')) {
                $table->dropColumn('niveau_id');
            }

            if (! Schema::hasColumn('classes', 'salle_id')) {
                $table->foreignId('salle_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('classes', 'annee_scolaire_id')) {
                $table->foreignId('annee_scolaire_id')
                    ->constrained()
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void {}
};
