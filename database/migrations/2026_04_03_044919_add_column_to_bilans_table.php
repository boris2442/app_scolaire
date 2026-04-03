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
        Schema::table('bilans', function (Blueprint $table) {
        
            $table->decimal('moyenne_premier', 4, 2)->nullable();
            $table->decimal('moyenne_dernier', 4, 2)->nullable();
            $table->decimal('moyenne_classe', 4, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilans', function (Blueprint $table) {
            //
        });
    }
};
