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
        Schema::create('parametres_academiques', function (Blueprint $table) {
            $table->id();

            $table->string('cle'); // ex: 'moyenne_min'
            $table->decimal('valeur', 5, 2); // ex: 10.00

            // Liens optionnels pour la hiérarchie
            $table->foreignId('niveau_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('classe_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametres_academiques');
    }
};
