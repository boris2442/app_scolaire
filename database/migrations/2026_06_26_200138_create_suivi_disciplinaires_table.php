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
        Schema::create('suivi_disciplinaires', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('trimestre_id')->constrained()->onDelete('cascade');
            $table->integer('retards')->default(0);
            $table->integer('absences')->default(0);
            $table->integer('suspensions')->default(0);
            $table->integer('avertissements')->default(0);
            $table->integer('blames')->default(0);
            $table->integer('exclusions')->default(0);





            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivi_disciplinaires');
    }
};
