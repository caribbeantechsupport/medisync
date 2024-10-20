<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('doctor_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->unsignedBigInteger('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->date('prescription_date');
            $table->string('medication_name');
            $table->string('dosage');
            $table->string('frequency');
            $table->integer('duration_days');
            $table->text('instructions')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add foreign key constraints with role checks
            $table->foreign('patient_id')
                ->references('id')
                ->on('users')
                ->where('user_role', '=', 'patient')
                ->onDelete('cascade');

            $table->foreign('doctor_id')
                ->references('id')
                ->on('users')
                ->where('user_role', '=', 'doctor')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
