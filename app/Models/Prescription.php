<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'hospital_id',
        'medical_record_id',
        'prescription_date',
        'medication_name',
        'dosage',
        'frequency',
        'duration_days',
        'instructions',
    ];

    protected $casts = [
        'prescription_date' => 'date',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id')->where('user_role', 'patient');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')->where('user_role', 'doctor');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
