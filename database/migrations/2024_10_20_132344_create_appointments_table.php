<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('doctor_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->dateTime('appointment_datetime');
            $table->string('status')->default('scheduled'); // e.g., scheduled, completed, cancelled
            $table->text('reason_for_visit')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->string('appointment_type')->default('in-person'); // e.g., in-person, video, phone
            $table->text('patient_feedback')->nullable();
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
        Schema::dropIfExists('appointments');
    }
};
