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
        Schema::table('inscriptions', function (Blueprint $table) {
           if (!Schema::hasColumn('inscriptions', 'classe_id')) {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->foreignId('classe_id')->nullable()->after('eleve_id')->constrained();
        });
    }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            //
        });
    }
};
