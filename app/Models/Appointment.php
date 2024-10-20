<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'hospital_id',
        'appointment_datetime',
        'status',
        'reason_for_visit',
        'doctor_notes',
        'duration_minutes',
        'appointment_type',
        'patient_feedback',
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
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
}
