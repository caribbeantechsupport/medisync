<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_name',
        'first_name',
        'middle_name',
        'last_name',
        'national_identification',
        'email',
        'contact_number',
        'address',
        'country',
        'kin_name',
        'kin_contact_number',
        'kin_address',
        'user_role',
        'doctor_specialization',
        'hospital_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, $this->user_role === 'patient' ? 'patient_id' : 'doctor_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, $this->user_role === 'patient' ? 'patient_id' : 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, $this->user_role === 'patient' ? 'patient_id' : 'doctor_id');
    }


}
