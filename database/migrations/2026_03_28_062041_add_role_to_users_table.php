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
        Schema::table('users', function (Blueprint $table) {
            // On crée une colonne de type ENUM
            // 'after' permet de placer la colonne juste après l'email dans PHPMyAdmin
            $table->enum('role', ['admin', 'enseignant', 'secretaire'])
                ->default('enseignant')
                ->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        // Si on annule la migration, on supprime la colonne role
        $table->dropColumn('role');
        });
    }
};
