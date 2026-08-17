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
        Schema::table('enseignants', function (Blueprint $table) {
            // Identification & Civil Status
            $table->string('grade')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('previous_position')->nullable();
            $table->string('previous_school')->nullable();

            // Administrative & Professional Information
            $table->string('appointment_document_number')->nullable();
            $table->date('appointment_date')->nullable();
            $table->date('service_assumption_date')->nullable(); // Date de prise / reprise de service
            $table->string('quality')->nullable(); // Ex: Enseignant, Surveillant Général...
            $table->string('diploma')->nullable(); // Ex: DIPES I, Master 2

            // Relation dynamique avec la table matieres
            $table->foreignId('matiere_id')->nullable()->constrained('matieres')->nullOnDelete();

            $table->date('public_service_first_date')->nullable(); // 1ère prise de service (Fonction Publique)
            $table->date('school_first_date')->nullable(); // 1ère prise de service (Établissement)

            // Coordinates & Service Interruption
            $table->text('interruption_reason')->nullable();
            $table->date('interruption_start_date')->nullable();
            $table->date('interruption_end_date')->nullable();
            $table->string('secondary_phone')->nullable(); // Contact 2
            $table->string('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enseignants', function (Blueprint $table) {
            $table->dropForeign(['matiere_id']);
            $table->dropColumn([
                'grade',
                'birth_date',
                'birth_place',
                'marital_status',
                'previous_position',
                'previous_school',
                'appointment_document_number',
                'appointment_date',
                'service_assumption_date',
                'quality',
                'diploma',
                'matiere_id',
                'public_service_first_date',
                'school_first_date',
                'interruption_reason',
                'interruption_start_date',
                'interruption_end_date',
                'secondary_phone',
                'address'
            ]);
        });
    }
};
