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
        Schema::create('groupes_matieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Ex: "Matières Littéraires"
            $table->integer('ordre')->default(0); // 1, 2, 3 pour l'ordre sur le bulletin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupes_matieres');
    }
};
