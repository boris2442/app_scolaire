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
    Schema::table('classes', function (Blueprint $table) {
        $table->dropColumn('niveau'); // On supprime la colonne gênante
    });
}

public function down()
{
    Schema::table('classes', function (Blueprint $table) {
        $table->string('niveau')->nullable(); // Au cas où on revient en arrière
    });
}
};
