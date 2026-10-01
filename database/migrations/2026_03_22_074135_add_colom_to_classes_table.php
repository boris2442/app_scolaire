<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('classes', function (Blueprint $table) {

        //     if (!Schema::hasColumn('classes', 'salle_id')) {
        //         $table->unsignedBigInteger('salle_id')->nullable();
        //     }

        //     if (!Schema::hasColumn('classes', 'annee_scolaire_id')) {
        //         $table->unsignedBigInteger('annee_scolaire_id');
        //     }
        // });

        // // Foreign key salle_id
        // if (Schema::hasColumn('classes', 'salle_id')) {
        //     Schema::table('classes', function (Blueprint $table) {
        //         $table->foreign('salle_id')
        //             ->references('id')
        //             ->on('salles')
        //             ->nullOnDelete();
        //     });
        // }

        // // Foreign key annee_scolaire_id
        // if (Schema::hasColumn('classes', 'annee_scolaire_id')) {
        //     Schema::table('classes', function (Blueprint $table) {
        //         $table->foreign('annee_scolaire_id')
        //             ->references('id')
        //             ->on('annees_scolaires')
        //             ->cascadeOnDelete();
        //     });
        // }
    }

    public function down(): void
    {
        //
    }
};
