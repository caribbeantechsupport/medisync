<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalRecordPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return $user->user_role === 'doctor';
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->id === $medicalRecord->doctor_id;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->id === $medicalRecord->doctor_id;
    }
}
