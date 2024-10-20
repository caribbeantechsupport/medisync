<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'hospital_id',
        'record_date',
        'diagnosis',
        'treatment',
        'notes',
    ];

    protected $casts = [
        'record_date' => 'date',
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

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
