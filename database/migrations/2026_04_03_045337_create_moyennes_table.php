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
        Schema::create('moyennes', function (Blueprint $table) {
            $table->id();
            // Liens logiques
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained();
            $table->foreignId('sequence_id')->nullable()->constrained(); // Pour le calcul séquentiel
            $table->foreignId('trimestre_id')->nullable()->constrained(); // Pour le calcul trimestriel

            // Les chiffres
            $table->decimal('valeur', 4, 2); // La note sur 20 (ex: 15.50)
            $table->integer('coefficient');   // Le coef à l'instant T (très important pour l'historique)
            $table->decimal('total_points', 6, 2); // valeur * coefficient

            // Positionnement dans la classe pour cette matière
            $table->integer('rang');
            $table->decimal('moyenne_classe', 4, 2)->nullable();
            $table->decimal('min_classe', 4, 2)->nullable();
            $table->decimal('max_classe', 4, 2)->nullable();

            // Appréciation du prof pour CETTE matière
            $table->string('appreciation')->nullable(); // Ex: "Excellent", "Peut mieux faire"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moyennes');
    }
};
