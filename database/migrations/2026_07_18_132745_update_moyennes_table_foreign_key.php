<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('moyennes', function (Blueprint $table) {
            // Supprime l'ancienne contrainte (le nom peut varier, vérifiez le votre)
            $table->dropForeign(['trimestre_id']);

            // Ajoute la nouvelle avec cascade
            $table->foreign('trimestre_id')
                ->references('id')
                ->on('trimestres')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
