<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('doctor_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->date('record_date');
            $table->text('diagnosis');
            $table->text('treatment');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('medical_records');
    }
};
