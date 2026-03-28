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
    Schema::table('eleves', function (Blueprint $table) {
        // On ajoute la colonne photo après le prénom, elle peut être vide (nullable)
        $table->string('photo')->nullable()->after('prenom');
    });
}

public function down(): void
{
    Schema::table('eleves', function (Blueprint $table) {
        $table->dropColumn('photo');
    });
}
};
