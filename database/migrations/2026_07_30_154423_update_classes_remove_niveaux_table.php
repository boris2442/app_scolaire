<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    // La colonne niveau_id n'existe déjà plus dans classes
    // Donc on ne touche pas à cette table.

    Schema::table('parametres_academiques', function (Blueprint $table) {
        $table->dropForeign(['niveau_id']);
        $table->dropColumn('niveau_id');
    });

    Schema::dropIfExists('niveaux');
}
    public function down(): void
    {
        // En cas de retour en arrière (rollback)
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->timestamps();
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux');
        });
    }
};
